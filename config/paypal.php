<?php
/**
 * PayPal Setting & API Credentials
 * Created by Raza Mehdi <srmk@outlook.com>.
 */

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'),
    'client_id'    => env('PAYPAL_CLIENT_ID', 'sandbox'),
    'client_secret'    => env('PAYPAL_SECRET_ID', 'sandbox'), 
];
