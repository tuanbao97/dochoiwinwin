<?php

namespace App\Service;

interface SapoProductImporter
{
    /**
     * Import các sản phẩm Sapo (theo ID cache) vào catalog local.
     *
     * @param  array<int, int>  $sapoProductIds  Rỗng = import toàn bộ cache
     * @return array{
     *     products_ok: int,
     *     products_skip: int,
     *     products_error: int,
     *     variants_ok: int,
     *     images_ok: int,
     *     images_skip: int,
     *     images_error: int,
     *     deactivated: int,
     *     errors: array<int, string>
     * }
     */
    public function import(array $sapoProductIds = []): array;

    /**
     * @param  array<int, int>  $sapoProductIds
     */
    public function deactivateMissing(array $sapoProductIds): int;
}
