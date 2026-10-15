<?php

namespace App\Support\Socialite;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use RuntimeException;

/**
 * Đăng nhập Zalo — Social API OAuth v4 (PKCE).
 * Docs: https://docs.zaloplatforms.com/docs/Social/social-api/tham-khao/user-access-token-v4
 *
 * Zalo không trả email; id là duy nhất theo từng app (đổi app → id khác).
 */
class ZaloProvider extends AbstractProvider
{
    protected $usesPKCE = true;

    protected $scopes = [];

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://oauth.zaloapp.com/v4/permission', $state);
    }

    protected function getCodeFields($state = null): array
    {
        $fields = parent::getCodeFields($state);
        $fields['app_id'] = $this->clientId;
        unset($fields['client_id'], $fields['scope'], $fields['response_type']);

        return $fields;
    }

    /** Zalo yêu cầu code_verifier đúng 43 ký tự */
    protected function getCodeVerifier(): string
    {
        return Str::random(43);
    }

    protected function getTokenUrl(): string
    {
        return 'https://oauth.zaloapp.com/v4/access_token';
    }

    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'secret_key' => $this->clientSecret,
            ],
            RequestOptions::FORM_PARAMS => $this->getTokenFields($code),
        ]);

        $data = json_decode((string) $response->getBody(), true) ?: [];

        // Zalo trả HTTP 200 kèm error khi code/verifier/secret sai
        if (empty($data['access_token'])) {
            throw new RuntimeException('Zalo từ chối đăng nhập: '
                .($data['error_description'] ?? $data['error_name'] ?? $data['message'] ?? 'không nhận được access token').'.');
        }

        return $data;
    }

    protected function getTokenFields($code): array
    {
        $fields = [
            'code' => $code,
            'app_id' => $this->clientId,
            'grant_type' => 'authorization_code',
        ];

        if ($this->usesPKCE()) {
            $fields['code_verifier'] = $this->request->session()->pull('code_verifier');
        }

        return $fields;
    }

    /**
     * Docs: https://docs.zaloplatforms.com/docs/Social/social-api/tai-lieu/thong-tin-ten-anh-dai-dien
     * Mặc định Zalo trả ảnh nhỏ nhất → xin picture.type(large).
     */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get('https://graph.zalo.me/v2.0/me', [
            RequestOptions::HEADERS => ['access_token' => $token],
            RequestOptions::QUERY => ['fields' => 'id,name,picture.type(large),is_sensitive'],
        ]);

        $data = json_decode((string) $response->getBody(), true) ?: [];
        $error = (int) ($data['error'] ?? 0);

        if ($error === 210) {
            throw new RuntimeException('Tài khoản Zalo đang ẩn thông tin với ứng dụng (mã 210). Vui lòng cho phép chia sẻ tên, ảnh đại diện và thử lại.');
        }

        if ($error !== 0 || empty($data['id'])) {
            throw new RuntimeException('Không lấy được thông tin tài khoản Zalo: '
                .($data['message'] ?? 'lỗi không xác định').($error !== 0 ? ' (mã '.$error.')' : '').'.');
        }

        return $data;
    }

    protected function mapUserToObject(array $user): User
    {
        $avatar = $user['picture']['data']['url'] ?? null;

        return (new User)->setRaw($user)->map([
            'id' => (string) $user['id'],
            'nickname' => null,
            'name' => $user['name'] ?? null,
            'email' => null,
            'avatar' => $avatar ? preg_replace('/^http:/i', 'https:', $avatar) : null,
            // true: người dùng thuộc nhóm cần xử lý dữ liệu theo quy định (trẻ em, người khuyết tật, ...)
            'isSensitive' => filter_var($user['is_sensitive'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
