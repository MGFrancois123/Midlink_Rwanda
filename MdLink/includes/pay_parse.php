<?php 
/*
* HDEV Payment Gateway 
* @email :  info@hev.rw
* @link : https://github.com/IZEREROGER
*
*/
/*
    Master payment controller
*/
if (!defined('hdev_payment')) {
	class hdev_payment
	{
		private static $api_id = null;
		private static $api_key = null;
		public static function 	api_key($value='')
		{
			self::$api_key = $value;
		}
		public static function api_id($value='')
		{
			self::$api_id = $value;
		}
		public static function pay($tel,$amount,$transaction_ref,$link=''){
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://payment.hdevtech.cloud/api_pay/api/'.self::$api_id.'/'.self::$api_key,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => array('ref'=>'pay','tel' => $tel,'tx_ref' => $transaction_ref,'amount' => $amount,'link'=>$link),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return json_decode($response, true);
		}
		public static function get_pay($tx_ref='')
		{
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://payment.hdevtech.cloud/api_pay/api/'.self::$api_id.'/'.self::$api_key,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => array('ref'=>'read','tx_ref' => $tx_ref),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return json_decode($response, true);
		}
	}

}
?>
