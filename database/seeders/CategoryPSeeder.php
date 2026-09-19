<?php

namespace Database\Seeders;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryPSeeder extends Seeder
{
    /**
     * Menu danh mục cửa hàng — map ATTR2 = Sapo custom_collection.id
     * Upsert keep-list; ẩn danh mục toy thừa (2001–2099 ngoài keep).
     */
    public function run(): void
    {
        $arrCategoryP = [
            $this->row(2001, 'Đồ chơi điều khiển', 1, 0, null, sapoCollectionId: '4342369'),
            $this->row(2016, 'Đồ chơi bỏ pin tự động', 2, 0, null, sapoCollectionId: '4347486'),
            $this->row(2002, 'Đồ chơi lắp ghép', 3, 0, null, sapoCollectionId: '4342366'),
            $this->row(2003, 'Đồ chơi mô hình', 4, 0, null, sapoCollectionId: '4342367'),
            $this->row(2007, 'Đồ chơi vui nhộn', 5, 0, null, sapoCollectionId: '4346809'),
            $this->row(2005, 'Đồ chơi giáo dục', 6, 0, null, sapoCollectionId: '4342368'),
            $this->row(2019, 'Đồ chơi nhập vai & chủ đề', 7, 0, null, sapoCollectionId: '4357553'),
            $this->row(2006, 'Đồ chơi bé gái', 8, 0, null, sapoCollectionId: '4346781'),
            $this->row(2011, 'Đồ chơi vận động', 9, 0, null, sapoCollectionId: '4346844'),
            $this->row(2004, 'Đồ chơi nước', 10, 0, null, sapoCollectionId: '4342370'),

            $this->row(2009, 'Xe điều khiển', 1, 1, 2001, sapoCollectionId: '4346833'),
            $this->row(2008, 'Máy bay điều khiển', 2, 1, 2001, sapoCollectionId: '4346832'),
            $this->row(2010, 'Đồ chơi điều khiển khác', 3, 1, 2001, sapoCollectionId: '4346834'),

            $this->row(2012, 'Lắp ghép siêu xe, robot', 1, 1, 2002, sapoCollectionId: '4346861'),
            $this->row(2013, 'Lắp ghép siêu anh hùng', 2, 1, 2002, sapoCollectionId: '4346862'),
            $this->row(2014, 'Lắp ghép con vật', 3, 1, 2002, sapoCollectionId: '4346863'),
            $this->row(2015, 'Lắp ghép, xếp hình tổng hợp', 4, 1, 2002, sapoCollectionId: '4346864'),

            // —— Nhập vai & chủ đề ——
            $this->row(2042, 'Đồ chơi nhà bếp', 1, 1, 2019, sapoCollectionId: '4357565'),
            $this->row(2044, 'Đồ chơi cứu hỏa', 2, 1, 2019, sapoCollectionId: '4357698'),
            $this->row(2045, 'Đồ chơi bác sĩ', 3, 1, 2019, sapoCollectionId: '4357699'),
            $this->row(2046, 'Đồ chơi cảnh sát', 4, 1, 2019, sapoCollectionId: '4357700'),
            $this->row(2047, 'Đồ chơi hóa trang', 5, 1, 2019, sapoCollectionId: '4357701'),
            $this->row(2048, 'Đồ chơi zombie thây ma', 6, 1, 2019, sapoCollectionId: '4357713'),
            $this->row(2049, 'Đồ chơi bắn súng', 7, 1, 2019, sapoCollectionId: '4357715'),
        ];

        foreach ($arrCategoryP as $categoryP) {
            $exists = DB::table('category_p')->where('ID', $categoryP['ID'])->exists();

            if (! $exists) {
                DB::table('category_p')->insert($categoryP);
            } else {
                DB::table('category_p')->where('ID', $categoryP['ID'])->update($categoryP);
            }
        }

        $keepIds = collect($arrCategoryP)->pluck('ID')->all();
        DB::table('category_p')
            ->whereBetween('ID', [2001, 2099])
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
