<?php

namespace App\Service\impl;

use App\Enum\AppConstant;
use App\Models\DocumentStorage;
use App\Service\AppService;
use App\Service\DocumentStorageService;
use App\Service\InterventionImageService;
use App\Service\SapoImageDownloader;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SapoImageDownloaderImpl implements SapoImageDownloader
{
    private const ACTOR_ID = 0;

    private const ACTOR_NAME = 'Sapo Sync';

    /** @var array<int, DocumentStorage|null> */
    private array $bySapoId = [];

    public function __construct(
        private AppService $appService,
        private DocumentStorageService $documentStorageService,
        private InterventionImageService $interventionImage,
    ) {
    }

    public function download(array $image): ?DocumentStorage
    {
        $sapoImageId = (int) ($image['id'] ?? 0);
        $src = trim((string) ($image['src'] ?? ''));
        if ($src === '' || ! filter_var($src, FILTER_VALIDATE_URL)) {
            return null;
        }

        $sourceUrl = $this->normalizeSourceUrl($src);

        if ($sapoImageId > 0 && array_key_exists($sapoImageId, $this->bySapoId)) {
            return $this->bySapoId[$sapoImageId];
        }

        $existing = $this->findExisting($sapoImageId, $sourceUrl);
        if ($existing && $this->localFileExists($existing)) {
            if ($sapoImageId > 0) {
                $this->bySapoId[$sapoImageId] = $existing;
            }

            return $existing;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; WinWinCatalog/1.0)',
                'Accept' => 'image/*,*/*;q=0.8',
            ])->timeout(45)->retry(2, 400)->get($src);

            if (! $response->successful()) {
                Log::warning('Sapo image HTTP failed', [
                    'id' => $sapoImageId,
                    'status' => $response->status(),
                    'src' => $src,
                ]);
                $this->remember($sapoImageId, $existing);

                return $existing;
            }

            $body = $response->body();
            if ($body === '' || strlen($body) > 15 * 1024 * 1024) {
                $this->remember($sapoImageId, $existing);

                return $existing;
            }

            $contentType = strtolower((string) $response->header('Content-Type'));
            $extension = $this->resolveExtension($image, $src, $contentType);
            if ($extension === null) {
                Log::warning('Sapo image skipped (not an image)', [
                    'id' => $sapoImageId,
                    'content_type' => $contentType,
                ]);
                $this->remember($sapoImageId, $existing);

                return $existing;
            }

            $directoryPath = $this->appService->getCurrDirectory();
            $absoluteDir = public_path($directoryPath);
            if (! File::isDirectory($absoluteDir)) {
                File::makeDirectory($absoluteDir, 0777, true, true);
            }

            $fileNameHash = Str::random(40).'.'.$extension;
            $relativePath = $directoryPath.'/'.$fileNameHash;
            $absolutePath = public_path($relativePath);
            File::put($absolutePath, $body);

            try {
                $this->interventionImage
                    ->readImage($absolutePath)
                    ->setFileName($fileNameHash)
                    ->setDestPath($absoluteDir)
                    ->storeImg((int) config('app.image_quality', 70));
                $this->documentStorageService->resizeImgTheoKichThuoc(
                    '1x1',
                    $this->interventionImage,
                    $fileNameHash,
                    $directoryPath
                );
            } catch (Throwable $e) {
                Log::warning('Sapo image resize failed, keep raw', [
                    'id' => $sapoImageId,
                    'message' => $e->getMessage(),
                ]);
            }

            $originalName = (string) ($image['filename'] ?? basename(parse_url($src, PHP_URL_PATH) ?: ('image.'.$extension)));
            $now = Carbon::now();
            $row = $existing ?? new DocumentStorage();
            $row->NAME = $fileNameHash;
            $row->ORIGINAL_NAME = $originalName;
            $row->EXTENSION = $extension;
            $row->PATH = $relativePath;
            $row->DIRECTORY = $directoryPath;
            $row->SIZE = File::size($absolutePath);
            $row->TYPE_FILE = 'image';
            $row->STATUS = AppConstant::STATUS_USING;
            $row->IS_ACTIVE = true;
            $row->SAPO_IMAGE_ID = $sapoImageId > 0 ? $sapoImageId : $row->SAPO_IMAGE_ID;
            $row->SOURCE_URL = $sourceUrl;
            $row->CRT_DT = $row->CRT_DT ?? $now;
            $row->UPD_DT = $now;
            $row->CRT_ID = $row->CRT_ID ?? self::ACTOR_ID;
            $row->UPD_ID = self::ACTOR_ID;
            $row->CRT_NAME = $row->CRT_NAME ?? self::ACTOR_NAME;
            $row->UPD_NAME = self::ACTOR_NAME;
            $row->save();

            $this->remember($sapoImageId, $row);

            return $row;
        } catch (Throwable $e) {
            Log::error('Sapo image download failed', [
                'id' => $sapoImageId,
                'src' => $src,
                'message' => $e->getMessage(),
            ]);
            $this->remember($sapoImageId, $existing);

            return $existing;
        }
    }

    private function findExisting(int $sapoImageId, string $sourceUrl): ?DocumentStorage
    {
        if ($sapoImageId > 0) {
            $row = DocumentStorage::query()
                ->where('SAPO_IMAGE_ID', $sapoImageId)
                ->where('STATUS', AppConstant::STATUS_USING)
                ->first();
            if ($row) {
                return $row;
            }
        }

        if ($sourceUrl === '') {
            return null;
        }

        return DocumentStorage::query()
            ->where('SOURCE_URL', $sourceUrl)
            ->where('STATUS', AppConstant::STATUS_USING)
            ->first();
    }

    private function localFileExists(DocumentStorage $row): bool
    {
        $path = (string) $row->PATH;
        if ($path === '') {
            return false;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return false;
        }

        return File::isFile(public_path($path));
    }

    private function remember(int $sapoImageId, ?DocumentStorage $row): void
    {
        if ($sapoImageId > 0) {
            $this->bySapoId[$sapoImageId] = $row;
        }
    }

    public static function normalizeSourceUrl(string $src): string
    {
        $parts = parse_url($src);
        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return $src;
        }
        $path = $parts['path'] ?? '';

        return $parts['scheme'].'://'.$parts['host'].$path;
    }

    /**
     * @param  array<string, mixed>  $image
     */
    public static function resolveExtension(array $image, string $src, string $contentType = ''): ?string
    {
        $filename = (string) ($image['filename'] ?? '');
        $fromName = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $fromPath = strtolower(pathinfo((string) (parse_url($src, PHP_URL_PATH) ?: ''), PATHINFO_EXTENSION));
        $fromMime = match (true) {
            str_contains($contentType, 'jpeg'), str_contains($contentType, 'jpg') => 'jpg',
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'gif') => 'gif',
            str_contains($contentType, 'webp') => 'webp',
            default => '',
        };

        $ext = $fromName !== '' ? $fromName : ($fromPath !== '' ? $fromPath : $fromMime);
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        $allowed = ['jpg', 'png', 'gif', 'webp'];

        return in_array($ext, $allowed, true) ? $ext : ($fromMime !== '' ? $fromMime : null);
    }
}
