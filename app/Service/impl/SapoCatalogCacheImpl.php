<?php

namespace App\Service\impl;

use App\Service\SapoCatalogCache;
use App\Service\SapoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SapoCatalogCacheImpl implements SapoCatalogCache
{
    private const SCOPE = 'products';

    private bool $syncedThisRequest = false;

    public function __construct(private SapoService $sapoService)
    {
    }

    public function sync(?bool $forceFull = false, bool $ignoreMinInterval = false): array
    {
        if (! $this->sapoService->isEnabled()) {
            return ['synced' => false, 'mode' => 'disabled', 'fetched' => 0, 'last_fetch_api_sapo' => null, 'product_ids' => [], 'inactive_ids' => []];
        }

        $nowUtc = Carbon::now('UTC');
        $state = $this->getState();
        $lastFetch = $state['LAST_FETCH_API_SAPO'] ?? null;
        $interval = max(0, (int) config('services.sapo.sync_min_interval', 60));

        if (! $forceFull && ! $ignoreMinInterval && $lastFetch && ! $this->cacheIsEmpty()) {
            $last = Carbon::parse($lastFetch, 'UTC');
            if ($last->diffInSeconds($nowUtc) < $interval) {
                return [
                    'synced' => false,
                    'mode' => 'fresh',
                    'fetched' => 0,
                    'last_fetch_api_sapo' => $this->formatUtc($last),
                    'product_ids' => [],
                    'inactive_ids' => [],
                ];
            }
        }

        $lockPath = storage_path('framework/cache/sapo-catalog-sync.lock');
        if (! is_dir(dirname($lockPath))) {
            mkdir(dirname($lockPath), 0777, true);
        }
        $lockHandle = fopen($lockPath, 'c');
        if ($lockHandle === false || ! flock($lockHandle, LOCK_EX | LOCK_NB)) {
            if (is_resource($lockHandle)) {
                fclose($lockHandle);
            }

            return [
                'synced' => false,
                'mode' => 'locked',
                'fetched' => 0,
                'last_fetch_api_sapo' => $lastFetch ? $this->formatUtc(Carbon::parse($lastFetch, 'UTC')) : null,
                'product_ids' => [],
                'inactive_ids' => [],
            ];
        }

        try {
            $state = $this->getState();
            $lastFetch = $state['LAST_FETCH_API_SAPO'] ?? null;
            $full = $forceFull || $lastFetch === null || $this->cacheIsEmpty();
            $fetchedAt = Carbon::now('UTC');

            if ($full) {
                $syncResult = $this->fullSync($fetchedAt);
                $mode = 'full';
            } else {
                $since = Carbon::parse($lastFetch, 'UTC')->subMinutes(2);
                $syncResult = $this->incrementalSync($since, $fetchedAt);
                $mode = 'incremental';
            }

            $this->saveState($fetchedAt);

            return [
                'synced' => true,
                'mode' => $mode,
                'fetched' => $syncResult['fetched'],
                'last_fetch_api_sapo' => $this->formatUtc($fetchedAt),
                'product_ids' => $syncResult['product_ids'],
                'inactive_ids' => $syncResult['inactive_ids'],
            ];
        } catch (Throwable $e) {
            Log::error('Sapo catalog sync failed', ['message' => $e->getMessage()]);
            throw $e;
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }

    public function listProducts(
        int $page,
        int $perPage,
        string $productType,
        array $collectionIds = [],
        string $keyword = ''
    ): array {
        $this->ensureSynced();

        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $collectionIds = array_values(array_unique(array_filter(array_map('intval', $collectionIds))));

        $q = DB::table('sapo_product_cache as p')
            ->where('p.STATUS', 'active');

        if ($productType !== '') {
            $q->where('p.PRODUCT_TYPE', $productType);
        }

        if ($keyword !== '') {
            $q->where(function ($inner) use ($keyword) {
                $inner->where('p.NAME', 'like', '%'.$keyword.'%')
                    ->orWhere('p.ALIAS', 'like', '%'.$keyword.'%');
            });
        }

        if ($collectionIds !== []) {
            $q->whereIn('p.ID', function ($sub) use ($collectionIds) {
                $sub->from('sapo_product_collection')
                    ->select('SAPO_PRODUCT_ID')
                    ->whereIn('SAPO_COLLECTION_ID', $collectionIds);
            });
        }

        $total = (int) (clone $q)->count('p.ID');

        $rows = $q->orderByDesc('p.MODIFIED_ON')
            ->orderByDesc('p.ID')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get(['p.PAYLOAD']);

        $products = [];
        foreach ($rows as $row) {
            $payload = json_decode((string) $row->PAYLOAD, true);
            if (is_array($payload)) {
                $products[] = $payload;
            }
        }

        return ['products' => $products, 'count' => $total];
    }

    public function getProduct(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $this->ensureSynced();

        $row = DB::table('sapo_product_cache')->where('ID', $id)->first();
        if ($row && $row->PAYLOAD) {
            $payload = json_decode((string) $row->PAYLOAD, true);
            if (is_array($payload)) {
                return $payload;
            }
        }

        $fresh = $this->sapoService->getProduct($id);
        if (is_array($fresh) && ! empty($fresh['id'])) {
            $this->upsertProducts([$fresh], Carbon::now('UTC'));
            $this->refreshCollectsForProducts([(int) $fresh['id']]);

            return $fresh;
        }

        return null;
    }

    private function ensureSynced(): void
    {
        if ($this->syncedThisRequest) {
            return;
        }
        $this->syncedThisRequest = true;
        try {
            $this->sync(false);
        } catch (Throwable $e) {
            if ($this->cacheIsEmpty()) {
                throw $e;
            }
        }
    }

    public function allCachedProductIds(): array
    {
        return DB::table('sapo_product_cache')
            ->orderBy('ID')
            ->pluck('ID')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array{fetched: int, product_ids: array<int, int>, inactive_ids: array<int, int>}
     */
    private function fullSync(Carbon $fetchedAt): array
    {
        $products = $this->fetchAllProducts(['status' => 'active']);
        $this->replaceProductCache($products, $fetchedAt);
        $this->refreshCollectsForCollections($this->menuCollectionIds());
        $ids = [];
        foreach ($products as $product) {
            $id = (int) ($product['id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return ['fetched' => count($products), 'product_ids' => $ids, 'inactive_ids' => []];
    }

    /**
     * @return array{fetched: int, product_ids: array<int, int>, inactive_ids: array<int, int>}
     */
    private function incrementalSync(Carbon $sinceUtc, Carbon $fetchedAt): array
    {
        $products = $this->fetchAllProducts([
            'modified_on_min' => $sinceUtc->format('Y-m-d H:i:s'),
        ]);

        $changedIds = [];
        $activeIds = [];
        $inactiveIds = [];
        $active = [];
        foreach ($products as $product) {
            if (! is_array($product)) {
                continue;
            }
            $id = (int) ($product['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $changedIds[] = $id;
            $status = (string) ($product['status'] ?? '');
            if ($status === 'active') {
                $active[] = $product;
                $activeIds[] = $id;
            } else {
                $inactiveIds[] = $id;
                DB::table('sapo_product_cache')->where('ID', $id)->delete();
                DB::table('sapo_product_collection')->where('SAPO_PRODUCT_ID', $id)->delete();
            }
        }

        if ($active !== []) {
            $this->upsertProducts($active, $fetchedAt);
        }

        if ($changedIds !== []) {
            if (count($changedIds) > 15) {
                $this->refreshCollectsForCollections($this->menuCollectionIds());
            } else {
                $this->refreshCollectsForProducts($changedIds);
            }
        }

        return [
            'fetched' => count($products),
            'product_ids' => $activeIds,
            'inactive_ids' => $inactiveIds,
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<int, array<string, mixed>>
     */
    private function fetchAllProducts(array $extra): array
    {
        $productType = trim((string) config('services.sapo.product_type', 'Đồ chơi'));
        $query = array_merge([
            'limit' => 250,
            'skip_count' => true,
        ], $extra);
        if ($productType !== '') {
            $query['product_type'] = $productType;
        }

        $all = [];
        $page = 1;
        do {
            $query['page'] = $page;
            $chunk = $this->sapoService->getProducts($query);
            $items = $chunk['products'] ?? [];
            foreach ($items as $item) {
                if (is_array($item)) {
                    $all[] = $item;
                }
            }
            $page++;
        } while (count($items) >= 250 && $page <= 20);

        return $all;
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     */
    private function replaceProductCache(array $products, Carbon $fetchedAt): void
    {
        DB::table('sapo_product_cache')->delete();
        $this->upsertProducts($products, $fetchedAt);
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     */
    private function upsertProducts(array $products, Carbon $fetchedAt): void
    {
        $now = $fetchedAt->format('Y-m-d H:i:s');
        foreach (array_chunk($products, 50) as $chunk) {
            $rows = [];
            foreach ($chunk as $product) {
                if (! is_array($product) || empty($product['id'])) {
                    continue;
                }
                $modified = $this->parseSapoUtc($product['modified_on'] ?? null);
                $rows[] = [
                    'ID' => (int) $product['id'],
                    'NAME' => (string) ($product['name'] ?? ''),
                    'ALIAS' => (string) ($product['alias'] ?? ''),
                    'PRODUCT_TYPE' => (string) ($product['product_type'] ?? ''),
                    'STATUS' => (string) ($product['status'] ?? ''),
                    'MODIFIED_ON' => $modified,
                    'PAYLOAD' => json_encode($product, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'LAST_FETCH_API_SAPO' => $now,
                    'CRT_DT' => $now,
                    'UPD_DT' => $now,
                ];
            }
            if ($rows === []) {
                continue;
            }
            DB::table('sapo_product_cache')->upsert(
                $rows,
                ['ID'],
                ['NAME', 'ALIAS', 'PRODUCT_TYPE', 'STATUS', 'MODIFIED_ON', 'PAYLOAD', 'LAST_FETCH_API_SAPO', 'UPD_DT']
            );
        }
    }

    /**
     * @param  array<int, int>  $collectionIds
     */
    private function refreshCollectsForCollections(array $collectionIds): void
    {
        $collectionIds = array_values(array_unique(array_filter($collectionIds)));
        if ($collectionIds === []) {
            return;
        }

        DB::table('sapo_product_collection')->whereIn('SAPO_COLLECTION_ID', $collectionIds)->delete();

        $rows = [];
        foreach ($collectionIds as $collectionId) {
            $page = 1;
            do {
                $data = $this->sapoService->getCollects([
                    'collection_id' => $collectionId,
                    'limit' => 250,
                    'page' => $page,
                ]);
                $items = $data;
                foreach ($items as $item) {
                    $pid = (int) ($item['product_id'] ?? 0);
                    if ($pid > 0) {
                        $rows[] = [
                            'SAPO_PRODUCT_ID' => $pid,
                            'SAPO_COLLECTION_ID' => $collectionId,
                        ];
                    }
                }
                $page++;
            } while (count($items) >= 250 && $page <= 20);
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('sapo_product_collection')->insertOrIgnore($chunk);
        }
    }

    /**
     * @param  array<int, int>  $productIds
     */
    private function refreshCollectsForProducts(array $productIds): void
    {
        $productIds = array_values(array_unique(array_filter($productIds)));
        if ($productIds === []) {
            return;
        }

        DB::table('sapo_product_collection')->whereIn('SAPO_PRODUCT_ID', $productIds)->delete();
        $allowed = $this->menuCollectionIds();
        $rows = [];
        foreach ($productIds as $productId) {
            $items = $this->sapoService->getCollects([
                'product_id' => $productId,
                'limit' => 250,
                'page' => 1,
            ]);
            foreach ($items as $item) {
                $cid = (int) ($item['collection_id'] ?? 0);
                if ($cid > 0 && ($allowed === [] || in_array($cid, $allowed, true))) {
                    $rows[] = [
                        'SAPO_PRODUCT_ID' => $productId,
                        'SAPO_COLLECTION_ID' => $cid,
                    ];
                }
            }
        }
        if ($rows !== []) {
            DB::table('sapo_product_collection')->insertOrIgnore($rows);
        }
    }

    /**
     * @return array<int, int>
     */
    private function menuCollectionIds(): array
    {
        $ids = DB::table('category_p')
            ->whereNotNull('ATTR2')
            ->where('ATTR2', '!=', '')
            ->pluck('ATTR2')
            ->map(static fn ($v) => (int) $v)
            ->filter(static fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        return $ids;
    }

    /**
     * @return array<string, mixed>
     */
    private function getState(): array
    {
        $row = DB::table('sapo_sync_state')->where('SCOPE', self::SCOPE)->first();

        return $row ? (array) $row : [];
    }

    private function saveState(Carbon $fetchedAt): void
    {
        $now = $fetchedAt->format('Y-m-d H:i:s');
        $exists = DB::table('sapo_sync_state')->where('SCOPE', self::SCOPE)->exists();
        if ($exists) {
            DB::table('sapo_sync_state')->where('SCOPE', self::SCOPE)->update([
                'LAST_FETCH_API_SAPO' => $now,
                'UPD_DT' => $now,
            ]);

            return;
        }
        DB::table('sapo_sync_state')->insert([
            'SCOPE' => self::SCOPE,
            'LAST_FETCH_API_SAPO' => $now,
            'CRT_DT' => $now,
            'UPD_DT' => $now,
        ]);
    }

    private function cacheIsEmpty(): bool
    {
        return ! DB::table('sapo_product_cache')->exists();
    }

    private function parseSapoUtc(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            return Carbon::parse((string) $value)->utc()->format('Y-m-d H:i:s');
        } catch (Throwable) {
            return null;
        }
    }

    private function formatUtc(Carbon $dt): string
    {
        return $dt->copy()->utc()->format('Y-m-d\TH:i:s\Z');
    }
}
