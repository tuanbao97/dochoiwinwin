<?php

namespace Database\Seeders;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use App\Enum\SettingEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $meta = [
            'CRT_DT' => $now,
            'UPD_DT' => $now,
            'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
            'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
            'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
            'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
            'STATUS' => AppConstant::STATUS_USING,
            'IS_ACTIVE' => true,
            'PARENT_CODE' => null,
        ];

        $gioiThieuHtml = <<<'HTML'
<p>🎁 <strong>ĐỒ CHƠI WIN WIN</strong></p>
<p>🚀 Chuyên đồ chơi chính hãng, chất lượng</p>
<p>🧸 Xe điều khiển • Máy bay RC • Đồ chơi giáo dục • Đồ chơi Búp bê • Đồ chơi vỉ giá rẻ</p>
<p>📦 Hàng sẵn kho – Giao nhanh toàn quốc</p>
<p>💬 Tư vấn tận tâm – Hỗ trợ nhiệt tình</p>
HTML;

        $camKetHtml = <<<'HTML'
<p>❤️ Win Win–Chọn đúng đồ chơi</p>
HTML;

        // Mỗi ý một dòng: giao diện render với white-space: pre-line
        $moTaCuaHang = <<<'TEXT'
🎁 ĐỒ CHƠI WIN WIN

