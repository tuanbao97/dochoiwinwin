<?php

namespace App\Enum;

enum SettingEnum : string
{
    case SETTING_COUNT_TOTAL_VIEW = "SETTING_COUNT_TOTAL_VIEW";
    case SETTING_COUNT_VIEW_DAY = "SETTING_COUNT_VIEW_DAY_%s";

    case SETTING_WEB = 'SETTING_WEB';
    case SETTING_DANH_SACH_HINH_ANH_BANNER_CHINH = 'SETTING_DANH_SACH_HINH_ANH_BANNER_CHINH';
    case SETTING_HOTLINE = 'SETTING_HOTLINE';
    case SETTING_TEN_CUA_HANG = 'SETTING_TEN_CUA_HANG';
    case SETTING_EMAIL = 'SETTING_EMAIL';
    case SETTING_MA_SO_THUE = 'SETTING_MA_SO_THUE';
    case SETTING_THOI_GIAN_LAM_VIEC = 'SETTING_THOI_GIAN_LAM_VIEC';
    case SETTING_MO_TA_CUA_HANG = 'SETTING_MO_TA_CUA_HANG';
    case SETTING_DIA_CHI_CUA_HANG = 'SETTING_DIA_CHI_CUA_HANG';
    case SETTING_DUONG_DAN_GG_MAP_CUA_HANG = 'SETTING_DUONG_DAN_GG_MAP_CUA_HANG';
    case SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG = 'SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG';
    case SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG = 'SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG';
    case SETTING_DUONG_DAN_SO_ZALO_CUA_HANG = 'SETTING_DUONG_DAN_SO_ZALO_CUA_HANG';
    case SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG = 'SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG';
    case SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG = 'SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG';
    case SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG = 'SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG';
    case SETTING_DUONG_DAN_TIKTOK_CUA_HANG = 'SETTING_DUONG_DAN_TIKTOK_CUA_HANG';
    case SETTING_DUONG_DAN_YOUTUBE_CUA_HANG = 'SETTING_DUONG_DAN_YOUTUBE_CUA_HANG';
    case SETTING_DUONG_DAN_SHOPPE_CUA_HANG = 'SETTING_DUONG_DAN_SHOPPE_CUA_HANG';
    case SETTING_DUONG_DAN_LAZADA_CUA_HANG = 'SETTING_DUONG_DAN_LAZADA_CUA_HANG';
    case SETTING_DUONG_DAN_TIKI_CUA_HANG = 'SETTING_DUONG_DAN_TIKI_CUA_HANG';
    case SETTING_DUONG_DAN_SENDO_CUA_HANG = 'SETTING_DUONG_DAN_SENDO_CUA_HANG';
    case SETTING_GIOI_THIEU_CUA_HANG = 'SETTING_GIOI_THIEU_CUA_HANG';
    case SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT = 'SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT';
    case SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET = 'SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET';
    case SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET_ONLY_TEXT = 'SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET_ONLY_TEXT';
    case SETTING_CHINH_SACH_BAO_HANH = 'SETTING_CHINH_SACH_BAO_HANH';
    case SETTING_CHINH_SACH_BAO_HANH_ONLY_TEXT = 'SETTING_CHINH_SACH_BAO_HANH_ONLY_TEXT';
    case SETTING_CHINH_SACH_THANH_TOAN = 'SETTING_CHINH_SACH_THANH_TOAN';
    case SETTING_CHINH_SACH_THANH_TOAN_ONLY_TEXT = 'SETTING_CHINH_SACH_THANH_TOAN_ONLY_TEXT';
    case SETTING_CAM_KET_BAN_HANG = 'SETTING_CAM_KET_BAN_HANG';
    case SETTING_CAM_KET_BAN_HANG_ONLY_TEXT = 'SETTING_CAM_KET_BAN_HANG_ONLY_TEXT';
    case SETTING_DANH_SACH_KHUYEN_MAI = 'SETTING_DANH_SACH_KHUYEN_MAI';
    case SETTING_DANH_SACH_KHUYEN_MAI_ONLY_TEXT = 'SETTING_DANH_SACH_KHUYEN_MAI_ONLY_TEXT';

