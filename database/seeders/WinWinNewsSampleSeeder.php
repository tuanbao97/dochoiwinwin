<?php

namespace Database\Seeders;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WinWinNewsSampleSeeder extends Seeder
{
    private string $uploadDir;

    private int $nextDocId = 1;

    private int $nextNewsId = 1;

    public function run(): void
    {
        $this->uploadDir = 'upload/UI-BACKEND/' . date('Y-m-d');
        $publicDir = public_path($this->uploadDir);
        if (! is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        $this->nextDocId = (int) DB::table('document_storage')->max('ID') + 1;
        $this->nextNewsId = 1;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (Schema::hasTable('news_document_storage')) {
            DB::table('news_document_storage')->truncate();
        }
        if (Schema::hasTable('news_category')) {
            DB::table('news_category')->truncate();
        }
        if (Schema::hasTable('news')) {
            DB::table('news')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $articles = $this->articles();
        $now = now();
        $adminId = AuthConstant::USER_SUPER_ADMIN_ID;
        $adminName = AuthConstant::USER_SUPER_ADMIN_FULL_NAME;

        foreach ($articles as $index => $article) {
            $imageMeta = $this->storeImage($article['image'], $article['image_label']);
            $newsId = $this->nextNewsId++;

            DB::table('news')->insert([
                'ID' => $newsId,
                'TITLE' => $article['title'],
                'SUMMARY' => Str::limit($article['summary'], 500, ''),
                'CONTENT_FORMAT' => $article['html'],
                'CONTENT_RAW' => $article['text'],
                'META_SEO_KEYWORDS' => $article['keywords'],
                'META_SEO_DESCRIPTION' => Str::limit($article['summary'], 1000, ''),
                'APPROVED_DATE' => $now,
                'PUBLISHED_DATE' => $now->copy()->subDays(count($articles) - $index),
                'IS_HOT_NEWS' => $article['hot'],
                'COUNT_VIEWS' => random_int(50, 800),
                'IS_APPROVED' => true,
                'USER_POST_NEWS_ID' => $adminId,
                'USER_APPROVED_POST_NEWS_ID' => $adminId,
                'CRT_DT' => $now,
                'UPD_DT' => $now,
                'CRT_ID' => $adminId,
                'UPD_ID' => $adminId,
                'CRT_NAME' => $adminName,
                'UPD_NAME' => $adminName,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
                'ATTR49' => AppConstant::TYPE_NEWS_COMMON,
            ]);

            DB::table('news_category')->insert([
                'NEWS_ID' => $newsId,
                'CATEGORY_ID' => $article['category_id'],
                'SORT_ORDER' => 0,
                'CRT_DT' => $now,
                'UPD_DT' => $now,
                'CRT_ID' => $adminId,
                'UPD_ID' => $adminId,
                'CRT_NAME' => $adminName,
                'UPD_NAME' => $adminName,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
            ]);

            DB::table('news_document_storage')->insert([
                'NEWS_ID' => $newsId,
                'DOCUMENT_STORAGE_ID' => $imageMeta['id'],
                'SORT_ORDER' => 0,
                'IS_THUMNAIL' => true,
                'TYPE' => 'image',
                'EXTENSION' => $imageMeta['extension'],
                'CRT_DT' => $now,
                'UPD_DT' => $now,
                'CRT_ID' => $adminId,
                'UPD_ID' => $adminId,
                'CRT_NAME' => $adminName,
                'UPD_NAME' => $adminName,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
                'ATTR1' => 'DANH_SACH_HINH_ANH_DAI_DIEN',
                'ATTR2' => '1x1',
            ]);
        }

        $this->command?->info('Đã seed ' . count($articles) . ' bài tin tức Đồ Chơi Win Win kèm hình ảnh.');
    }

    private function storeImage(string $source, string $label): array
    {
        if (! is_file($source)) {
            throw new \RuntimeException('Thiếu ảnh seed: ' . $source);
        }

        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION)) ?: 'png';
        $hashName = Str::lower(Str::random(40)) . '.' . $extension;
        $targetDir = public_path($this->uploadDir);
        $target = $targetDir . DIRECTORY_SEPARATOR . $hashName;
        if (! copy($source, $target)) {
            throw new \RuntimeException('Không copy được ảnh: ' . $label);
        }
        copy($target, $targetDir . DIRECTORY_SEPARATOR . '1x1_' . $hashName);

        $id = $this->nextDocId++;
        $now = now();
        $adminId = AuthConstant::USER_SUPER_ADMIN_ID;
        $adminName = AuthConstant::USER_SUPER_ADMIN_FULL_NAME;

        DB::table('document_storage')->insert([
            'ID' => $id,
            'NAME' => $hashName,
            'ORIGINAL_NAME' => Str::slug($label) . '.' . $extension,
            'EXTENSION' => $extension,
            'PATH' => $this->uploadDir . '/' . $hashName,
            'DIRECTORY' => $this->uploadDir,
            'SIZE' => filesize($target) ?: 0,
            'MD5' => md5_file($target),
            'TYPE_FILE' => 'image',
            'DESCRIPTION' => $label,
            'CRT_DT' => $now,
            'UPD_DT' => $now,
            'CRT_ID' => $adminId,
            'UPD_ID' => $adminId,
            'CRT_NAME' => $adminName,
            'UPD_NAME' => $adminName,
            'STATUS' => AppConstant::STATUS_USING,
            'IS_ACTIVE' => true,
        ]);

        return ['id' => $id, 'extension' => $extension];
    }

