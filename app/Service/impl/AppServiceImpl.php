<?php

namespace App\Service\impl;

use App\Jobs\SyncSapoCatalogJob;
use App\Service\AppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Dto\response\ApiResponseDto;
use App\Dto\response\StatusActiveDto;
use App\Enum\AppConstant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AppServiceImpl implements AppService
{
    private const SAPO_SYNC_STATUS_KEY = 'SAPO_ADMIN_SYNC_STATUS';
    private const SAPO_SYNC_STATUS_TTL = 86400;
    private const SAPO_SYNC_STALE_SECONDS = 1800;

    /**
     * Get thư mục upload theo ngày tháng năm hiện tại
     * 
     * @return string
     */
    public function getCurrDirectory(): string
    {
        $currentDate = Carbon::now();
        $directoryName = $currentDate->format('Y-m-d');
        return 'upload/UI-BACKEND/' . $directoryName;
    }

    /**
     * Xóa dấu câu tiếng Việt từ một chuỗi
     *
     * @param string $string
     * @return string
     */
    function removeDauChamCau($string) : string {
        $whatToStrip = array("?","!",",",";", ".");
        
        // Sử dụng preg_replace để loại bỏ các ký tự dấu câu
        return str_replace($whatToStrip, '', $string);
    }

    public function removeTiengViet($string): string
    {
        $accents = [
            'a' => ['á', 'à', 'ạ', 'ả', 'ã', 'â', 'ấ', 'ầ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ắ', 'ằ', 'ặ', 'ẳ', 'ẵ'],
            'A' => ['Á', 'À', 'Ạ', 'Ả', 'Ã', 'Â', 'Ấ', 'Ầ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ắ', 'Ằ', 'Ặ', 'Ẳ', 'Ẵ'],
            'e' => ['é', 'è', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ế', 'ề', 'ệ', 'ể', 'ễ'],
            'E' => ['É', 'È', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ế', 'Ề', 'Ệ', 'Ể', 'Ễ'],
            'i' => ['í', 'ì', 'ị', 'ỉ', 'ĩ'],
            'I' => ['Í', 'Ì', 'Ị', 'Ỉ', 'Ĩ'],
            'o' => ['ó', 'ò', 'ọ', 'ỏ', 'õ', 'ô', 'ố', 'ồ', 'ộ', 'ổ', 'ỗ', 'ơ', 'ớ', 'ờ', 'ợ', 'ở', 'ỡ'],
            'O' => ['Ó', 'Ò', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ố', 'Ồ', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ớ', 'Ờ', 'Ợ', 'Ở', 'Ỡ'],
            'u' => ['ú', 'ù', 'ụ', 'ủ', 'ũ', 'ư', 'ứ', 'ừ', 'ự', 'ử', 'ữ'],
            'U' => ['Ú', 'Ù', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ứ', 'Ừ', 'Ự', 'Ử', 'Ữ'],
            'y' => ['ý', 'ỳ', 'ỵ', 'ỷ', 'ỹ'],
            'Y' => ['Ý', 'Ỳ', 'Ỵ', 'Ỷ', 'Ỹ'],
            'd' => ['đ'],
            'D' => ['Đ']
        ];
        
        foreach ($accents as $nonAccent => $accentedChars) {
            $string = str_replace($accentedChars, $nonAccent, $string);
        }
        
        return $string;
    }

    public function removeDauCauVaTiengViet($string): string
    {
        // Xóa dấu câu
        $removePunctiation = self::removeDauChamCau($string);
        // Xóa tiếng việt
        $removeVieAccents = self::removeTiengViet($removePunctiation);
        return $removeVieAccents;
    }
    public function buildUriFromText($string): string
    {
        $textRemoveDauCauVaTiengViet = strtolower(self::removeDauCauVaTiengViet($string));
        return preg_replace('/\s+/', '-', $textRemoveDauCauVaTiengViet);
    }

    public function getListTrangThaiHoatDong(Request $request)
    {
        // Khởi tạo giá trị biến trạng thái thái hoạt động
        $statusActives = [
            new StatusActiveDto('Hoạt động', true),
            new StatusActiveDto('Không Hoạt động', false)
        ];

        $statusActives = collect($statusActives)->map(function($item, $key) {
            return [
                'NAME' => $item->name,
                'VALUE' => $item->value
            ];
        });

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Truy vấn thành công.'
                , [
                    class_basename(StatusActiveDto::class) => $statusActives
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function evictCache(Request $request)
    {
        evictCacheDataFrontEnd();
        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Xóa cache thành công.'
                , []
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    /**
     * Chạy sync sau khi đã trả response (fastcgi_finish_request) — fetch full + tải ảnh
     * mất vài phút, chạy trong request sẽ bị nginx trả 504.
     */
    public function syncSapoCatalog(Request $request)
    {
        $forceFull = filter_var($request->input('FULL', false), FILTER_VALIDATE_BOOLEAN);

        $current = $this->sapoSyncState();
        if (($current['STATE'] ?? '') === 'running') {
            return response()->json(
                new ApiResponseDto(AppConstant::STATUS_FAILURE, 'Đang có tiến trình fetch Sapo chạy nền, vui lòng đợi.', [
                    'SAPO_SYNC_STATUS' => $current,
                ])
            )->setStatusCode(JsonResponse::HTTP_OK);
        }

        $runId = (string) Str::uuid();
        $state = [
            'STATE' => 'running',
            'RUN_ID' => $runId,
            'FULL' => $forceFull,
            'STARTED_AT' => time(),
            'FINISHED_AT' => null,
            'STATUS' => null,
            'STATUS_DETAIL' => $forceFull ? 'Đang fetch FULL sản phẩm Sapo...' : 'Đang fetch sản phẩm Sapo...',
        ];
        Cache::put(self::SAPO_SYNC_STATUS_KEY, $state, self::SAPO_SYNC_STATUS_TTL);

        app()->terminating(function () use ($forceFull, $runId) {
            ignore_user_abort(true);
            @set_time_limit(0);
            $this->runSapoSync($forceFull, $runId);
        });

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS, 'Đã bắt đầu fetch Sapo chạy nền.', [
                'SAPO_SYNC_STATUS' => $state,
            ])
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function getSapoSyncStatus(Request $request)
    {
        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS, 'Truy vấn thành công.', [
                'SAPO_SYNC_STATUS' => $this->sapoSyncState(),
            ])
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    private function sapoSyncState(): array
    {
        $state = Cache::get(self::SAPO_SYNC_STATUS_KEY);
        if (! is_array($state)) {
            return ['STATE' => 'idle'];
        }

        // Process bị kill giữa chừng (timeout FPM, restart) → không khoá nút fetch mãi
        if (($state['STATE'] ?? '') === 'running'
            && time() - (int) ($state['STARTED_AT'] ?? 0) > self::SAPO_SYNC_STALE_SECONDS) {
            $state['STATE'] = 'failed';
            $state['STATUS'] = AppConstant::STATUS_FAILURE;
            $state['STATUS_DETAIL'] = 'Fetch Sapo bị gián đoạn, vui lòng thử lại.';
        }

        return $state;
    }

    private function runSapoSync(bool $forceFull, string $runId): void
    {
        try {
            $result = SyncSapoCatalogJob::dispatchSync($forceFull, true, false);
            [$status, $detail] = $this->sapoSyncSummary($result);
            $state = $status === AppConstant::STATUS_SUCCESS ? 'done' : 'failed';
        } catch (\Throwable $e) {
            $status = AppConstant::STATUS_FAILURE;
            $detail = 'Fetch Sapo lỗi: '.$e->getMessage();
            $state = 'failed';
        }

        $current = Cache::get(self::SAPO_SYNC_STATUS_KEY);
        if (is_array($current) && ($current['RUN_ID'] ?? null) !== $runId) {
            return;
        }

        Cache::put(self::SAPO_SYNC_STATUS_KEY, array_merge(is_array($current) ? $current : [], [
            'STATE' => $state,
            'FINISHED_AT' => time(),
            'STATUS' => $status,
            'STATUS_DETAIL' => $detail,
        ]), self::SAPO_SYNC_STATUS_TTL);
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private function sapoSyncSummary(array $result): array
    {
        $mode = $result['mode'] ?? '';
        $fetched = (int) ($result['fetched'] ?? 0);
        $import = is_array($result['import'] ?? null) ? $result['import'] : [];
        $productsOk = (int) ($import['products_ok'] ?? 0);
        $imagesOk = (int) ($import['images_ok'] ?? 0);
        $imagesSkip = (int) ($import['images_skip'] ?? 0);
        $errors = (int) ($import['products_error'] ?? 0) + (int) ($import['images_error'] ?? 0);

        $detail = match ($mode) {
            'disabled' => $this->sapoDisabledMessage(),
            'locked' => 'Đang có tiến trình fetch khác, vui lòng thử lại sau.',
            'fresh' => 'Cache còn mới, chưa cần fetch lại.',
            'full' => 'Fetch full thành công ('.$fetched.' sản phẩm).',
            'incremental' => 'Fetch thành công ('.$fetched.' sản phẩm cập nhật).',
            'import-only' => 'Import từ cache hoàn tất.',
            default => 'Fetch Sapo hoàn tất.',
        };
        if (! in_array($mode, ['disabled', 'locked'], true)) {
            $detail .= ' Import '.$productsOk.' sản phẩm, '.$imagesOk.' ảnh mới'
                .($imagesSkip > 0 ? ', '.$imagesSkip.' ảnh tái sử dụng' : '')
                .($errors > 0 ? ', '.$errors.' lỗi' : '')
                .'.';
        }

        $status = in_array($mode, ['disabled', 'locked'], true)
            ? AppConstant::STATUS_FAILURE
            : AppConstant::STATUS_SUCCESS;

        return [$status, $detail];
    }

    private function sapoDisabledMessage(): string
    {
        $status = app(\App\Service\SapoService::class)->configurationStatus();
        if ($status['missing'] === []) {
            return 'Sapo chưa được bật.';
        }

        return 'Sapo chưa cấu hình trên server. Thiếu: '.implode(', ', $status['missing'])
            .' trong .env (sau đó chạy php artisan optimize:clear).';
    }


}
