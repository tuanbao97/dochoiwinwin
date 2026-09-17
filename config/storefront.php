<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Phí vận chuyển
    |--------------------------------------------------------------------------
    |
    | Đồng giá cho đơn giao tận nơi. Đơn nhận tại cửa hàng (pickup) luôn
    | miễn phí ship — phí được tính lại ở máy chủ, client không tự đặt.
    |
    */
    'shipping_fee' => (int) env('STOREFRONT_SHIPPING_FEE', 30000),
];
