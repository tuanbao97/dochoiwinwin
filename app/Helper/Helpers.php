<?php

use App\Enum\AppConstant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

    /* Evict forget tag keys data UI-FRONTEND */
    if (!function_exists('evictCacheDataFrontEnd')) {
        function evictCacheDataFrontEnd() {
            $cacheTagDataUI = Cache::get(AppConstant::CACHE_TAG_DATA_UI_FRONTEND, []);
            if (is_array($cacheTagDataUI)) {
                foreach ($cacheTagDataUI as $tagKey) {
                    Cache::forget($tagKey);
                }
            }
            // Prefix thực tế trong DB: [laravel_cache_]CACHE_DATA_UI_FRONTEND_{md5}
            $prefix = AppConstant::CACHE_PREFIX_DATA_UI_FRONTEND;
            try {
                DB::table('cache')->where('key', 'like', '%' . $prefix . '%')->delete();
            } catch (\Throwable) {
                // Ignore nếu store không dùng bảng cache
            }
            Cache::forget(AppConstant::CACHE_TAG_DATA_UI_FRONTEND);
        }
    }

    /* Chuyển đổi string kiểu camelCase sang SNAKE_CASE_UPPER */
    if (!function_exists('camelToSnakeUpper')) {
        function camelToSnakeUpper($string)
        {
            // Chuyển từ camelCase sang snake_case
            $snake = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));

            // Chuyển snake_case thành in hoa
            return strtoupper($snake);
        }
    }

    /* Chuyển đổi String sang kiểu SLUG. VD: Xin chào tôi năm nay 96 tuổi => xin-chao-toi-nam-nay-96-tuoi */
    if (!function_exists('convertStrToSlug')) {
        function convertStrToSlug($string)
        {
            // Chuyển chuỗi sang mã UTF-8 để đảm bảo xử lý đúng các ký tự có dấu
            $string = mb_convert_encoding($string, 'UTF-8', 'auto');

            // Mảng các ký tự có dấu
            $vietnameseChars = array(
                'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
                'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
                'ì','í','ị','ỉ','ĩ',
                'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
                'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
                'ỳ','ý','ỵ','ỷ','ỹ',
                'đ', // Đây là ký tự "đ"
                'À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
                'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
                'Ì','Í','Ị','Ỉ','Ĩ',
                'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
                'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
                'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
                'Đ' // Đây là ký tự "Đ"
            );

            // Mảng các ký tự không dấu tương ứng
            $nonVietnameseChars = array(
                'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
                'e','e','e','e','e','e','e','e','e','e','e',
                'i','i','i','i','i',
                'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
                'u','u','u','u','u','u','u','u','u','u','u',
                'y','y','y','y','y',
                'd', // Ký tự "đ" được thay thế bằng "d"
                'A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
                'E','E','E','E','E','E','E','E','E','E','E',
                'I','I','I','I','I',
                'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
                'U','U','U','U','U','U','U','U','U','U','U',
                'Y','Y','Y','Y','Y',
                'D' // Ký tự "Đ" được thay thế bằng "D"
            );

            // Thay thế các ký tự có dấu thành không dấu
            $string = str_replace($vietnameseChars, $nonVietnameseChars, $string);

            // Loại bỏ các ký tự không phải chữ cái, số hoặc khoảng trắng
            $string = preg_replace('/[^A-Za-z0-9\s]/', '', $string);

            // Thay thế khoảng trắng bằng dấu gạch ngang (-)
            $string = preg_replace('/\s+/', '-', trim($string));

            // Chuyển về chữ thường một cách an toàn với ký tự đa byte
            return mb_strtolower($string, 'UTF-8');
        }
    }

    if (!function_exists('convertImagePathsToAbsolute')) {
        function convertMediaPathsToAbsolute(?string $html = null) {
            if (is_null($html)) return null;
            
            $doc = new \DOMDocument();
            libxml_use_internal_errors(true); // Bỏ qua warning HTML5

            $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

            // Xử lý thẻ <img>
            $images = $doc->getElementsByTagName('img');
            foreach ($images as $img) {
                $src = $img->getAttribute('src');
                if ($src && !preg_match('/^https?:\/\//', $src)) {
                    $img->setAttribute('src', asset($src));
                }
            }

            // Xử lý thẻ <video>
            $videos = $doc->getElementsByTagName('video');
            foreach ($videos as $video) {
                $src = $video->getAttribute('src');
                if ($src && !preg_match('/^https?:\/\//', $src)) {
                    $video->setAttribute('src', asset($src));
                }

                $poster = $video->getAttribute('poster');
                if ($poster && !preg_match('/^https?:\/\//', $poster)) {
                    $video->setAttribute('poster', asset($poster));
                    $newStyle = 'object-fit: cover; width: 100%; max-width: 650px; height: auto; display: block;margin: auto;';
                    $video->setAttribute('style', $newStyle);
                }
            }

            // Xử lý thẻ <source> (nằm trong video/audio)
            $sources = $doc->getElementsByTagName('source');
            foreach ($sources as $source) {
                $src = $source->getAttribute('src');
                if ($src && !preg_match('/^https?:\/\//', $src)) {
                    $source->setAttribute('src', asset($src));
                }
            }

            $body = $doc->getElementsByTagName('body')->item(0);
            if ($body) {
                $inner = '';
                foreach ($body->childNodes as $child) {
                    $inner .= $doc->saveHTML($child);
                }
                return $inner;
            }

            return $doc->saveHTML();
        }
    }

    if (!function_exists('normalizeStorefrontRichHtml')) {
        /**
         * Chuẩn hóa HTML dán từ Facebook/editor: emoji img → unicode.
         */
        function normalizeStorefrontRichHtml(?string $html = null): ?string
        {
            if ($html === null || $html === '') {
                return $html;
            }

            if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $m)) {
                $html = $m[1];
            }

            $html = preg_replace_callback(
                '/<img\b([^>]*)>/iu',
                static function (array $m): string {
                    $tag = $m[0];
                    $attrs = $m[1];
                    $width = preg_match('/\bwidth\s*=\s*["\']?(\d+)/i', $attrs, $w) ? (int) $w[1] : 0;
                    $height = preg_match('/\bheight\s*=\s*["\']?(\d+)/i', $attrs, $h) ? (int) $h[1] : 0;
                    $isEmoji = (bool) preg_match('/emoji\.php|\/emoji\//i', $tag)
                        || ($width > 0 && $width <= 32)
                        || ($height > 0 && $height <= 32);

                    if (! $isEmoji) {
                        return $tag;
                    }

                    if (preg_match('/\balt\s*=\s*["\']([^"\']*)["\']/u', $attrs, $alt)) {
                        return html_entity_decode($alt[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }

                    return '';
                },
                $html
            );

            // Giữ <br>; chỉ gỡ span rỗng sau khi thay emoji
            $html = preg_replace('/<span[^>]*>\s*<\/span>/iu', '', $html);

            // Nếu còn newline thuần giữa chữ (mất break khi lưu TinyMCE) → khôi phục <br>
            if (str_contains($html, "\n") || str_contains($html, "\r")) {
                $html = preg_replace("/\\r\\n|\\r/", "\n", $html);
                // Hai newline trở lên = đoạn trống
                $html = preg_replace("/([^>\\n])\\n{2,}([^<\\n])/u", '$1<br><br>$2', $html);
                // Một newline giữa chữ
                $html = preg_replace("/([^>\\n])\\n([^<\\n])/u", '$1<br>$2', $html);
            }

            return $html;
        }
    }

    if (!function_exists('storefrontParseListingFilters')) {
        /**
         * Parse path segments kiểu: gia/350000-500000/muc-gia/duoi-300k--300-500k/sap-xep/gia-tang/noi-bat/trang/2
         *
         * @return array{
         *   boLoc: string,
         *   giaTu: int|null,
         *   giaDen: int|null,
         *   giaChips: array<int, string>,
         *   page: int,
         *   productHot: bool,
         *   productVip: bool
         * }
         */
        function storefrontParseListingFilters(?string $filters): array
        {
            $out = [
                'boLoc' => 'default',
                'giaTu' => null,
                'giaDen' => null,
                'giaChips' => [],
                'page' => 1,
                'productHot' => false,
                'productVip' => false,
            ];

            $filters = trim((string) $filters, '/');
            if ($filters === '') {
                return $out;
            }

            $parts = array_values(array_filter(explode('/', $filters), static fn ($p) => $p !== ''));
            $i = 0;
            $n = count($parts);
            while ($i < $n) {
                $seg = $parts[$i];
                if ($seg === 'gia' && isset($parts[$i + 1])) {
                    $range = $parts[$i + 1];
                    if (preg_match('/^(\d+)-(up|\d+)$/', $range, $m)) {
                        $out['giaTu'] = (int) $m[1];
                        $out['giaDen'] = $m[2] === 'up' ? null : (int) $m[2];
                    } elseif (preg_match('/^(\d+)$/', $range, $m)) {
                        $out['giaTu'] = (int) $m[1];
                    }
                    $i += 2;
                    continue;
                }
                if ($seg === 'muc-gia' && isset($parts[$i + 1])) {
                    $chips = array_values(array_filter(explode('--', $parts[$i + 1])));
                    $out['giaChips'] = $chips;
                    $i += 2;
                    continue;
                }
                if ($seg === 'sap-xep' && isset($parts[$i + 1])) {
                    $out['boLoc'] = $parts[$i + 1];
                    $i += 2;
                    continue;
                }
                if ($seg === 'trang' && isset($parts[$i + 1]) && ctype_digit($parts[$i + 1])) {
                    $out['page'] = max(1, (int) $parts[$i + 1]);
                    $i += 2;
                    continue;
                }
                if ($seg === 'noi-bat' || $seg === 'hot') {
                    $out['productHot'] = true;
                    $i += 1;
                    continue;
                }
                if ($seg === 'vip') {
                    $out['productVip'] = true;
                    $i += 1;
                    continue;
                }
                $i += 1;
            }

            return $out;
        }
    }

    if (!function_exists('storefrontBuildListingFiltersPath')) {
        /**
         * @param  array{
         *   boLoc?: string,
         *   giaTu?: int|null,
         *   giaDen?: int|null,
         *   giaChips?: array<int, string>,
         *   page?: int,
         *   productHot?: bool,
         *   productVip?: bool
         * }  $opts
         */
        function storefrontBuildListingFiltersPath(array $opts = []): string
        {
            $parts = [];

            $giaTu = array_key_exists('giaTu', $opts) && $opts['giaTu'] !== null && $opts['giaTu'] !== ''
                ? (int) $opts['giaTu']
                : null;
            $giaDen = array_key_exists('giaDen', $opts) && $opts['giaDen'] !== null && $opts['giaDen'] !== ''
                ? (int) $opts['giaDen']
                : null;
            if ($giaTu !== null || $giaDen !== null) {
                $min = $giaTu ?? 0;
                $maxPart = $giaDen !== null ? (string) $giaDen : 'up';
                $parts[] = 'gia';
                $parts[] = $min . '-' . $maxPart;
            }

            $chips = array_values(array_filter($opts['giaChips'] ?? [], static fn ($c) => is_string($c) && $c !== ''));
            if ($chips !== []) {
                $parts[] = 'muc-gia';
                $parts[] = implode('--', $chips);
            }

            $boLoc = (string) ($opts['boLoc'] ?? 'default');
            if ($boLoc !== '' && $boLoc !== 'default') {
                $parts[] = 'sap-xep';
                $parts[] = $boLoc;
            }

            if (! empty($opts['productHot'])) {
                $parts[] = 'noi-bat';
            }
            if (! empty($opts['productVip'])) {
                $parts[] = 'vip';
            }

            $page = max(1, (int) ($opts['page'] ?? 1));
            if ($page > 1) {
                $parts[] = 'trang';
                $parts[] = (string) $page;
            }

            return $parts === [] ? '' : implode('/', $parts);
        }
    }

    if (!function_exists('storefrontListingUrl')) {
        /**
         * URL listing storefront dạng path (không query).
         *
         * @param  array{
         *   mode?: 'category'|'search'|'vip'|'hot'|'all',
         *   categoryKey?: string,
         *   query?: string,
         *   boLoc?: string,
         *   giaTu?: int|null,
         *   giaDen?: int|null,
         *   giaChips?: array<int, string>,
         *   page?: int,
         *   productHot?: bool,
         *   productVip?: bool
         * }  $opts
         */
        function storefrontListingUrl(array $opts = []): string
        {
            $mode = (string) ($opts['mode'] ?? '');
            $categoryKey = trim((string) ($opts['categoryKey'] ?? ''));
            $query = trim((string) ($opts['query'] ?? ''));
            $productVip = ! empty($opts['productVip']);
            $productHot = ! empty($opts['productHot']);

            if ($mode === '') {
                if ($productVip && $categoryKey === '' && $query === '') {
                    $mode = 'vip';
                } elseif ($productHot && $categoryKey === '' && $query === '') {
                    $mode = 'hot';
                } elseif ($categoryKey !== '') {
                    $mode = 'category';
                } elseif ($query !== '') {
                    $mode = 'search';
                } else {
                    $mode = 'all';
                }
            }

            $filterOpts = $opts;
            // Flag vip/hot trên segment khi đã có base riêng thì không lặp
            if ($mode === 'vip') {
                unset($filterOpts['productVip']);
            }
            if ($mode === 'hot') {
                unset($filterOpts['productHot']);
            }
            // productHot kèm danh mục → giữ segment noi-bat
            if ($mode === 'category' && $productHot) {
                $filterOpts['productHot'] = true;
            } elseif ($mode !== 'category') {
                unset($filterOpts['productHot']);
            }

            $filters = storefrontBuildListingFiltersPath($filterOpts);

            $base = match ($mode) {
                'vip' => '/san-pham-vip',
                'hot' => '/san-pham-noi-bat',
                'category' => '/danh-muc/' . rawurlencode($categoryKey !== '' ? $categoryKey : 'danh-muc-0'),
                'search' => '/tim-kiem/' . rawurlencode($query !== '' ? $query : '-'),
                default => '/tat-ca-san-pham',
            };

            $path = $filters !== '' ? rtrim($base, '/') . '/' . $filters : $base;

            return url($path);
        }
    }

    if (!function_exists('storefrontProductCategoryUrl')) {
        function storefrontProductCategoryUrl(int $categoryId, ?string $name = null): string
        {
            if ($categoryId <= 0) {
                return storefrontListingUrl(['mode' => 'all']);
            }

            if ($name === null || $name === '') {
                try {
                    $name = (string) DB::table('category_p')
                        ->where('ID', $categoryId)
                        ->where('STATUS', AppConstant::STATUS_USING)
                        ->value('NAME');
                } catch (\Throwable) {
                    $name = 'danh-muc';
                }
            }

            $slug = convertStrToSlug($name ?: 'danh-muc');

            return storefrontListingUrl([
                'mode' => 'category',
                'categoryKey' => $slug . '-' . $categoryId,
            ]);
        }
    }

    if (!function_exists('storefrontProductCategoryUrlMap')) {
        /**
         * @return array<string, string>
         */
        function storefrontProductCategoryUrlMap(): array
        {
            static $map = null;
            if (is_array($map)) {
                return $map;
            }

            $map = [];
            try {
                $rows = DB::table('category_p')
                    ->where('STATUS', AppConstant::STATUS_USING)
                    ->where('IS_ACTIVE', true)
                    ->get(['ID', 'NAME', 'ATTR1']);

                foreach ($rows as $row) {
                    $title = (string) $row->NAME;
                    $external = trim((string) ($row->ATTR1 ?? ''));
                    if ($external !== '' && str_starts_with($external, 'http')) {
                        $map[$title] = $external;
                        continue;
                    }

                    $map[$title] = storefrontProductCategoryUrl((int) $row->ID, $title);
                }
            } catch (\Throwable) {
                $map = [];
            }

            return $map;
        }
    }

    if (!function_exists('storefrontCategoryIconUrl')) {
        /** Icon minh hoạ danh mục sản phẩm, suy ra từ tên danh mục. */
        function storefrontCategoryIconUrl(?string $name): string
        {
            $n = mb_strtolower(trim((string) $name));

            $file = match (true) {
                str_contains($n, 'bé gái') || str_contains($n, 'be gai') => 'do-choi-be-gai.svg',
                str_contains($n, 'vận động') || str_contains($n, 'van dong') => 'do-choi-van-dong.svg',
                str_contains($n, 'máy bay') || str_contains($n, 'may bay') => 'may-bay-dieu-khien.svg',
                str_starts_with($n, 'xe ') => 'xe-dieu-khien.svg',
                str_contains($n, 'siêu xe') || str_contains($n, 'sieu xe') => 'lap-ghep-xe-dua.svg',
                str_contains($n, 'siêu anh hùng') || str_contains($n, 'sieu anh hung') => 'lap-ghep-sieu-anh-hung.svg',
                str_contains($n, 'con vật') || str_contains($n, 'con vat') => 'lap-ghep-con-vat.svg',
                str_contains($n, 'tổng hợp') || str_contains($n, 'tong hop') => 'lap-ghep-tong-hop.svg',
                str_contains($n, 'vui nhộn') || str_contains($n, 'vui nhon') => 'do-choi-vui-nhon.svg',
                str_contains($n, 'lắp ghép') || str_contains($n, 'lap ghep') || str_contains($n, 'lego') => 'do-choi-lap-ghep.svg',
                str_contains($n, 'mô hình') || str_contains($n, 'mo hinh') => 'do-choi-mo-hinh.svg',
                str_contains($n, 'nước') || str_contains($n, 'nuoc') => 'do-choi-nuoc.svg',
                str_contains($n, 'giáo dục') || str_contains($n, 'giao duc') => 'do-choi-giao-duc.svg',
                default => 'do-choi-dieu-khien.svg',
            };

            return asset('UI-FRONTEND/assets/ww-menu-icons/' . $file);
        }
    }

    if (!function_exists('storefrontMenuLabel')) {
        /**
         * Nối các cụm từ khoá bằng non-breaking space để menu không ngắt dòng giữa cụm,
         * ví dụ "siêu xe" hoặc "điều khiển" luôn nằm trọn trên một dòng.
         */
        function storefrontMenuLabel(?string $name): string
        {
            $text = trim((string) $name);
            if ($text === '') {
                return '';
            }

            $phrases = [
                'siêu anh hùng', 'siêu nhân', 'siêu xe', 'xe đua', 'máy bay', 'điều khiển',
                'lắp ghép', 'xếp hình', 'tổng hợp', 'con vật', 'đồ chơi', 'bé gái',
                'vui nhộn', 'vận động', 'giáo dục', 'mô hình', 'trẻ em',
            ];

            foreach ($phrases as $phrase) {
                $pattern = '/'.implode('[\s\x{00A0}]+', array_map(
                    static fn ($word) => preg_quote($word, '/'),
                    explode(' ', $phrase)
                )).'/iu';

                $text = preg_replace_callback(
                    $pattern,
                    static fn (array $m) => preg_replace('/[\s\x{00A0}]+/u', "\u{00A0}", $m[0]),
                    $text
                ) ?? $text;
            }

            return $text;
        }
    }

    if (!function_exists('storefrontGioQuaPriceRanges')) {
        /**
         * Khoảng giá Giỏ trái cây:
         * - dưới 500k: PRICE < 500000
         * - từ 500k đến 700k: PRICE >= 500000 và PRICE <= 700000
         * - trên 700k: PRICE > 700000
         *
         * @return array<int, array{id: string, label: string, min: int|null, max: int|null, minInclusive: bool, maxInclusive: bool}>
         */
        function storefrontGioQuaPriceRanges(): array
        {
            return [
                [
                    'id' => 'duoi-500k',
                    'label' => 'Giỏ trái cây dưới 500k',
                    'min' => null,
                    'max' => 500000,
                    'minInclusive' => true,
                    'maxInclusive' => false,
                ],
                [
                    'id' => '500-700k',
                    'label' => 'Giỏ trái cây từ 500k đến 700k',
                    'min' => 500000,
                    'max' => 700000,
                    'minInclusive' => true,
                    'maxInclusive' => true,
                ],
                [
                    'id' => 'tren-700k',
                    'label' => 'Giỏ trái cây trên 700k',
                    'min' => 700000,
                    'max' => null,
                    'minInclusive' => false,
                    'maxInclusive' => true,
                ],
            ];
        }
    }

    if (!function_exists('storefrontGioQuaPriceSearchUrl')) {
        function storefrontGioQuaPriceSearchUrl(string $chipId): string
        {
            return storefrontListingUrl([
                'mode' => 'category',
                'categoryKey' => 'gio-qua-trai-cay-1004',
                'giaChips' => [$chipId],
            ]);
        }
    }

    if (!function_exists('storefrontGioQuaPriceUrlMap')) {
        /**
         * @return array<string, string>
         */
        function storefrontGioQuaPriceUrlMap(): array
        {
            $map = [];
            foreach (storefrontGioQuaPriceRanges() as $range) {
                $map[$range['label']] = storefrontGioQuaPriceSearchUrl((string) $range['id']);
            }

            return $map;
        }
    }

    if (!function_exists('storefrontNewsCategoryUrl')) {
        function storefrontNewsCategoryUrl(string $slug, int $categoryId, int $page = 1): string
        {
            $key = trim($slug . '-' . $categoryId, '-');
            $path = '/tin-tuc/danh-muc/' . rawurlencode($key);
            if ($page > 1) {
                $path .= '/trang/' . $page;
            }

            return url($path);
        }
    }

    if (!function_exists('storefrontNewsListUrl')) {
        function storefrontNewsListUrl(int $page = 1, ?string $categoryKey = null): string
        {
            if ($categoryKey) {
                $path = '/tin-tuc/danh-muc/' . rawurlencode($categoryKey);
                if ($page > 1) {
                    $path .= '/trang/' . $page;
                }

                return url($path);
            }

            return $page > 1 ? url('/tin-tuc/trang/' . $page) : url('/tin-tuc');
        }
    }

    if (!function_exists('storefrontVideoListUrl')) {
        function storefrontVideoListUrl(int $page = 1): string
        {
            return $page > 1 ? url('/video/trang/' . $page) : url('/video');
        }
    }

    /**
     * Chuẩn hóa UPD_DT → timestamp dùng cho ?upd_time= (cache bust ảnh).
     */
    if (!function_exists('storefrontImageUpdTime')) {
        function storefrontImageUpdTime(mixed $updDt): ?string
        {
            if ($updDt === null || $updDt === '') {
                return null;
            }

            if (is_numeric($updDt)) {
                return (string) (int) $updDt;
            }

            $raw = trim((string) $updDt);
            $ts = strtotime($raw);
            if ($ts !== false) {
                return (string) $ts;
            }

            $digits = preg_replace('/\D+/', '', $raw);

            return $digits !== '' ? $digits : null;
        }
    }

    /**
     * URL ảnh storefront + ?upd_time= từ UPD_DT (bust cache khi BE lưu).
     */
    if (!function_exists('storefrontImageUrl')) {
        function storefrontImageUrl(?string $path, mixed $updDt = null): string
        {
            if ($path === null || $path === '') {
                return '';
            }

            $path = str_replace('\\', '/', $path);
            if (preg_match('#^https?://#i', $path)) {
                $url = $path;
            } else {
                $url = asset(ltrim($path, '/'));
            }

            $bust = storefrontImageUpdTime($updDt);
            if ($bust === null) {
                return $url;
            }

            return $url . (str_contains($url, '?') ? '&' : '?') . 'upd_time=' . rawurlencode($bust);
        }
    }

    /**
     * Thông tin cửa hàng (SETTING_WEB) cho storefront — footer, liên hệ, v.v.
     *
     * @return array{
     *   storeName: string,
     *   email: string,
     *   taxCode: string,
     *   workingHours: string,
     *   description: string,
     *   address: string,
     *   mapUrl: string,
     *   zaloUrl: string,
     *   zaloPageUrl: string,
     *   messengerUrl: string,
     *   facebookUrl: string,
     *   websiteUrl: string,
     *   tiktokUrl: string,
     *   youtubeUrl: string,
     *   commitmentText: string,
     *   hotline: ?array{display: string, tel: string, raw: string, owner: string},
     *   hotlines: list<array{display: string, tel: string, raw: string, owner: string}>
     * }
     */
    if (!function_exists('wwWebContact')) {
        function wwWebContact(): array
        {
            static $cached = null;
            if ($cached !== null) {
                return $cached;
            }

            $rows = \App\Models\Setting::query()
                ->where('TYPE', 'SETTING_WEB')
                ->where('IS_ACTIVE', true)
                ->where('STATUS', \App\Enum\AppConstant::STATUS_USING)
                ->orderBy('ORDER')
                ->get(['CODE', 'NAME', 'VALUE', 'ORDER']);

            $val = static function (string $code) use ($rows): string {
                $item = $rows->first(static fn ($r) => strtoupper((string) $r->CODE) === strtoupper($code));
                return $item && $item->VALUE !== null ? trim((string) $item->VALUE) : '';
            };

            $formatPhone = static function (string $phone): string {
                $digits = preg_replace('/\D+/', '', $phone) ?? '';
                if (strlen($digits) <= 7) {
                    return $digits;
                }
                return substr($digits, 0, 4) . ' ' . substr($digits, 4, 3) . ' ' . substr($digits, 7);
            };

            $hotlines = $rows
                ->filter(static function ($r) {
                    return str_starts_with((string) $r->CODE, 'SETTING_HOTLINE')
                        && filled($r->VALUE);
                })
                ->sortBy(static fn ($r) => $r->ORDER === null ? 9999 : (int) $r->ORDER)
                ->values()
                ->map(static function ($r) use ($formatPhone) {
                    $parts = explode('|', (string) $r->VALUE);
                    if (count($parts) < 2) {
                        return null;
                    }
                    $raw = preg_replace('/\D+/', '', $parts[1]) ?? '';
                    if ($raw === '') {
                        return null;
                    }
                    return [
                        'display' => $formatPhone($raw),
                        'tel' => 'tel:' . $raw,
                        'raw' => $raw,
                        'owner' => $parts[2] ?? '',
                        'type' => $parts[0] ?? '',
                    ];
                })
                ->filter()
                ->values()
                ->all();

            $cached = [
                'storeName' => $val('SETTING_TEN_CUA_HANG') ?: 'Win Win Trái Cây Nhập Khẩu',
                'email' => $val('SETTING_EMAIL'),
                'taxCode' => $val('SETTING_MA_SO_THUE'),
                'workingHours' => $val('SETTING_THOI_GIAN_LAM_VIEC'),
                'description' => $val('SETTING_MO_TA_CUA_HANG'),
                'address' => $val('SETTING_DIA_CHI_CUA_HANG'),
                'mapUrl' => $val('SETTING_DUONG_DAN_GG_MAP_CUA_HANG'),
                'zaloUrl' => $val('SETTING_DUONG_DAN_SO_ZALO_CUA_HANG'),
                'zaloPageUrl' => $val('SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG'),
                'messengerUrl' => $val('SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG'),
                'facebookUrl' => $val('SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG'),
                'websiteUrl' => $val('SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG'),
                'tiktokUrl' => $val('SETTING_DUONG_DAN_TIKTOK_CUA_HANG'),
                'youtubeUrl' => $val('SETTING_DUONG_DAN_YOUTUBE_CUA_HANG'),
                'commitmentText' => $val('SETTING_CAM_KET_BAN_HANG_ONLY_TEXT'),
                'hotline' => $hotlines[0] ?? null,
                'hotlines' => $hotlines,
            ];

            return $cached;
        }
    }

    /**
     * Đảm bảo URL tuyệt đối (Facebook / crawler cần absolute https).
     */
    if (!function_exists('storefrontAbsoluteUrl')) {
        function storefrontAbsoluteUrl(?string $url): string
        {
            if ($url === null || $url === '') {
                return '';
            }

            if (preg_match('#^https?://#i', $url)) {
                return $url;
            }

            if (str_starts_with($url, '//')) {
                return 'https:' . $url;
            }

            return url('/' . ltrim($url, '/'));
        }
    }

    /**
     * Ảnh mặc định khi share Facebook / Zalo / OG.
     * Ảnh lưới sản phẩm (giỏ trái cây) — dùng cho trang chủ & danh sách.
     */
    if (!function_exists('storefrontDefaultShareImageUrl')) {
        function storefrontDefaultShareImageUrl(): string
        {
            $url = storefrontAbsoluteUrl(asset('UI-FRONTEND/images/og-share-listing.png'));
            $version = '20260718d';

            return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $version;
        }
    }

    /**
     * Facebook App ID cho meta fb:app_id (Sharing Debugger / Insights).
     */
    if (!function_exists('storefrontFacebookAppId')) {
        function storefrontFacebookAppId(): string
        {
            return trim((string) config('services.facebook.app_id', ''));
        }
    }

    /**
     * Favicon / icon site (logo Win Win).
     */
    if (!function_exists('storefrontFaviconUrl')) {
        function storefrontFaviconUrl(): string
        {
            return storefrontAbsoluteUrl(asset('UI-FRONTEND/images/logo-win-win-tron.png'));
        }
    }

    /**
     * URL ảnh từ 1 item document storage (PATH hoặc DIRECTORY/NAME).
     *
     * @param  array<string, mixed>|null  $media
     */
    if (!function_exists('storefrontMediaImageUrl')) {
        function storefrontMediaImageUrl(?array $media, mixed $parentUpd = null, bool $preferOriginal = true): string
        {
            if ($media === null || $media === []) {
                return '';
            }

            $bust = $parentUpd ?? ($media['UPD_DT'] ?? null);

            if (! empty($media['PATH'])) {
                return storefrontAbsoluteUrl(storefrontImageUrl((string) $media['PATH'], $bust));
            }

            $name = (string) ($media['IMAGE_THUMNAIL'] ?? $media['NAME'] ?? '');
            $dir = trim((string) ($media['DIRECTORY'] ?? ''), '/');
            if ($dir === '' || $name === '') {
                return '';
            }

            $rel = $preferOriginal
                ? ($dir . '/' . $name)
                : ($dir . '/' . ((string) ($media['ASPECT_RATIO'] ?? '1x1')) . '_' . $name);

            return storefrontAbsoluteUrl(storefrontImageUrl($rel, $bust));
        }
    }

    /**
     * Meta SEO/OG mặc định + override từng trang.
     *
     * @param  array{
     *   title?: string,
     *   description?: string,
     *   image?: string,
     *   type?: string,
     *   url?: string
     * }  $overrides
     * @return array{title: string, description: string, image: string, type: string, url: string, siteName: string}
     */
    if (!function_exists('storefrontSeo')) {
        function storefrontSeo(array $overrides = []): array
        {
            $ww = wwWebContact();
            $siteName = $ww['storeName'] !== '' ? $ww['storeName'] : 'Win Win Trái Cây Nhập Khẩu';
            $defaultDesc = $ww['description'] !== ''
                ? $ww['description']
                : 'Win Win Trái Cây Nhập Khẩu — trái cây tươi, giỏ quà và quà tặng: giao nhanh, nhiều set combo, phù hợp biếu tặng và tiệc.';

            $title = trim((string) ($overrides['title'] ?? ''));
            $description = trim((string) ($overrides['description'] ?? ''));
            $image = trim((string) ($overrides['image'] ?? ''));
            $type = trim((string) ($overrides['type'] ?? ''));
            $url = trim((string) ($overrides['url'] ?? ''));

            if ($description !== '') {
                $description = preg_replace('/\s+/u', ' ', strip_tags($description)) ?? $description;
                $description = mb_substr($description, 0, 300);
            }

            return [
                'title' => $title !== '' ? $title : $siteName,
                'description' => $description !== '' ? $description : $defaultDesc,
                'image' => $image !== '' ? storefrontAbsoluteUrl($image) : storefrontDefaultShareImageUrl(),
                'type' => $type !== '' ? $type : 'website',
                'url' => $url !== '' ? storefrontAbsoluteUrl($url) : url()->current(),
                'siteName' => $siteName,
            ];
        }
    }

?>
