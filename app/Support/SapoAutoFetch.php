<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Models\Setting;

class SapoAutoFetch
{
    public const CODE = 'SETTING_SAPO_AUTO_FETCH';

    public static function enabled(): bool
    {
        $value = Setting::query()->where('CODE', self::CODE)->value('VALUE');

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'on', 'yes'], true);
    }

    public static function setEnabled(bool $enabled): void
    {
        Setting::query()->updateOrCreate(
            ['CODE' => self::CODE],
            [
                'NAME' => 'Tự fetch sản phẩm Sapo',
                'TYPE' => 'SETTING_SYSTEM',
                'VALUE' => $enabled ? '1' : '0',
                'UNIT' => 'boolean',
                'DESCRIPTION' => 'Công tắc cho tiến trình nền: bật thì mỗi 60 giây fetch phần sản phẩm Sapo vừa đổi.',
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
                'UPD_NAME' => 'ADMIN',
            ]
        );

        if (! app()->runningUnitTests()) {
            BackgroundTasks::ensureRunning();
        }
    }
}
