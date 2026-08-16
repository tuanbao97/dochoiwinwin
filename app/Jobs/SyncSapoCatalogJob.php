<?php

namespace App\Jobs;

use App\Models\Product;
use App\Service\SapoCatalogCache;
use App\Service\SapoProductImporter;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncSapoCatalogJob
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public bool $forceFull = false,
        public bool $ignoreMinInterval = false,
        public bool $importOnly = false,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(SapoCatalogCache $cache, SapoProductImporter $importer): array
    {
        @set_time_limit(0);

        try {
            if ($this->importOnly) {
                $result = [
                    'synced' => false,
                    'mode' => 'import-only',
                    'fetched' => 0,
                    'last_fetch_api_sapo' => null,
                    'product_ids' => $cache->allCachedProductIds(),
                    'inactive_ids' => [],
                ];
            } else {
                $result = $cache->sync($this->forceFull, $this->ignoreMinInterval);
            }

            $mode = $result['mode'] ?? '';
            if (in_array($mode, ['disabled', 'locked'], true)) {
                return $result;
            }

            $productIds = $result['product_ids'] ?? [];
            $inactiveIds = $result['inactive_ids'] ?? [];

            if ($productIds === [] && ($mode === 'full' || $mode === 'import-only' || $this->shouldBootstrapImport())) {
                $productIds = $cache->allCachedProductIds();
            }

            // Sapo không có thay đổi nhưng bảng product còn thiếu (DB mới deploy, import lỗi giữa chừng…)
            if ($productIds === []) {
                $productIds = $this->missingFromLocal($cache);
            }

            $import = ['products_ok' => 0, 'products_skip' => 0, 'products_error' => 0, 'variants_ok' => 0, 'images_ok' => 0, 'images_skip' => 0, 'images_error' => 0, 'deactivated' => 0, 'errors' => []];
            if ($productIds !== []) {
                $import = $importer->import($productIds);
            }

            if ($mode === 'full' || $mode === 'import-only') {
                $keep = $productIds !== [] ? $productIds : $cache->allCachedProductIds();
                $missing = Product::query()
                    ->whereNotNull('SAPO_ID')
                    ->when($keep !== [], fn ($q) => $q->whereNotIn('SAPO_ID', $keep))
                    ->pluck('SAPO_ID')
                    ->map(static fn ($id) => (int) $id)
                    ->all();
                $import['deactivated'] = $importer->deactivateMissing($missing);
            } elseif ($inactiveIds !== []) {
                $import['deactivated'] = $importer->deactivateMissing($inactiveIds);
            }

            // Cache public API sống 24h → phải xoá khi catalog vừa đổi, nếu không storefront giữ dữ liệu cũ
            if ($import['products_ok'] > 0 || $import['deactivated'] > 0) {
                evictCacheDataFrontEnd();
            }

            $result['import'] = $import;
            Log::info('Sapo catalog job', [
                'mode' => $mode,
                'fetched' => $result['fetched'] ?? 0,
                'import' => $import,
            ]);

            return $result;
        } catch (Throwable $e) {
            Log::error('Sapo catalog job failed', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function shouldBootstrapImport(): bool
    {
        return ! Product::query()->whereNotNull('SAPO_ID')->exists();
    }

    /**
     * @return array<int, int>
     */
    private function missingFromLocal(SapoCatalogCache $cache): array
    {
        $cached = $cache->allCachedProductIds();
        if ($cached === []) {
            return [];
        }

        $existing = Product::query()
            ->whereIn('SAPO_ID', $cached)
            ->pluck('SAPO_ID')
            ->map(static fn ($id) => (int) $id)
            ->all();

        return array_values(array_diff($cached, $existing));
    }
}
