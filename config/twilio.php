<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['twilio'] = [

    'account_sid' => 'xxxxxxxxxxxxxxxxxxxxx',
	'api_key' 	 => 'xxxxxxxxxxxxxxxx',
    'api_secret' => 'xxxxxxxxxxxxxxxxxx',  
	  
	  //Test Phone Number
    'from' => '+111111111111',
    
    // Extra configuration
    'application' => 'Example',
    'ssl' => FALSE,
];