🚀 Chuyên đồ chơi chính hãng, chất lượng
🧸 Xe điều khiển • Máy bay RC • Đồ chơi giáo dục • Đồ chơi Búp bê • Đồ chơi vỉ giá rẻ
📦 Hàng sẵn kho – Giao nhanh toàn quốc
💬 Tư vấn tận tâm – Hỗ trợ nhiệt tình
TEXT;

        $arrSetting = [
            [
                'CODE' => SettingEnum::SETTING_COUNT_TOTAL_VIEW->value,
                'NAME' => SettingEnum::SETTING_COUNT_TOTAL_VIEW->description(),
                'TYPE' => SettingEnum::SETTING_COUNT_TOTAL_VIEW->type(),
                'DESCRIPTION' => SettingEnum::SETTING_COUNT_TOTAL_VIEW->description(),
                'UNIT' => SettingEnum::SETTING_COUNT_TOTAL_VIEW->unit(),
                'VALUE' => '0',
                'ORDER' => null,
            ],
            [
                'CODE' => SettingEnum::SETTING_TEN_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_TEN_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_TEN_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_TEN_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_TEN_CUA_HANG->unit(),
                'VALUE' => 'Đồ Chơi Win Win',
                'ORDER' => 1,
            ],
            [
                'CODE' => SettingEnum::SETTING_EMAIL->value,
                'NAME' => SettingEnum::SETTING_EMAIL->description(),
                'TYPE' => SettingEnum::SETTING_EMAIL->type(),
                'DESCRIPTION' => SettingEnum::SETTING_EMAIL->description(),
                'UNIT' => SettingEnum::SETTING_EMAIL->unit(),
                'VALUE' => 'dochoiwinwin@gmail.com',
                'ORDER' => 2,
            ],
            [
                'CODE' => SettingEnum::SETTING_MA_SO_THUE->value,
                'NAME' => SettingEnum::SETTING_MA_SO_THUE->description(),
                'TYPE' => SettingEnum::SETTING_MA_SO_THUE->type(),
                'DESCRIPTION' => SettingEnum::SETTING_MA_SO_THUE->description(),
                'UNIT' => SettingEnum::SETTING_MA_SO_THUE->unit(),
                'VALUE' => '048097002277',
                'ORDER' => 2,
            ],
            [
                'CODE' => SettingEnum::SETTING_THOI_GIAN_LAM_VIEC->value,
                'NAME' => SettingEnum::SETTING_THOI_GIAN_LAM_VIEC->description(),
                'TYPE' => SettingEnum::SETTING_THOI_GIAN_LAM_VIEC->type(),
                'DESCRIPTION' => SettingEnum::SETTING_THOI_GIAN_LAM_VIEC->description(),
                'UNIT' => SettingEnum::SETTING_THOI_GIAN_LAM_VIEC->unit(),
                'VALUE' => '6:00 – 21:30 (Tất cả các ngày trong tuần)',
                'ORDER' => 3,
            ],
            [
                'CODE' => SettingEnum::SETTING_MO_TA_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_MO_TA_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_MO_TA_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_MO_TA_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_MO_TA_CUA_HANG->unit(),
                'VALUE' => $moTaCuaHang,
                'ORDER' => 4,
            ],
            [
                'CODE' => SettingEnum::SETTING_DIA_CHI_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DIA_CHI_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DIA_CHI_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DIA_CHI_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DIA_CHI_CUA_HANG->unit(),
                'VALUE' => 'Đường DT605, xã Hòa Tiến, Đà Nẵng (đối diện Trường Tiểu học số 2 Hòa Tiến)',
                'ORDER' => 5,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_GG_MAP_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_GG_MAP_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_GG_MAP_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_GG_MAP_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_GG_MAP_CUA_HANG->unit(),
                'VALUE' => 'https://www.google.com/maps?q=Đường+DT605,+Hòa+Tiến,+Đà+Nẵng&output=embed',
                'ORDER' => 6,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG->unit(),
                'VALUE' => 'https://zalo.me/',
                'ORDER' => 7,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG->unit(),
                'VALUE' => 'https://zalo.me/',
                'ORDER' => 8,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG->unit(),
                'VALUE' => 'https://www.facebook.com/dochoi.winwin/',
                'ORDER' => 9,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG->unit(),
                'VALUE' => 'https://m.me/dochoi.winwin',
                'ORDER' => 10,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG->unit(),
                'VALUE' => 'https://dochoiwinwin.com',
                'ORDER' => 11,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_TIKTOK_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_TIKTOK_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_TIKTOK_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_TIKTOK_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_TIKTOK_CUA_HANG->unit(),
                'VALUE' => 'https://www.tiktok.com/',
                'ORDER' => 12,
            ],
            [
                'CODE' => SettingEnum::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG->unit(),
                'VALUE' => 'https://www.youtube.com/channel/UCh5jj4Q-vqQMdEhwRZAeZMA',
                'ORDER' => 13,
            ],
            [
                'CODE' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG->value,
                'NAME' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG->description(),
                'TYPE' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG->description(),
                'UNIT' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG->unit(),
                'VALUE' => $gioiThieuHtml,
                'ORDER' => 14,
            ],
            [
                'CODE' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT->value,
                'NAME' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT->description(),
                'TYPE' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT->type(),
                'DESCRIPTION' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT->description(),
                'UNIT' => SettingEnum::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT->unit(),
                'VALUE' => strip_tags(str_replace(['</p>', '<br>', '<br/>'], ["\n", "\n", "\n"], $gioiThieuHtml)),
                'ORDER' => 15,
            ],
            [
                'CODE' => SettingEnum::SETTING_CAM_KET_BAN_HANG->value,
                'NAME' => SettingEnum::SETTING_CAM_KET_BAN_HANG->description(),
                'TYPE' => SettingEnum::SETTING_CAM_KET_BAN_HANG->type(),
                'DESCRIPTION' => SettingEnum::SETTING_CAM_KET_BAN_HANG->description(),
                'UNIT' => SettingEnum::SETTING_CAM_KET_BAN_HANG->unit(),
                'VALUE' => $camKetHtml,
                'ORDER' => 16,
            ],
            [
                'CODE' => SettingEnum::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT->value,
                'NAME' => SettingEnum::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT->description(),
                'TYPE' => SettingEnum::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT->type(),
                'DESCRIPTION' => SettingEnum::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT->description(),
                'UNIT' => SettingEnum::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT->unit(),
                'VALUE' => strip_tags($camKetHtml),
                'ORDER' => 17,
            ],
            // Hotline — VALUE = LOAI|SDT|TEN_CHU_SDT (ORDER 1 = số chính trên UI)
            [
                'CODE' => 'SETTING_HOTLINE_TYPE_HOTLINE_0',
                'NAME' => 'SETTING_HOTLINE_TYPE_HOTLINE',
                'TYPE' => SettingEnum::SETTING_HOTLINE->type(),
                'DESCRIPTION' => SettingEnum::SETTING_HOTLINE->description(),
                'UNIT' => SettingEnum::SETTING_HOTLINE->unit(),
                'VALUE' => 'HOTLINE|0905090910|Win Win',
                'ORDER' => 1,
            ],
            [
                'CODE' => 'SETTING_HOTLINE_TYPE_HOTLINE_1',
                'NAME' => 'SETTING_HOTLINE_TYPE_HOTLINE',
                'TYPE' => SettingEnum::SETTING_HOTLINE->type(),
                'DESCRIPTION' => SettingEnum::SETTING_HOTLINE->description(),
                'UNIT' => SettingEnum::SETTING_HOTLINE->unit(),
                'VALUE' => 'HOTLINE|0905454775|Win Win',
                'ORDER' => 2,
            ],
        ];

        foreach ($arrSetting as $setting) {
            $row = array_merge($meta, $setting);
            $exists = DB::table('setting')->where('CODE', $row['CODE'])->exists();
            if (!$exists) {
                DB::table('setting')->insert($row);
                continue;
            }

            // Không ghi đè URL MXH nếu admin đã nhập giá trị cụ thể (tránh seed làm mất link thật).
            // Website là dữ liệu thương hiệu nên luôn đồng bộ theo Đồ Chơi Win Win.
            $socialCodes = [
                SettingEnum::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG->value,
                SettingEnum::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG->value,
                SettingEnum::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG->value,
                SettingEnum::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG->value,
                SettingEnum::SETTING_DUONG_DAN_TIKTOK_CUA_HANG->value,
                SettingEnum::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG->value,
            ];
            if (in_array($row['CODE'], $socialCodes, true)) {
                $current = trim((string) DB::table('setting')->where('CODE', $row['CODE'])->value('VALUE'));
                $placeholders = [
                    '',
                    'https://www.facebook.com/',
                    'https://www.facebook.com',
                    'https://zalo.me/',
                    'https://zalo.me',
                    'https://m.me/',
                    'https://m.me',
                    'https://www.tiktok.com/',
                    'https://www.tiktok.com',
                ];
                if ($current !== '' && !in_array($current, $placeholders, true)) {
                    continue;
                }
            }

            DB::table('setting')->where('CODE', $row['CODE'])->update($row);
        }

        if (function_exists('evictCacheDataFrontEnd')) {
            evictCacheDataFrontEnd();
        }
    }
}
