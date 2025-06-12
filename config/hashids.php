<?php
// Tạo key mã hóa
return [
    'encryption' => [
        'salt' => env('APP_KEY', 'your-default-salt'),
        'length' => 8,
        'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    ],
];
