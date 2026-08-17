<?php

namespace App\Service\impl;

use App\Service\SapoService;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SapoServiceImpl implements SapoService
{
    public function isEnabled(): bool
    {
        $status = $this->configurationStatus();

        return $status['enabled'];
    }

    public function configurationStatus(): array
    {
        $cfg = config('services.sapo', []);
        $missing = [];

        if (empty($cfg['enabled'])) {
            $missing[] = 'SAPO_ENABLED';
        }
        if (trim((string) ($cfg['store'] ?? '')) === '') {
            $missing[] = 'SAPO_STORE';
        }
        if (trim((string) ($cfg['api_key'] ?? '')) === '') {
            $missing[] = 'SAPO_API_KEY';
        }
        if (trim((string) ($cfg['api_secret'] ?? '')) === '') {
            $missing[] = 'SAPO_API_SECRET';
        }

        return [
            'enabled' => $missing === [],
            'missing' => $missing,
            'store' => trim((string) ($cfg['store'] ?? '')),
            'product_type' => trim((string) ($cfg['product_type'] ?? '')),
        ];
    }

    public function getProducts(array $query = []): array
    {
        $skipCount = ! empty($query['skip_count']);
        unset($query['skip_count']);

        $params = $this->normalizeProductQuery($query);

        $data = $this->get('/admin/products.json', $params);
        $products = $data['products'] ?? [];
        $products = is_array($products) ? $products : [];

        if ($skipCount) {
            return [
                'products' => $products,
                'count' => count($products),
            ];
        }

        $countParams = $params;
        unset($countParams['page'], $countParams['limit'], $countParams['fields'], $countParams['since_id'], $countParams['ids']);

        return [
            'products' => $products,
            'count' => $this->getProductsCount($countParams),
        ];
    }

    public function getProductsCount(array $query = []): int
    {
        $params = $this->normalizeProductQuery($query);
        unset($params['page'], $params['limit'], $params['fields'], $params['since_id'], $params['ids'], $params['query']);

        $data = $this->get('/admin/products/count.json', $params);

        return (int) ($data['count'] ?? 0);
    }

    public function getProduct(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        try {
            $data = $this->get('/admin/products/'.$id.'.json');
        } catch (RuntimeException $e) {
            Log::warning('Sapo getProduct failed', ['id' => $id, 'message' => $e->getMessage()]);

            return null;
        }

        $product = $data['product'] ?? null;

        return is_array($product) ? $product : null;
    }

    public function getCustomCollections(array $query = []): array
    {
        $params = array_merge([
            'limit' => 250,
            'page' => 1,
        ], $query);

        $data = $this->get('/admin/custom_collections.json', $params);
        $items = $data['custom_collections'] ?? [];

        return is_array($items) ? $items : [];
    }

    public function getSmartCollections(array $query = []): array
    {
        $params = array_merge([
            'limit' => 250,
            'page' => 1,
        ], $query);

        $data = $this->get('/admin/smart_collections.json', $params);
        $items = $data['smart_collections'] ?? [];

        return is_array($items) ? $items : [];
    }

    public function getCollects(array $query = []): array
    {
        $params = array_merge([
            'limit' => 250,
            'page' => 1,
        ], $query);

        $data = $this->get('/admin/collects.json', $params);
        $items = $data['collects'] ?? [];

        return is_array($items) ? $items : [];
    }

    public function getBlogs(array $query = []): array
    {
        $data = $this->get('/admin/blogs.json', $query);
        $items = $data['blogs'] ?? [];

        return is_array($items) ? $items : [];
    }

    public function getArticles(int $blogId, array $query = []): array
    {
        $params = array_merge([
            'limit' => 50,
            'page' => 1,
        ], $query);

        $data = $this->get('/admin/blogs/'.$blogId.'/articles.json', $params);
        $articles = $data['articles'] ?? [];

        $countData = $this->get('/admin/blogs/'.$blogId.'/articles/count.json', array_diff_key($params, array_flip(['page', 'limit', 'fields'])));

        return [
            'articles' => is_array($articles) ? $articles : [],
            'count' => (int) ($countData['count'] ?? count($articles)),
        ];
    }

    /**
     * Map app filter → query SAPO GET /admin/products.json.
     * Docs: product_type, query, page, limit (max 250).
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function normalizeProductQuery(array $query): array
    {
        $page = max(1, (int) ($query['page'] ?? 1));
        $limit = min(250, max(1, (int) ($query['limit'] ?? 50)));

        $productType = $query['product_type'] ?? null;
        if (isset($query['product_types']) && is_array($query['product_types'])) {
            foreach ($query['product_types'] as $type) {
                if (is_string($type) && trim($type) !== '') {
                    $productType = trim($type);
                    break;
                }
            }
        }

        $keyword = isset($query['query']) ? trim((string) $query['query']) : '';

        $params = [
            'page' => $page,
            'limit' => $limit,
        ];

        if ($keyword !== '') {
            $params['query'] = $keyword;
        }

        if (is_string($productType) && trim($productType) !== '') {
            $params['product_type'] = trim($productType);
        }

        foreach ([
            'vendor', 'alias', 'collection_id', 'ids', 'since_id', 'fields', 'published', 'status',
            'created_on_min', 'created_on_max', 'modified_on_min', 'modified_on_max',
            'published_on_min', 'published_on_max',
        ] as $key) {
            if (! array_key_exists($key, $query) || $query[$key] === null || $query[$key] === '') {
                continue;
            }
            $params[$key] = $query[$key];
        }

        return $params;
    }

    public function get(string $path, array $query = []): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Sapo API chưa được cấu hình (SAPO_ENABLED / credentials).');
        }

        $response = $this->client()
            ->get($this->baseUrl().$path, $query);

        if ($response->failed()) {
            throw new RuntimeException(
                'Sapo API error '.$response->status().': '.$response->body()
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public function post(string $path, array $payload = []): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Sapo API chưa được cấu hình (SAPO_ENABLED / credentials).');
        }

        $response = $this->client()
            ->asJson()
            ->post($this->baseUrl().$path, $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'Sapo API error '.$response->status().': '.$response->body()
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public function put(string $path, array $payload = []): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Sapo API chưa được cấu hình (SAPO_ENABLED / credentials).');
        }

        $response = $this->client()
            ->asJson()
            ->put($this->baseUrl().$path, $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'Sapo API error '.$response->status().': '.$response->body()
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public function delete(string $path): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Sapo API chưa được cấu hình (SAPO_ENABLED / credentials).');
        }

        $response = $this->client()->delete($this->baseUrl().$path);

        if ($response->failed()) {
            throw new RuntimeException(
                'Sapo API error '.$response->status().': '.$response->body()
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    private function client(): PendingRequest
    {
        $cfg = config('services.sapo');

        $request = Http::timeout((int) ($cfg['timeout'] ?? 60))
            ->acceptJson()
            ->withBasicAuth((string) $cfg['api_key'], (string) $cfg['api_secret']);

        $token = trim((string) ($cfg['access_token'] ?? ''));
        if ($token !== '') {
            $request = $request->withHeaders(['X-Sapo-Access-Token' => $token]);
        }

        return $request;
    }

    private function baseUrl(): string
    {
        $store = trim((string) config('services.sapo.store'));
        $store = preg_replace('#^https?://#i', '', $store) ?: '';
        $store = rtrim($store, '/');

        return 'https://'.$store;
    }
}
