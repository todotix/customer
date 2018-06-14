<?php

return [

	// PARAMETERS
	'send_mail' => true,
    'dependants' => false,
    'fields' => [
        'password'=> true,
        'age'=> true,
        'shirt'=> false,
        'shirt_size'=> false,
        'emergency_short'=> true,
        'emergency_long'=> false,
    ],
    'custom' => [
        // After login true configurarlo en solunes
        'register'=> false,
        'after_register'=> false,
        'after_login'=> false,
        'after_succesful_payment'=> false,
    ],

];