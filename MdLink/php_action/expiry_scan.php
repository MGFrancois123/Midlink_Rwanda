<?php
require_once 'db_connect.php';
@require_once dirname(__DIR__).'/includes/Sms_parse.php';
@require_once dirname(__DIR__).'/includes/sms_config.php';
header('Content-Type: application/json');

if (!isset($connect) || !($connect instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection not available']);
    exit;
}

// Create expiry_alerts table if missing
$createSql = "CREATE TABLE IF NOT EXISTS expiry_alerts (
  alert_id INT(11) NOT NULL AUTO_INCREMENT,
  medicine_id INT(11) NOT NULL,
  pharmacy_id INT(11) DEFAULT NULL,
  name VARCHAR(191) NOT NULL,
  category_name VARCHAR(150) DEFAULT NULL,
  expires_on DATE NOT NULL,
  days_left INT(11) DEFAULT NULL,
  severity ENUM('normal','urgent','expired') NOT NULL,
  notified_super TINYINT(1) NOT NULL DEFAULT 0,
  notified_pharma TINYINT(1) NOT NULL DEFAULT 0,
  notified_finance TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (alert_id),
  UNIQUE KEY uniq_medicine_expiry (medicine_id, expires_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
@$connect->query($createSql);

$action = isset($_GET['action']) ? trim($_GET['action']) : 'run';

function respond($data, int $code = 200) { http_response_code($code); echo json_encode($data); exit; }

if ($action === 'run') {
    // Find medicines expiring within 30 days (including already expired)
    $sql = "SELECT m.medicine_id, m.name, m.expiry_date, m.stock_quantity,
                   p.pharmacy_id, p.name AS pharmacy_name,
                   c.category_name
            FROM medicines m
            LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
            LEFT JOIN category c ON m.category_id = c.category_id
            WHERE m.expiry_date IS NOT NULL
              AND m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ".((int) (defined('EXPIRY_ALERT_THRESHOLD_DAYS')?EXPIRY_ALERT_THRESHOLD_DAYS:30))." DAY)";
    $q = $connect->query($sql);
    if (!$q) { respond(['success'=>false,'message'=>$connect->error], 500); }

    $inserted = 0; $updated = 0;
    while ($r = $q->fetch_assoc()) {
        $expires = $r['expiry_date'];
        $daysLeft = (int) floor((strtotime($expires) - time()) / (60*60*24));
        $severity = 'normal';
        if ($daysLeft < 0) $severity = 'expired';
        elseif ($daysLeft <= (int)(defined('EXPIRY_URGENT_DAYS')?EXPIRY_URGENT_DAYS:7)) $severity = 'urgent';
        else $severity = 'normal';

        $stmt = $connect->prepare("INSERT INTO expiry_alerts (medicine_id, pharmacy_id, name, category_name, expires_on, days_left, severity)
                                   VALUES (?,?,?,?,?,?,?)
                                   ON DUPLICATE KEY UPDATE pharmacy_id=VALUES(pharmacy_id), name=VALUES(name), category_name=VALUES(category_name), days_left=VALUES(days_left), severity=VALUES(severity)");
        if ($stmt) {
            $stmt->bind_param('iisssis', $r['medicine_id'], $r['pharmacy_id'], $r['name'], $r['category_name'], $expires, $daysLeft, $severity);
            $ok = $stmt->execute();
            if ($ok) {
                if ($stmt->affected_rows === 1) { $inserted++; } else { $updated++; }
            }
        }
    }

    // Optional cleanup: remove alerts that are now beyond 30 days ahead (no longer relevant)
    @$connect->query("DELETE FROM expiry_alerts WHERE expires_on > DATE_ADD(CURDATE(), INTERVAL 30 DAY)");

    // Optional: basic SMS/Email hooks (configured below)
    $alertsNewQ = @$connect->query("SELECT name, days_left, severity FROM expiry_alerts WHERE DATE(created_at)=CURDATE() AND TIME(created_at) >= (NOW() - INTERVAL 1 HOUR)");
    $newAlerts = $alertsNewQ ? $alertsNewQ->num_rows : 0;

    // HDEV SMS: send notification to Super Admin
    // NOTE: format phone numbers in E.164. Given 0786980814 (RW) -> +250786980814
    hdev_sms::api_id(SMS_API_ID);
    hdev_sms::api_key(SMS_API_KEY);
    $smsResult = null;
    if ($newAlerts > 0) {
      $message = $newAlerts." new expiry alert(s) detected in the last scan. Open Expiry Alerts to review.";
      $senderCandidates = isset($SMS_SENDER_CANDIDATES) && is_array($SMS_SENDER_CANDIDATES) ? $SMS_SENDER_CANDIDATES : array('MDLINK-RW');
      $recipients = isset($SMS_ALERT_RECIPIENTS) && is_array($SMS_ALERT_RECIPIENTS) ? $SMS_ALERT_RECIPIENTS : array(SMS_SUPER_ADMIN_PHONE);
      foreach ($recipients as $to) {
        foreach ($senderCandidates as $sid) {
          $smsResult = @hdev_sms::send($sid, $to, $message);
          $logLine = date('Y-m-d H:i:s') . " SMS try to $to sender='".$sid."' => " . json_encode($smsResult) . "\n";
          @file_put_contents(dirname(__DIR__).'/logs_sms.txt', $logLine, FILE_APPEND);
          if (is_object($smsResult) && isset($smsResult->status) && strtolower((string)$smsResult->status) === 'success') {
            break;
          }
        }
      }
    }

    respond(['success'=>true,'inserted'=>$inserted,'updated'=>$updated,'new_alerts'=>$newAlerts,'sms'=>$smsResult]);
}

respond(['error'=>'Invalid action'], 400);

