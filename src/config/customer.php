<?php

return [

    // PARAMETERS
    'send_mail' => false,
    'dependants' => false,
    'enable_test' => true,
    'fields' => [
        'password'=> true,
        'age'=> true,
        'shirt'=> false,
        'shirt_size'=> false,
        'invoice_data'=> true,
        'emergency_short'=> true,
        'emergency_long'=> false,
    ],
    'custom' => [
        'register'=> false,
        'register_rules'=> false,
        'after_register'=> false,
        'after_login'=> false,
        'after_succesful_payment'=> false,
    ],

];