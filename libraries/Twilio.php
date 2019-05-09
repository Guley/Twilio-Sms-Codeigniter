<?php

class Twilio {

    private $config;

    public function __construct() {
        $this->config = config_item('twilio');
    }

    public function sms($message = '', $to = '') {

        /* Check if number is valid */
        if($this->lookups($to)){
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_USERAGENT => $this->config['application'],
                CURLOPT_HTTPHEADER => [
                    'Accept-Charset: utf-8',
                    'Content-Type: application/x-www-form-urlencoded',
                ],
                CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/' . $this->config['account_sid'] . '/Messages.json',
                CURLOPT_SSL_VERIFYPEER => $this->config['ssl'],
                CURLE_ABORTED_BY_CALLBACK => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLE_FTP_WEIRD_227_FORMAT => -1,
                CURLOPT_HEADER => TRUE,
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => http_build_query([
                    'From' => $this->config['from'],
                    'To' => $to,
                    'Body' => $message,
                ]),
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => $this->config['api_key'] . ':' . $this->config['api_secret'],
                CURLE_TOO_MANY_REDIRECTS => TRUE,
            ]);

            if ($response = curl_exec($curl))
            {
                $parts = explode("\r\n\r\n", $response, 3);
                
                list($head, $body) = ($parts[0] == 'HTTP/1.1 100 Continue') ? [$parts[1], $parts[2]] : [$parts[0], $parts[1]];
                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                
                $header_lines = explode("\r\n", $head);
                array_shift($header_lines);
                
                foreach ($header_lines as $line) {
                    list($key, $value) = explode(":", $line, 2);
                    $headers[$key] = trim($value);
                }
                
                if ($body && !empty($body)) {
                    $body = json_decode($body);
                    if (is_object($body)) {
                        $body = (array) $body;
                    }
                }
                
                return [
                    'status' => $status,
                    'headers' => $headers,
                    'data' => $body
                ];
            }
            else
            {
                return ['error' => curl_error($curl)];
            }

        } else {
            return  ['error' => 'Phone number might incorrect!'];
        }
    }

    /* Success Response from loopup service.
        stdClass Object
        (
            [caller_name] => 
            [country_code] => IN
            [phone_number] => +919814569120
            [national_format] => 098145 69120
            [carrier] => 
            [add_ons] => 
            [url] => https://lookups.twilio.com/v1/PhoneNumbers/+919814569120
        )

        Error Response from loopup service.
        stdClass Object
        (
            [code] => 20404
            [message] => The requested resource /PhoneNumbers/912343 was not found
            [more_info] => https://www.twilio.com/docs/errors/20404
            [status] => 404
        )
    */
    public function lookups($mobile_number){
        $curl = curl_init();

        curl_setopt_array($curl, [
            //?Type=carrier&Type=caller-name
            CURLOPT_URL => 'https://lookups.twilio.com/v1/PhoneNumbers/'.$mobile_number,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->config['api_key'] . ':' . $this->config['api_secret'],
        ]);

        if ($response = curl_exec($curl))
        {
            $lookUpResponse = json_decode($response);
            if(!empty($lookUpResponse) && isset($lookUpResponse->national_format)){
                return true;
            }
        }
        else
        {
            //return ['error' => curl_error($curl)];
        }
        return false;
    }
}