    private function articles(): array
    {
        $newsImages = public_path('UI-FRONTEND/images/news');

        return [
            $this->article(1, 'Chọn đồ chơi phù hợp theo độ tuổi của bé', $newsImages . '/chon-do-choi-theo-do-tuoi.jpg',
                'Mỗi giai đoạn phát triển cần loại đồ chơi có kích thước, độ khó và kỹ năng phù hợp.',
                '<p>Khi chọn đồ chơi, phụ huynh nên ưu tiên <strong>độ tuổi khuyến nghị, kích thước chi tiết và kỹ năng mà sản phẩm hỗ trợ</strong>.</p><p>Trẻ nhỏ phù hợp với đồ chơi màu sắc rõ, thao tác đơn giản và không có chi tiết dễ nuốt. Trẻ lớn hơn có thể làm quen với lắp ghép, mô hình, điều khiển hoặc trò chơi tư duy có thử thách.</p><p>Đồ Chơi Win Win luôn sẵn sàng tư vấn sản phẩm phù hợp với độ tuổi và sở thích của bé.</p>',
                'chọn đồ chơi theo độ tuổi, đồ chơi an toàn, Đồ Chơi Win Win'),
            $this->article(1, 'Đồ chơi lắp ghép giúp bé phát triển tư duy như thế nào?', $newsImages . '/lap-ghep-phat-trien-tu-duy.jpg',
                'Lắp ghép giúp rèn khả năng quan sát, phối hợp tay mắt, tư duy không gian và tính kiên trì.',
                '<p>Đồ chơi lắp ghép khuyến khích bé quan sát, thử nghiệm và tự sửa khi mô hình chưa đúng. Quá trình này giúp phát triển <strong>tư duy không gian, khả năng giải quyết vấn đề và sự kiên trì</strong>.</p><p>Phụ huynh nên bắt đầu bằng bộ ít chi tiết, sau đó tăng dần độ khó theo kỹ năng của bé. Luôn kiểm tra cảnh báo về chi tiết nhỏ trước khi sử dụng.</p>',
                'đồ chơi lắp ghép, phát triển tư duy, đồ chơi giáo dục'),
            $this->article(1, 'Hướng dẫn chơi xe điều khiển an toàn', $newsImages . '/xe-dieu-khien-an-toan.jpg',
                'Chọn khu vực rộng, kiểm tra pin và hướng dẫn bé điều khiển đúng cách trước khi chơi.',
                '<p>Trước khi chơi xe điều khiển, hãy kiểm tra pin, bánh xe và bộ điều khiển. Nên chọn khu vực bằng phẳng, tránh đường giao thông, cầu thang và nơi có nước.</p><p>Không sạc pin qua đêm hoặc dùng bộ sạc không đúng thông số. Với trẻ nhỏ, người lớn nên hướng dẫn và giám sát trong những lần chơi đầu tiên.</p>',
                'xe điều khiển, đồ chơi điều khiển, an toàn cho bé'),
            $this->article(1, 'Đồ chơi vận động và lợi ích cho sức khỏe của bé', $newsImages . '/do-choi-van-dong.jpg',
                'Đồ chơi vận động giúp bé tăng phối hợp cơ thể, phản xạ và hứng thú hoạt động ngoài trời.',
                '<p>Bóng, bộ ném vòng, cầu trượt mini và các trò chơi vận động giúp bé rèn <strong>thăng bằng, phản xạ và khả năng phối hợp</strong>.</p><p>Hãy chọn sản phẩm đúng độ tuổi, không gian sử dụng và luôn kiểm tra độ chắc chắn trước khi cho bé chơi.</p>',
                'đồ chơi vận động, sức khỏe trẻ em, hoạt động ngoài trời'),
            $this->article(1, 'Cách vệ sinh và bảo quản đồ chơi đúng cách', $newsImages . '/ve-sinh-bao-quan-do-choi.jpg',
                'Vệ sinh theo chất liệu giúp đồ chơi bền hơn và bảo đảm an toàn trong quá trình sử dụng.',
                '<p>Đồ chơi nhựa có thể lau bằng khăn ẩm và dung dịch dịu nhẹ; đồ chơi điện tử chỉ nên lau bề mặt, tránh để nước vào mạch. Thú bông cần làm theo hướng dẫn giặt trên nhãn.</p><p>Tháo pin khi không sử dụng lâu ngày, cất chi tiết nhỏ vào hộp và kiểm tra định kỳ các cạnh sắc hoặc bộ phận lỏng.</p>',
                'vệ sinh đồ chơi, bảo quản đồ chơi, an toàn trẻ em'),

            $this->article(2, 'Gợi ý quà sinh nhật theo sở thích của bé', $newsImages . '/qua-sinh-nhat-cho-be.jpg',
                'Chọn quà theo sở thích giúp bé hào hứng và sử dụng món đồ chơi lâu dài hơn.',
                '<p>Với bé thích phương tiện, có thể chọn xe điều khiển hoặc mô hình. Bé yêu sáng tạo thường phù hợp với bộ lắp ghép, xếp hình hoặc đồ chơi nhập vai.</p><p>Ngoài sở thích, hãy lưu ý độ tuổi, không gian chơi và mức độ hỗ trợ cần thiết từ người lớn.</p>',
                'quà sinh nhật cho bé, quà tặng đồ chơi, Đồ Chơi Win Win'),
            $this->article(2, 'Quà Quốc tế Thiếu nhi 1/6 vui nhộn và ý nghĩa', $newsImages . '/qua-quoc-te-thieu-nhi.jpg',
                'Một món đồ chơi phù hợp vừa mang lại niềm vui vừa khuyến khích bé khám phá kỹ năng mới.',
                '<p>Dịp 1/6 là cơ hội để gia đình dành thời gian chơi cùng bé. Những bộ trò chơi tương tác, lắp ghép hoặc vận động phù hợp để cả nhà cùng tham gia.</p><p>Win Win hỗ trợ tư vấn và đóng gói quà theo độ tuổi, sở thích và ngân sách.</p>',
                'quà 1/6, quà Quốc tế Thiếu nhi, đồ chơi vui nhộn'),
            $this->article(2, 'Quà cho bé mê ô tô, máy bay và phương tiện', $newsImages . '/qua-xe-may-bay.jpg',
                'Xe mô hình, xe điều khiển và máy bay là những lựa chọn hấp dẫn cho bé yêu phương tiện.',
                '<p>Có thể bắt đầu với mô hình kéo thả cho bé nhỏ, sau đó chuyển sang xe điều khiển hoặc bộ lắp ghép phương tiện khi bé lớn hơn.</p><p>Kiểm tra phạm vi điều khiển, loại pin và không gian chơi để chọn sản phẩm phù hợp.</p>',
                'quà cho bé mê xe, máy bay điều khiển, xe mô hình'),
            $this->article(2, 'Quà tặng khơi gợi sáng tạo cho bé', $newsImages . '/qua-tang-sang-tao.jpg',
                'Bộ lắp ghép, xếp hình và nhập vai giúp bé tạo ra câu chuyện và sản phẩm của riêng mình.',
                '<p>Quà tặng sáng tạo không giới hạn bé vào một cách chơi duy nhất. Các bộ lắp ghép mở, xếp hình và đồ chơi nhập vai giúp bé tưởng tượng, kể chuyện và hợp tác với bạn bè.</p>',
                'quà tặng sáng tạo, đồ chơi lắp ghép, đồ chơi nhập vai'),
            $this->article(2, 'Chọn combo đồ chơi cho anh chị em cùng chơi', $newsImages . '/do-choi-anh-chi-em.jpg',
                'Combo phù hợp giúp các bé chia sẻ, phối hợp và học cách chơi cùng nhau.',
                '<p>Hãy chọn bộ có nhiều vai trò hoặc đủ chi tiết để các bé cùng tham gia, chẳng hạn đường đua, bộ lắp ghép lớn hoặc trò chơi vận động theo nhóm.</p><p>Ưu tiên sản phẩm phù hợp với độ tuổi của bé nhỏ nhất trong nhóm.</p>',
                'combo đồ chơi, đồ chơi nhóm, quà cho anh chị em'),

            $this->article(3, 'Đồ Chơi Win Win — cửa hàng đồ chơi tại Hòa Tiến', $newsImages . '/cua-hang-do-choi.jpg',
                'Đồ Chơi Win Win cung cấp sản phẩm an toàn, chính hãng và đa dạng tại Hòa Tiến, Đà Nẵng.',
                '<p><strong>Đồ Chơi Win Win</strong> chuyên đồ chơi điều khiển, lắp ghép, giáo dục, vận động, mô hình, đồ chơi bé gái và nhiều sản phẩm quà tặng.</p><p><strong>Địa chỉ:</strong> Đường DT605, xã Hòa Tiến, Đà Nẵng.<br><strong>Hotline:</strong> 0905 454 775 · 0905 09 09 10<br><strong>Email:</strong> dochoiwinwin@gmail.com</p>',
                'Đồ Chơi Win Win, cửa hàng đồ chơi Đà Nẵng, Hòa Tiến'),
            $this->article(3, 'Cam kết nguồn gốc và chất lượng sản phẩm tại Win Win', $newsImages . '/chat-luong-do-choi.jpg',
                'Win Win ưu tiên sản phẩm có thông tin nguồn gốc, cảnh báo độ tuổi và hướng dẫn sử dụng rõ ràng.',
                '<p>Sản phẩm được kiểm tra bao bì, phụ kiện và tình trạng hoạt động trước khi giao. Thông tin độ tuổi, chất liệu và hướng dẫn sử dụng được tư vấn rõ để phụ huynh lựa chọn phù hợp.</p>',
                'đồ chơi chính hãng, nguồn gốc đồ chơi, chất lượng Win Win'),
            $this->article(3, 'Giao đồ chơi tại Đà Nẵng — cách đặt hàng', $newsImages . '/giao-do-choi.jpg',
                'Win Win hỗ trợ giao hàng tại Đà Nẵng với thời gian linh hoạt và xác nhận rõ trước khi gửi.',
                '<p>Khách hàng có thể đặt qua website, Facebook hoặc hotline <strong>0905 454 775</strong> và <strong>0905 09 09 10</strong>. Nhân viên sẽ xác nhận sản phẩm, địa chỉ, khung giờ và phí giao trước khi gửi hàng.</p>',
                'giao đồ chơi Đà Nẵng, đặt đồ chơi online, Win Win'),
            $this->article(3, 'Chính sách kiểm tra và hỗ trợ sau mua', $newsImages . '/kiem-tra-san-pham.jpg',
                'Khách hàng được kiểm tra sản phẩm và liên hệ Win Win khi phát hiện lỗi hoặc thiếu phụ kiện.',
                '<p>Nếu sản phẩm giao sai, thiếu phụ kiện hoặc có lỗi khi mở hộp, quý khách vui lòng chụp ảnh/video và liên hệ sớm để được kiểm tra. Chính sách bảo hành cụ thể áp dụng theo từng sản phẩm và nhà sản xuất.</p>',
                'bảo hành đồ chơi, đổi trả đồ chơi, hỗ trợ sau mua'),
            $this->article(3, 'Liên hệ Win Win để chọn đồ chơi phù hợp', $newsImages . '/tu-van-chon-do-choi.jpg',
                'Đội ngũ Win Win tư vấn theo độ tuổi, sở thích, ngân sách và dịp tặng quà.',
                '<p>Gọi <strong>0905 454 775</strong> hoặc <strong>0905 09 09 10</strong> để được tư vấn sản phẩm, đóng gói quà và thời gian giao.</p><p><strong>Địa chỉ:</strong> Đường DT605, xã Hòa Tiến, Đà Nẵng.<br><strong>Website:</strong> dochoiwinwin.com</p>',
                'tư vấn đồ chơi, hotline Win Win, quà tặng cho bé'),
        ];
    }

    private function article(
        int $categoryId,
        string $title,
        string $image,
        string $summary,
        string $html,
        string $keywords
    ): array {
        return [
            'category_id' => $categoryId,
            'title' => $title,
            'summary' => $summary,
            'html' => $html,
            'text' => trim(preg_replace('/\s+/', ' ', strip_tags($html))),
            'keywords' => $keywords,
            'image' => $image,
            'image_label' => Str::slug($title),
            'hot' => true,
        ];
    }
}
