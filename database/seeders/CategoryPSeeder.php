<?php

namespace Database\Seeders;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryPSeeder extends Seeder
{
    /**
     * Menu danh mục theo folder Drive:
     * G:\My Drive\Kinh doanh\Đồ chơi Win Win\Đồ chơi Win Win
     * 01–05 = danh mục cha (root). Con "B01. No Brands" chỉ là thư mục kho, không đưa vào menu.
     */
    public function run(): void
    {
        $arrCategoryP = [
            $this->row(2001, 'Đồ chơi điều khiển từ xa', 1, 0, null, sapoCollectionId: '4342369'),
            $this->row(2002, 'Đồ chơi lắp ghép', 2, 0, null, sapoCollectionId: '4342366'),
            $this->row(2003, 'Đồ chơi mô hình', 3, 0, null, sapoCollectionId: '4342367'),
            $this->row(2004, 'Đồ chơi nước', 4, 0, null, sapoCollectionId: '4342370'),
            $this->row(2005, 'Đồ chơi giáo dục', 5, 0, null, sapoCollectionId: '4342368'),
        ];

        foreach ($arrCategoryP as $categoryP) {
            $exists = DB::table('category_p')->where('ID', $categoryP['ID'])->exists();

            if (! $exists) {
                DB::table('category_p')->insert($categoryP);
            } else {
                DB::table('category_p')->where('ID', $categoryP['ID'])->update($categoryP);
            }
        }

        // Vô hiệu hóa danh mục cũ (trái cây / sữa / bất động sản…) không còn dùng
        $keepIds = collect($arrCategoryP)->pluck('ID')->all();
        DB::table('category_p')
            ->whereNotIn('ID', $keepIds)
            ->update([
                'STATUS' => AppConstant::STATUS_DELETED,
                'IS_ACTIVE' => false,
                'UPD_DT' => now(),
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
            ]);
    }

    private function row(int $id, string $name, int $sortOrder, int $treeLevel, ?int $parentId, ?string $externalUrl = null, ?string $sapoCollectionId = null): array
    {
        return [
            'ID' => $id,
            'NAME' => $name,
            'SORT_ORDER' => $sortOrder,
            'TREE_LEVEL' => $treeLevel,
            'PARENT_ID' => $parentId,
            'CRT_DT' => now(),
            'UPD_DT' => now(),
            'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
            'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
            'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
            'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
            'STATUS' => AppConstant::STATUS_USING,
            'IS_ACTIVE' => true,
            'ATTR1' => $externalUrl,
            'ATTR2' => $sapoCollectionId,
            'ATTR50' => 'UI-BACKEND/admin/san-pham/common/san-pham',
        ];
    }
}
