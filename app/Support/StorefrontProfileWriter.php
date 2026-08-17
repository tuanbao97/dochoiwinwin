<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Models\User;
use App\Models\UserProfile;

class StorefrontProfileWriter
{
    /**
     * Auth::user() carries virtual attributes injected by the OAuth middleware,
     * so persist through explicit column updates instead of saving that model.
     */
    public static function save(
        int $userId,
        string $fullName,
        string $email,
        string $phone,
        string $address
    ): void {
        $fullName = trim($fullName);
        $actorName = $fullName !== '' ? $fullName : $email;
        $phone = trim((string) preg_replace('/[\s.\-]/', '', $phone));
        $address = trim($address);

        if ($fullName !== '') {
            User::query()->whereKey($userId)->update([
                'FULL_NAME' => $fullName,
                'UPD_ID' => $userId,
                'UPD_NAME' => $actorName,
                'UPD_DT' => now(),
            ]);
        }

        $profile = UserProfile::query()
            ->where('USER_ID', $userId)
            ->where('IS_DEFAULT', true)
            ->where('STATUS', AppConstant::STATUS_USING)
            ->first() ?? new UserProfile();

        $profile->USER_ID = $userId;
        if ($phone !== '') {
            $profile->MOBILE = $phone;
        }
        if ($address !== '') {
            $profile->ADDRESS = $address;
        }
        $profile->IS_DEFAULT = true;
        $profile->STATUS = AppConstant::STATUS_USING;
        $profile->IS_ACTIVE = true;
        $profile->CRT_ID = $profile->CRT_ID ?? $userId;
        $profile->CRT_NAME = $profile->CRT_NAME ?? $actorName;
        $profile->UPD_ID = $userId;
        $profile->UPD_NAME = $actorName;
        $profile->save();
    }
}
