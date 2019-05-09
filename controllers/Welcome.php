<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->library('twilio');
		$mobile_number = '919876543210';
		$message = "Welcome to Twilio.";
		$result = $this->sendSMS($mobile_number, $message);
		var_dump( $result);
		exit;


	}

	protected function sendSMS($mobile_number, $message) {
        $sms = $this->twilio->sms($message, $mobile_number);
        if (!isset($sms['error'])) {
            if (isset($sms['data']['sid'])) {
                return true;
            } else {
                log_message('error','1==='.json_encode($sms));
                return false;
            }
        } else {
            log_message('error','2==='.json_encode($sms));
            return false;
        }
    }
}
