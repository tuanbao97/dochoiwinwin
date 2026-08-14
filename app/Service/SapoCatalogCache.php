<?php

namespace App\Service;

interface SapoCatalogCache
{
    /**
     * Đồng bộ incremental từ last_fetch_api_sapo (lần đầu = full).
     *
     * @return array{
     *     synced: bool,
     *     mode: string,
     *     fetched: int,
     *     last_fetch_api_sapo: ?string,
     *     product_ids: array<int, int>,
     *     inactive_ids: array<int, int>
     * }
     */
    public function sync(?bool $forceFull = false, bool $ignoreMinInterval = false): array;

    /**
     * @return array<int, int>
     */
    public function allCachedProductIds(): array;

    /**
     * @param  array<int, int>  $collectionIds
     * @return array{products: array<int, array<string, mixed>>, count: int}
     */
    public function listProducts(
        int $page,
        int $perPage,
        string $productType,
        array $collectionIds = [],
        string $keyword = ''
    ): array;

    /**
     * @return array<string, mixed>|null
     */
    public function getProduct(int $id): ?array;
}
