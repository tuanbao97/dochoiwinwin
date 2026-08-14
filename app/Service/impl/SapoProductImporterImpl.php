<?php

namespace App\Service\impl;

use App\Enum\AppConstant;
use App\Models\DocumentStorage;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductDocumentStorage;
use App\Models\ProductVariant;
use App\Service\SapoImageDownloader;
use App\Service\SapoProductImporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SapoProductImporterImpl implements SapoProductImporter
{
    private const ACTOR_ID = 0;

    private const ACTOR_NAME = 'Sapo Sync';

    public function __construct(private SapoImageDownloader $imageDownloader)
    {
    }

    public function import(array $sapoProductIds = []): array
    {
        $stats = [
            'products_ok' => 0,
            'products_skip' => 0,
            'products_error' => 0,
            'variants_ok' => 0,
            'images_ok' => 0,
            'images_skip' => 0,
            'images_error' => 0,
            'deactivated' => 0,
            'errors' => [],
        ];

        $ids = array_values(array_unique(array_filter(array_map('intval', $sapoProductIds))));
        $query = DB::table('sapo_product_cache')->orderBy('ID');
        if ($ids !== []) {
            $query->whereIn('ID', $ids);
        }

        $collectionMap = $this->collectionMap($ids);
        $categoryBySapo = $this->localCategoryBySapoCollection();

        foreach ($query->cursor() as $row) {
            $payload = json_decode((string) $row->PAYLOAD, true);
            $sapoId = (int) ($row->ID ?? 0);
            if (! is_array($payload) || $sapoId <= 0) {
                $stats['products_skip']++;
                continue;
            }

            try {
                $result = $this->importOne($payload, $collectionMap[$sapoId] ?? [], $categoryBySapo);
                $stats['products_ok']++;
                $stats['variants_ok'] += $result['variants_ok'];
                $stats['images_ok'] += $result['images_ok'];
                $stats['images_skip'] += $result['images_skip'];
                $stats['images_error'] += $result['images_error'];
            } catch (Throwable $e) {
                $stats['products_error']++;
                $stats['errors'][] = $sapoId.': '.$e->getMessage();
                Log::error('Sapo product import failed', [
                    'sapo_id' => $sapoId,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    public function deactivateMissing(array $sapoProductIds): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $sapoProductIds))));
        if ($ids === []) {
            return 0;
        }

        return Product::query()
            ->whereNotNull('SAPO_ID')
            ->whereIn('SAPO_ID', $ids)
            ->update([
                'IS_ACTIVE' => false,
                'UPD_DT' => Carbon::now(),
                'UPD_NAME' => self::ACTOR_NAME,
                'UPD_ID' => self::ACTOR_ID,
            ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, int>  $sapoCollectionIds
     * @param  array<int, int>  $categoryBySapo
     * @return array{variants_ok: int, images_ok: int, images_skip: int, images_error: int}
     */
    private function importOne(array $payload, array $sapoCollectionIds, array $categoryBySapo): array
    {
        $sapoId = (int) ($payload['id'] ?? 0);
        if ($sapoId <= 0) {
            throw new \RuntimeException('Thiếu Sapo product id');
        }

        $imageStats = $this->downloadImages($payload);

        DB::beginTransaction();
        try {
            $product = Product::query()->where('SAPO_ID', $sapoId)->first() ?? new Product();
            $this->fillProduct($product, $payload, $sapoId);
            $product->save();

            if (empty($product->MA_SAN_PHAM)) {
                $product->MA_SAN_PHAM = (string) $product->ID;
                $product->save();
            }

            $this->syncCategories($product->ID, $sapoCollectionIds, $categoryBySapo);
            $this->attachImages($product, $payload, $imageStats['by_sapo_image_id']);
            $variantsOk = $this->syncVariants($product, $payload, $imageStats['by_sapo_image_id']);

            DB::commit();

            return [
                'variants_ok' => $variantsOk,
                'images_ok' => $imageStats['ok'],
                'images_skip' => $imageStats['skip'],
                'images_error' => $imageStats['error'],
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function fillProduct(Product $product, array $payload, int $sapoId): void
    {
        $now = Carbon::now();
        $name = $this->clip((string) ($payload['name'] ?? ''), 1000);
        if ($name === '') {
            $name = 'Sản phẩm Sapo '.$sapoId;
        }
        $summary = $this->clip((string) ($payload['summary'] ?? ''), 255);
        $content = (string) ($payload['content'] ?? '');
        $meta = trim((string) ($payload['meta_title'] ?? ''));
        $keywords = $this->clip($meta !== '' ? $meta : $name, 500);
        $status = (string) ($payload['status'] ?? 'active');
        $alias = trim((string) ($payload['alias'] ?? ''));

        $price = 0.0;
        $compare = null;
        $qty = 0;
        $priceVariantId = '';
        $variants = is_array($payload['variants'] ?? null) ? $payload['variants'] : [];
        foreach ($variants as $variant) {
            if (! is_array($variant)) {
                continue;
            }
            $qty += (int) ($variant['inventory_quantity'] ?? 0);
            $variantPrice = isset($variant['price']) ? (float) $variant['price'] : 0.0;
            if ($variantPrice <= 0) {
                continue;
            }
            if ($price <= 0 || $variantPrice < $price) {
                $price = $variantPrice;
                $priceVariantId = (string) ($variant['id'] ?? '');
                $variantCompare = isset($variant['compare_at_price']) && $variant['compare_at_price'] !== null
                    ? (float) $variant['compare_at_price']
                    : null;
                $compare = $variantCompare;
            }
        }

        if (empty($product->UUID)) {
            $product->UUID = $this->uniqueUuid();
        }

        $product->SAPO_ID = $sapoId;
        $product->SAPO_PAYLOAD = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $product->NAME = $name;
        $product->MA_SAN_PHAM = $alias !== '' ? $this->clip($alias, 100) : (string) $sapoId;
        $product->TYPE = $this->clip((string) ($payload['product_type'] ?? AppConstant::TYPE_PRODUCT_COMMON), 1000);
        $product->SHORT_DESCRIPTION = $summary !== '' ? $summary : null;
        $product->DESCRIPTION_DETAIL = $content !== '' ? $content : null;
        $product->DESCRIPTION_DETAIL_ONLY_TEXT = $content !== '' ? $this->plainText($content) : null;
        $product->KEYWORDS_SEO_WEBSITE = $keywords;
        $product->PRODUCT_TAGS = $this->clip((string) ($payload['tags'] ?? ''), 1000) ?: null;
        $product->PRICE = $price > 0 ? $price : null;
        $product->PRICE_SALE = ($compare !== null && $compare > $price) ? $compare : null;
        $product->PRODUCT_QUANTITY = $qty > 0 ? $qty : null;
        $product->STATUS = AppConstant::STATUS_USING;
        $product->IS_ACTIVE = $status === 'active';
        $product->PRODUCT_HOT = (bool) ($product->PRODUCT_HOT ?? false);
        $product->PRODUCT_VIP = (bool) ($product->PRODUCT_VIP ?? false);
        $product->ATTR1 = $priceVariantId;
        $product->ATTR2 = $this->clip((string) ($payload['vendor'] ?? ''), 500) ?: null;
        $product->ATTR3 = $this->optionGroupName($payload);
        $product->ATTR49 = AppConstant::TYPE_PRODUCT_COMMON;
        $product->ATTR50 = AppConstant::PATH_CHI_TIET_PRODUCT_COMMON;
        $product->CRT_DT = $product->CRT_DT ?? $now;
        $product->UPD_DT = $now;
        $product->CRT_ID = $product->CRT_ID ?? self::ACTOR_ID;
        $product->UPD_ID = self::ACTOR_ID;
        $product->CRT_NAME = $product->CRT_NAME ?? self::ACTOR_NAME;
        $product->UPD_NAME = self::ACTOR_NAME;
    }

    /**
     * @param  array<int, int>  $sapoCollectionIds
     * @param  array<int, int>  $categoryBySapo
     */
    private function syncCategories(int $productId, array $sapoCollectionIds, array $categoryBySapo): void
    {
        ProductCategory::query()->where('PRODUCT_ID', $productId)->delete();

        $localIds = [];
        foreach ($sapoCollectionIds as $collectionId) {
            $localId = $categoryBySapo[(int) $collectionId] ?? null;
            if ($localId) {
                $localIds[] = (int) $localId;
            }
        }
        $localIds = array_values(array_unique($localIds));
        $now = Carbon::now();
        foreach ($localIds as $index => $categoryId) {
            $pivot = new ProductCategory();
            $pivot->PRODUCT_ID = $productId;
            $pivot->CATEGORY_ID = $categoryId;
            $pivot->SORT_ORDER = $index;
            $pivot->STATUS = AppConstant::STATUS_USING;
            $pivot->IS_ACTIVE = true;
            $pivot->CRT_DT = $now;
            $pivot->UPD_DT = $now;
            $pivot->CRT_ID = self::ACTOR_ID;
            $pivot->UPD_ID = self::ACTOR_ID;
            $pivot->CRT_NAME = self::ACTOR_NAME;
            $pivot->UPD_NAME = self::ACTOR_NAME;
            $pivot->save();
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{ok: int, skip: int, error: int, by_sapo_image_id: array<int, int>}
     */
    private function downloadImages(array $payload): array
    {
        $images = $this->collectImages($payload);
        $ok = 0;
        $skip = 0;
        $error = 0;
        $bySapoImageId = [];

        foreach ($images as $image) {
            if (! is_array($image) || empty($image['src'])) {
                continue;
            }
            $sapoImageId = (int) ($image['id'] ?? 0);
            $row = $this->imageDownloader->download($image);
            if ($row instanceof DocumentStorage && $row->ID) {
                if ($row->wasRecentlyCreated) {
                    $ok++;
                } else {
                    $skip++;
                }
                if ($sapoImageId > 0) {
                    $bySapoImageId[$sapoImageId] = (int) $row->ID;
                }
            } else {
                $error++;
            }
        }

        return [
            'ok' => $ok,
            'skip' => $skip,
            'error' => $error,
            'by_sapo_image_id' => $bySapoImageId,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, int>  $bySapoImageId
     */
    private function attachImages(Product $product, array $payload, array $bySapoImageId): void
    {
        $images = $this->collectImages($payload);
        $primary = is_array($payload['image'] ?? null) ? $payload['image'] : ($images[0] ?? null);
        $primarySapoId = is_array($primary) ? (int) ($primary['id'] ?? 0) : 0;

        ProductDocumentStorage::query()->where('PRODUCT_ID', $product->ID)->delete();

        $now = Carbon::now();
        $sort = 0;
        $attached = [];
        foreach ($images as $index => $image) {
            if (! is_array($image)) {
                continue;
            }
            $sapoImageId = (int) ($image['id'] ?? 0);
            $localId = $bySapoImageId[$sapoImageId] ?? null;
            if (! $localId || isset($attached[$localId])) {
                continue;
            }
            $attached[$localId] = true;
            $isPrimary = $primarySapoId > 0 ? $sapoImageId === $primarySapoId : $index === 0;
            $doc = DocumentStorage::query()->find($localId);
            $pivot = new ProductDocumentStorage();
            $pivot->PRODUCT_ID = $product->ID;
            $pivot->DOCUMENT_STORAGE_ID = $localId;
            $pivot->SORT_ORDER = $sort++;
            $pivot->IS_THUMNAIL = $isPrimary;
            $pivot->TYPE = 'image';
            $pivot->EXTENSION = $doc?->EXTENSION ?: 'jpg';
            $pivot->ATTR1 = $isPrimary ? 'DANH_SACH_HINH_ANH_DAI_DIEN' : 'DANH_SACH_HINH_ANH';
            $pivot->ATTR2 = '1x1';
            $pivot->STATUS = AppConstant::STATUS_USING;
            $pivot->IS_ACTIVE = true;
            $pivot->CRT_DT = $now;
            $pivot->UPD_DT = $now;
            $pivot->CRT_ID = self::ACTOR_ID;
            $pivot->UPD_ID = self::ACTOR_ID;
            $pivot->CRT_NAME = self::ACTOR_NAME;
            $pivot->UPD_NAME = self::ACTOR_NAME;
            $pivot->save();
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, int>  $imageBySapoId
     */
    private function syncVariants(Product $product, array $payload, array $imageBySapoId): int
    {
        $variants = is_array($payload['variants'] ?? null) ? $payload['variants'] : [];
        $images = $this->collectImages($payload);
        $primary = is_array($payload['image'] ?? null) ? $payload['image'] : ($images[0] ?? null);
        $imageByVariantId = [];
        foreach ($images as $image) {
            if (! is_array($image)) {
                continue;
            }
            $variantIds = is_array($image['variant_ids'] ?? null) ? $image['variant_ids'] : [];
            foreach ($variantIds as $vid) {
                $vid = (int) $vid;
                if ($vid > 0 && ! isset($imageByVariantId[$vid])) {
                    $imageByVariantId[$vid] = (int) ($image['id'] ?? 0);
                }
            }
        }

        $keep = [];
        $now = Carbon::now();
        $count = 0;
        foreach ($variants as $variant) {
            if (! is_array($variant)) {
                continue;
            }
            $sapoVariantId = (int) ($variant['id'] ?? 0);
            if ($sapoVariantId <= 0) {
                continue;
            }
            $keep[] = $sapoVariantId;

            $row = ProductVariant::query()->where('SAPO_VARIANT_ID', $sapoVariantId)->first() ?? new ProductVariant();
            $price = isset($variant['price']) ? (float) $variant['price'] : 0.0;
            $compare = isset($variant['compare_at_price']) && $variant['compare_at_price'] !== null
                ? (float) $variant['compare_at_price']
                : null;
            $title = trim((string) ($variant['title'] ?? $variant['option1'] ?? 'Mặc định'));
            $color = trim((string) ($variant['option1'] ?? $title));
            if ($color === '') {
                $color = 'Mặc định';
            }
            $qty = (int) ($variant['inventory_quantity'] ?? 0);
            $policy = (string) ($variant['inventory_policy'] ?? 'deny');
            $managed = (string) ($variant['inventory_management'] ?? '');
            $inStock = $managed === '' || $managed === 'null' || $qty > 0 || $policy === 'continue';

            $imageId = isset($variant['image_id']) ? (int) $variant['image_id'] : 0;
            if ($imageId <= 0) {
                $imageId = $imageByVariantId[$sapoVariantId] ?? (int) ($primary['id'] ?? 0);
            }
            $localImageId = $imageId > 0 ? ($imageBySapoId[$imageId] ?? null) : null;

            $row->SAPO_VARIANT_ID = $sapoVariantId;
            $row->PRODUCT_ID = $product->ID;
            $row->PRODUCT_STATUS = $inStock ? 'CON_HANG' : 'HET_HANG';
            $row->PRODUCT_COLOR = $this->clip($color, 500);
            $row->PRODUCT_IMAGE_ID = $localImageId;
            $row->IS_CONTACT_PRICE = $price <= 0;
            $row->PRODUCT_PRICE = $price > 0 ? $price : null;
            $row->PRODUCT_ORIGINAL_PRICE = ($compare !== null && $compare > $price) ? $compare : null;
            $row->IS_IN_STOCK = $inStock;
            $row->STATUS = AppConstant::STATUS_USING;
            $row->IS_ACTIVE = true;
            $row->ATTR1 = $this->clip((string) ($variant['sku'] ?? ''), 500) ?: null;
            $row->ATTR2 = $this->clip((string) ($variant['option2'] ?? ''), 500) ?: null;
            $row->ATTR3 = $this->clip((string) ($variant['option3'] ?? ''), 500) ?: null;
            $row->ATTR4 = $title;
            $row->ATTR50 = json_encode($variant, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $row->CRT_DT = $row->CRT_DT ?? $now;
            $row->UPD_DT = $now;
            $row->CRT_ID = $row->CRT_ID ?? self::ACTOR_ID;
            $row->UPD_ID = self::ACTOR_ID;
            $row->CRT_NAME = $row->CRT_NAME ?? self::ACTOR_NAME;
            $row->UPD_NAME = self::ACTOR_NAME;
            $row->save();
            $count++;
        }

        ProductVariant::query()
            ->where('PRODUCT_ID', $product->ID)
            ->when($keep !== [], fn ($q) => $q->whereNotIn('SAPO_VARIANT_ID', $keep))
            ->update([
                'STATUS' => AppConstant::STATUS_DELETED,
                'IS_ACTIVE' => false,
                'UPD_DT' => $now,
            ]);

        return $count;
    }

    /**
     * @param  array<int, int>  $sapoProductIds
     * @return array<int, array<int, int>>
     */
    private function collectionMap(array $sapoProductIds): array
    {
        $q = DB::table('sapo_product_collection');
        if ($sapoProductIds !== []) {
            $q->whereIn('SAPO_PRODUCT_ID', $sapoProductIds);
        }
        $map = [];
        foreach ($q->get() as $row) {
            $pid = (int) $row->SAPO_PRODUCT_ID;
            $map[$pid][] = (int) $row->SAPO_COLLECTION_ID;
        }

        return $map;
    }

    /**
     * @return array<int, int> sapoCollectionId => local category_p.ID
     */
    private function localCategoryBySapoCollection(): array
    {
        $map = [];
        $rows = DB::table('category_p')
            ->whereNotNull('ATTR2')
            ->where('ATTR2', '!=', '')
            ->where('STATUS', AppConstant::STATUS_USING)
            ->get(['ID', 'ATTR2']);
        foreach ($rows as $row) {
            $sapoId = (int) $row->ATTR2;
            if ($sapoId > 0) {
                $map[$sapoId] = (int) $row->ID;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function optionGroupName(array $payload): string
    {
        $options = is_array($payload['options'] ?? null) ? $payload['options'] : [];
        $name = trim((string) ($options[0]['name'] ?? ''));
        if ($name === '' || strcasecmp($name, 'Title') === 0) {
            return 'Phân loại';
        }

        return $this->clip($name, 255);
    }

    private function uniqueUuid(): string
    {
        for ($i = 0; $i < 100; $i++) {
            $uuid = substr(preg_replace('/[^a-zA-Z0-9]/', '', Str::random(8)) ?: Str::random(6), 0, 6);
            if (strlen($uuid) < 6) {
                $uuid = str_pad($uuid, 6, '0');
            }
            if (! Product::query()->where('UUID', $uuid)->exists()) {
                return $uuid;
            }
        }

        throw new \RuntimeException('Không tạo được UUID sản phẩm.');
    }

    private function clip(string $value, int $max): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return mb_substr($value, 0, $max);
    }

    private function plainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $this->clip($text, 60000);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function collectImages(array $payload): array
    {
        $images = is_array($payload['images'] ?? null) ? $payload['images'] : [];
        if ($images === [] && is_array($payload['image'] ?? null)) {
            return [$payload['image']];
        }

        return $images;
    }
}
