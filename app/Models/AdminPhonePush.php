<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPhonePush extends Model
{
    protected $table = 'admin_phone_push';

    protected $primaryKey = 'ID';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'CRT_DT';

    const UPDATED_AT = 'UPD_DT';

    protected $guarded = [];
}
