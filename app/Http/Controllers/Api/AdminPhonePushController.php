<?php

namespace App\Http\Controllers\Api;

use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Http\Controllers\Controller;
use App\Models\AdminPhonePush;
use App\Support\OrderPhoneNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPhonePushController extends Controller
{
    public function __construct(private readonly OrderPhoneNotifier $notifier) {}

    public function save(Request $request)
    {
        $data = $request->validate([
            'ENDPOINT' => ['required', 'string', 'max:2000', 'starts_with:https://'],
            'PUBLIC_KEY' => ['required', 'string', 'max:255'],
            'AUTH_TOKEN' => ['required', 'string', 'max:255'],
            'CONTENT_ENCODING' => ['nullable', 'string', 'in:aes128gcm,aesgcm'],
        ]);

        $subscription = AdminPhonePush::query()->updateOrCreate(
            ['ENDPOINT_HASH' => hash('sha256', $data['ENDPOINT'])],
            [
                'USER_ID' => Auth::id(),
                'ENDPOINT' => $data['ENDPOINT'],
                'PUBLIC_KEY' => $data['PUBLIC_KEY'],
                'AUTH_TOKEN' => $data['AUTH_TOKEN'],
                'CONTENT_ENCODING' => $data['CONTENT_ENCODING'] ?: 'aes128gcm',
            ]
        );

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS, 'Đã bật thông báo trên điện thoại này.', [
                'ID' => $subscription->ID,
            ])
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'ENDPOINT' => ['required', 'string', 'max:2000'],
        ]);

        AdminPhonePush::query()
            ->where('ENDPOINT_HASH', hash('sha256', $data['ENDPOINT']))
            ->delete();

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS, 'Đã tắt thông báo trên điện thoại này.', null)
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function test()
    {
        $sent = $this->notifier->sendTest();
        $detail = $sent > 0
            ? 'Đã gửi thông báo thử tới '.$sent.' điện thoại.'
            : 'Chưa có điện thoại nào bật thông báo.';

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS, $detail, ['SENT' => $sent])
        )->setStatusCode(JsonResponse::HTTP_OK);
    }
}
