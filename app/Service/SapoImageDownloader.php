<?php

namespace App\Service;

use App\Models\DocumentStorage;

interface SapoImageDownloader
{
    /**
     * Tải ảnh Sapo về local (tái sử dụng theo SAPO_IMAGE_ID / SOURCE_URL).
     *
     * @param  array<string, mixed>  $image
     */
    public function download(array $image): ?DocumentStorage;
}
