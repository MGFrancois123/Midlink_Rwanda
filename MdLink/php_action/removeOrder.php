<?php 	

require_once 'core.php';


$valid['success'] = array('success' => false, 'messages' => array());

//$orderId = $_POST['orderId'];
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($orderId) { 

 $sql = "UPDATE orders SET delete_status = 1 WHERE id = {$orderId}";

 $orderItem = "DELETE FROM order_item WHERE lastid = {$orderId}";

 if($connect->query($sql) === TRUE && $connect->query($orderItem) === TRUE) {
 	$valid['success'] = true;
	$valid['messages'] = "Successfully Removed";
	header('location:../Order.php');		
 } else {
 	$valid['success'] = false;
 	$valid['messages'] = "Error while removing the order";
 }
 
 $connect->close();

 echo json_encode($valid);
 
} // /if $_POST