    // Nếu bạn cần thêm thông tin, bạn có thể định nghĩa phương thức
    public function description() : ?string {
        return match ($this) {
            self::SETTING_COUNT_TOTAL_VIEW => 'Lượt xem web',
            self::SETTING_COUNT_VIEW_DAY => 'Lượt xem web ngày %s',

            self::SETTING_WEB => 'Setting trang web',
            self::SETTING_DANH_SACH_HINH_ANH_BANNER_CHINH => 'Setting danh sách hình ảnh banner chính',
            self::SETTING_HOTLINE => 'Setting hotline',
            self::SETTING_TEN_CUA_HANG => 'Setting tên cửa hàng',
            self::SETTING_EMAIL => 'Setting email cửa hàng',
            self::SETTING_MA_SO_THUE => 'Setting mã số thuế cửa hàng',
            self::SETTING_THOI_GIAN_LAM_VIEC => 'Setting thời gian làm việc',
            self::SETTING_MO_TA_CUA_HANG => 'Setting mô tả cửa hàng',
            self::SETTING_DIA_CHI_CUA_HANG => 'Setting địa chỉ cửa hàng',
            self::SETTING_DUONG_DAN_GG_MAP_CUA_HANG => 'Setting đường dẫn Google map cửa hàng',
            self::SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG => 'Setting đường dẫn chia sẻ Google Maps cửa hàng',
            self::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG => 'Setting đường dẫn Trang Zalo cửa hàng',
            self::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG => 'Setting đường dẫn Số Zalo cửa hàng',
            self::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG => 'Setting đường dẫn Trang Facebook cửa hàng',
            self::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG => 'Setting đường dẫn Facebook Messenger cửa hàng',
            self::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG => 'Setting đường dẫn Trang website',
            self::SETTING_DUONG_DAN_TIKTOK_CUA_HANG => 'Setting đường dẫn Tiktok cửa hàng',
            self::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG => 'Setting đường dẫn Youtube cửa hàng',
            self::SETTING_DUONG_DAN_SHOPPE_CUA_HANG => 'Setting đường dẫn Shoppe cửa hàng',
            self::SETTING_DUONG_DAN_LAZADA_CUA_HANG => 'Setting đường dẫn Lazada cửa hàng',
            self::SETTING_DUONG_DAN_TIKI_CUA_HANG => 'Setting đường dẫn Tiki cửa hàng',
            self::SETTING_DUONG_DAN_SENDO_CUA_HANG => 'Setting đường dẫn Sendo cửa hàng',
            self::SETTING_GIOI_THIEU_CUA_HANG => 'Setting giới thiệu cửa hàng',
            self::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT => 'Setting giới thiệu cửa hàng (Only text)',
            self::SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET => 'Setting chính sách khách hàng thân thiết',
            self::SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET_ONLY_TEXT => 'Setting chính sách khách hàng thân thiết (Only text)',
            self::SETTING_CHINH_SACH_BAO_HANH => 'Setting chính sách bảo hành',
            self::SETTING_CHINH_SACH_BAO_HANH_ONLY_TEXT => 'Setting chính sách bảo hành (Only text)',
            self::SETTING_CHINH_SACH_THANH_TOAN => 'Setting chính sách thanh toán',
            self::SETTING_CHINH_SACH_THANH_TOAN_ONLY_TEXT => 'Setting chính sách thanh toán (Only text)',
            self::SETTING_CAM_KET_BAN_HANG => 'Setting cam kết bán hàng',
            self::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT => 'Setting cam kết bán hàng (Only text)',
            self::SETTING_DANH_SACH_KHUYEN_MAI => 'Setting danh sách khuyến mãi',
            self::SETTING_DANH_SACH_KHUYEN_MAI_ONLY_TEXT => 'Setting danh sách khuyến mãi (Only text)',
        };
    }

