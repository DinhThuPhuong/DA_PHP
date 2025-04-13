<?php
return [
    'vnp_TmnCode'     => env('VNPAY_TMNCODE'),
    'vnp_HashSecret'  => env('VNPAY_HASHSECRET'),
    'vnp_Url'         => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'vnp_ReturnUrl'   => env('VNP_RETURN_URL'),
];