<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Phí vận chuyển
    |--------------------------------------------------------------------------
    |
    | Tạm thời áp dụng đồng giá 30.000đ cho mọi đơn hàng. Giá trị này chỉ
    | được đọc ở máy chủ; client không thể tự thay đổi phí khi đặt hàng.
    |
    */
    'shipping_fee' => (int) env('STOREFRONT_SHIPPING_FEE', 30000),
];