    public function unit() : ?string {
        return match($this) {
            self::SETTING_COUNT_TOTAL_VIEW => 'Lượt xem',
            self::SETTING_COUNT_VIEW_DAY => 'Lượt xem',

            self::SETTING_WEB => null,
            self::SETTING_DANH_SACH_HINH_ANH_BANNER_CHINH => null,
            self::SETTING_HOTLINE => null,
            self::SETTING_TEN_CUA_HANG => null,
            self::SETTING_EMAIL => null,
            self::SETTING_MA_SO_THUE => null,
            self::SETTING_THOI_GIAN_LAM_VIEC => null,
            self::SETTING_MO_TA_CUA_HANG => null,
            self::SETTING_DIA_CHI_CUA_HANG => null,
            self::SETTING_DUONG_DAN_GG_MAP_CUA_HANG => null,
            self::SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG => null,
            self::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG => null,
            self::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG => null,
            self::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG => null,
            self::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG => null,
            self::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG => null,
            self::SETTING_DUONG_DAN_TIKTOK_CUA_HANG => null,
            self::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG => null,
            self::SETTING_DUONG_DAN_SHOPPE_CUA_HANG => null,
            self::SETTING_DUONG_DAN_LAZADA_CUA_HANG => null,
            self::SETTING_DUONG_DAN_TIKI_CUA_HANG => null,
            self::SETTING_DUONG_DAN_SENDO_CUA_HANG => null,
            self::SETTING_GIOI_THIEU_CUA_HANG => null,
            self::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT => null,
            self::SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET => null,
            self::SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET_ONLY_TEXT => null,
            self::SETTING_CHINH_SACH_BAO_HANH => null,
            self::SETTING_CHINH_SACH_BAO_HANH_ONLY_TEXT => null,
            self::SETTING_CHINH_SACH_THANH_TOAN => null,
            self::SETTING_CHINH_SACH_THANH_TOAN_ONLY_TEXT => null,
            self::SETTING_CAM_KET_BAN_HANG => null,
            self::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT => null,
            self::SETTING_DANH_SACH_KHUYEN_MAI => null,
            self::SETTING_DANH_SACH_KHUYEN_MAI_ONLY_TEXT => null,
        };
    }

    public function type() : ?string {
        return match($this) {
            self::SETTING_COUNT_TOTAL_VIEW => 'SETTING_COUNT_TOTAL_VIEW',
            self::SETTING_COUNT_VIEW_DAY => 'SETTING_COUNT_VIEW_DAY',

            self::SETTING_WEB => 'SETTING_WEB',
            self::SETTING_DANH_SACH_HINH_ANH_BANNER_CHINH => 'SETTING_WEB',
            self::SETTING_HOTLINE => 'SETTING_WEB',
            self::SETTING_TEN_CUA_HANG => 'SETTING_WEB',
            self::SETTING_EMAIL => 'SETTING_WEB',
            self::SETTING_MA_SO_THUE => 'SETTING_WEB',
            self::SETTING_THOI_GIAN_LAM_VIEC => 'SETTING_WEB',
            self::SETTING_MO_TA_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DIA_CHI_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_GG_MAP_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_SO_ZALO_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_TIKTOK_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_YOUTUBE_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_SHOPPE_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_LAZADA_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_TIKI_CUA_HANG => 'SETTING_WEB',
            self::SETTING_DUONG_DAN_SENDO_CUA_HANG => 'SETTING_WEB',
            self::SETTING_GIOI_THIEU_CUA_HANG => 'SETTING_WEB',
            self::SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT => 'SETTING_WEB',
             self::SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET => 'SETTING_WEB',
            self::SETTING_CHINH_SACH_KHACH_HANG_THAN_THIET_ONLY_TEXT => 'SETTING_WEB',
            self::SETTING_CHINH_SACH_BAO_HANH => 'SETTING_WEB',
            self::SETTING_CHINH_SACH_BAO_HANH_ONLY_TEXT => 'SETTING_WEB',
            self::SETTING_CHINH_SACH_THANH_TOAN => 'SETTING_WEB',
            self::SETTING_CHINH_SACH_THANH_TOAN_ONLY_TEXT => 'SETTING_WEB',
            self::SETTING_CAM_KET_BAN_HANG => 'SETTING_WEB',
            self::SETTING_CAM_KET_BAN_HANG_ONLY_TEXT => 'SETTING_WEB',
            self::SETTING_DANH_SACH_KHUYEN_MAI => 'SETTING_WEB',
            self::SETTING_DANH_SACH_KHUYEN_MAI_ONLY_TEXT => 'SETTING_WEB',
        };
    }

    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null;
    }
    
}
