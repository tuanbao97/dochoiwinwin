<?php

namespace Database\Seeders;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryNSeeder extends Seeder
{
    public function run(): void
    {
        $arrCategoryN = [
            [
                'ID' => 1,
                'NAME' => 'Cẩm nang chọn đồ chơi',
                'SORT_ORDER' => 1,
                'TREE_LEVEL' => 0,
                'PARENT_ID' => null,
                'DESCRIPTION' => 'Hướng dẫn chọn đồ chơi an toàn, phù hợp độ tuổi và sở thích của bé.',
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
            ],
            [
                'ID' => 2,
                'NAME' => 'Ý tưởng quà tặng',
                'SORT_ORDER' => 2,
                'TREE_LEVEL' => 0,
                'PARENT_ID' => null,
                'DESCRIPTION' => 'Gợi ý đồ chơi và quà tặng cho sinh nhật, ngày lễ và các dịp đặc biệt.',
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
            ],
            [
                'ID' => 3,
                'NAME' => 'Tin cửa hàng',
                'SORT_ORDER' => 3,
                'TREE_LEVEL' => 0,
                'PARENT_ID' => null,
                'DESCRIPTION' => 'Khuyến mãi, sản phẩm mới và thông tin hoạt động cửa hàng Win Win.',
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
            ],
        ];

        foreach ($arrCategoryN as $categoryN) {
            DB::table('category_n')->updateOrInsert(
                ['ID' => $categoryN['ID']],
                $categoryN
            );
        }
    }
}
