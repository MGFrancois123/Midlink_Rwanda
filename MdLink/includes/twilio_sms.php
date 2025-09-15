<?php
/*
 * Alternative SMS Provider: Twilio
 * This can be used as a backup if HDEV doesn't work
 * Sign up at: https://www.twilio.com
 */

class TwilioSMS {
    private $accountSid;
    private $authToken;
    private $fromNumber;
    
    public function __construct($accountSid, $authToken, $fromNumber) {
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->fromNumber = $fromNumber;
    }
    
    public function sendSMS($to, $message) {
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";
        
        $data = array(
            'From' => $this->fromNumber,
            'To' => $to,
            'Body' => $message
        );
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_USERPWD => $this->accountSid . ':' . $this->authToken,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            )
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        return json_decode($response);
    }
}

/*
 * Alternative SMS Provider: Africa's Talking
 * Popular in Africa, might work better for Rwanda
 * Sign up at: https://africastalking.com
 */
class AfricasTalkingSMS {
    private $apiKey;
    private $username;
    
    public function __construct($username, $apiKey) {
        $this->username = $username;
        $this->apiKey = $apiKey;
    }
    
    public function sendSMS($to, $message, $from = null) {
        $url = 'https://api.africastalking.com/version1/messaging';
        
        $data = array(
            'username' => $this->username,
            'to' => $to,
            'message' => $message
        );
        
        if ($from) {
            $data['from'] = $from;
        }
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                'apiKey: ' . $this->apiKey,
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            )
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        return json_decode($response);
    }
}

/*
 * Simple SMS Provider Switcher
 * Allows easy switching between SMS providers
 */
class SMSManager {
    private $provider;
    private $config;
    
    public function __construct($provider = 'hdev', $config = []) {
        $this->provider = $provider;
        $this->config = $config;
    }
    
    public function sendSMS($to, $message, $from = null) {
        switch ($this->provider) {
            case 'twilio':
                if (!isset($this->config['account_sid']) || !isset($this->config['auth_token']) || !isset($this->config['from_number'])) {
                    throw new Exception('Twilio configuration missing');
                }
                $twilio = new TwilioSMS($this->config['account_sid'], $this->config['auth_token'], $this->config['from_number']);
                return $twilio->sendSMS($to, $message);
                
            case 'africastalking':
                if (!isset($this->config['username']) || !isset($this->config['api_key'])) {
                    throw new Exception('Africa\'s Talking configuration missing');
                }
                $at = new AfricasTalkingSMS($this->config['username'], $this->config['api_key']);
                return $at->sendSMS($to, $message, $from);
                
            case 'hdev':
            default:
                // Use existing HDEV implementation
                require_once 'Sms_parse.php';
                require_once 'sms_config.php';
                hdev_sms::api_id(HDEV_SMS_API_ID);
                hdev_sms::api_key(HDEV_SMS_API_KEY);
                return hdev_sms::send($from ?: DEFAULT_SENDER_ID, $to, $message);
        }
    }
}
?>


