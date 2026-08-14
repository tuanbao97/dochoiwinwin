<?php

namespace App\Service;

interface SapoService
{
    public function isEnabled(): bool;

    /**
     * @param  array<string, mixed>  $query
     * @return array{products: array<int, array<string, mixed>>, count: int}
     */
    public function getProducts(array $query = []): array;

    public function getProductsCount(array $query = []): int;

    /**
     * @return array<string, mixed>|null
     */
    public function getProduct(int $id): ?array;

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, array<string, mixed>>
     */
    public function getCustomCollections(array $query = []): array;

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, array<string, mixed>>
     */
    public function getSmartCollections(array $query = []): array;

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, array<string, mixed>>
     */
    public function getBlogs(array $query = []): array;

    /**
     * @param  array<string, mixed>  $query
     * @return array{articles: array<int, array<string, mixed>>, count: int}
     */
    public function getArticles(int $blogId, array $query = []): array;

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array;
}
