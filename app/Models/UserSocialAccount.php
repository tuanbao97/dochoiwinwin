<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSocialAccount extends Model
{
    protected $table = 'user_social_account';

    protected $primaryKey = 'ID';

    protected $guarded = [];

    const CREATED_AT = 'CRT_DT';

    const UPDATED_AT = 'UPD_DT';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'USER_ID', 'ID');
    }
}
