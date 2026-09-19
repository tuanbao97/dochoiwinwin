<?php

namespace App\Console\Commands;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use App\Service\SapoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SapoSyncCategoriesCommand extends Command
{
    protected $signature = 'sapo:sync-categories
        {--publish : Publish unpublished Sapo collections}
        {--include-legacy : Kèm sữa/bánh kẹo (mặc định bỏ)}';

    protected $description = 'Đồng bộ custom collections Sapo → category_p (chỉ thêm/cập nhật, không xóa danh mục cũ)';

    public function handle(SapoService $sapo): int
    {
        if (! $sapo->isEnabled()) {
            $this->error('Sapo chưa bật cấu hình.');

            return self::FAILURE;
        }

        $custom = [];
        for ($page = 1; $page <= 20; $page++) {
            $chunk = $sapo->getCustomCollections(['limit' => 250, 'page' => $page]);
            if ($chunk === []) {
                break;
            }
            foreach ($chunk as $row) {
                if (is_array($row)) {
                    $custom[] = $row;
                }
            }
            if (count($chunk) < 250) {
                break;
            }
        }

        $includeLegacy = (bool) $this->option('include-legacy');
        $skipNeedles = ['sữa', 'sua ', 'bánh kẹo', 'banh keo', 'đồ khô', 'trái cây', 'trai cay'];

        $toyish = [];
        foreach ($custom as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            if (! $includeLegacy) {
                $lower = mb_strtolower($name);
                $skip = false;
                foreach ($skipNeedles as $needle) {
                    if (str_contains($lower, $needle)) {
                        $skip = true;
                        break;
                    }
                }
                if ($skip) {
                    continue;
                }
            }
            $toyish[] = [
                'id' => (int) ($row['id'] ?? 0),
                'name' => $name,
                'published' => (bool) ($row['published'] ?? false),
            ];
        }

        $bySapo = DB::table('category_p')
            ->whereNotNull('ATTR2')
            ->where('ATTR2', '!=', '')
            ->get(['ID', 'ATTR2'])
            ->mapWithKeys(static fn ($r) => [(string) $r->ATTR2 => (int) $r->ID])
            ->all();

        $byName = DB::table('category_p')
            ->get(['ID', 'NAME'])
            ->keyBy(static fn ($r) => mb_strtolower(trim((string) $r->NAME)));

        $maxId = (int) DB::table('category_p')->max('ID');
        $nextId = max($maxId + 1, 2100);
        $maxSortRoot = (int) DB::table('category_p')->whereNull('PARENT_ID')->max('SORT_ORDER');

        $added = 0;
        $updated = 0;

        foreach ($toyish as $row) {
            $sapoId = (string) $row['id'];
            if ((int) $sapoId <= 0) {
                continue;
            }
            $name = $row['name'];
            $nameKey = mb_strtolower($name);

            if (isset($bySapo[$sapoId])) {
                DB::table('category_p')->where('ID', $bySapo[$sapoId])->update([
                    'NAME' => $name,
                    'STATUS' => AppConstant::STATUS_USING,
                    'IS_ACTIVE' => true,
                    'ATTR2' => $sapoId,
                    'UPD_DT' => now(),
                    'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                    'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                ]);
                $updated++;
                continue;
            }

            if ($byName->has($nameKey)) {
                $existingId = (int) $byName->get($nameKey)->ID;
                DB::table('category_p')->where('ID', $existingId)->update([
                    'STATUS' => AppConstant::STATUS_USING,
                    'IS_ACTIVE' => true,
                    'ATTR2' => $sapoId,
                    'UPD_DT' => now(),
                    'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                    'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                ]);
                $bySapo[$sapoId] = $existingId;
                $updated++;
                continue;
            }

            $maxSortRoot++;
            $newId = $nextId++;
            DB::table('category_p')->insert([
                'ID' => $newId,
                'NAME' => $name,
                'SORT_ORDER' => $maxSortRoot,
                'TREE_LEVEL' => 0,
                'PARENT_ID' => null,
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
                'ATTR1' => null,
                'ATTR2' => $sapoId,
                'ATTR50' => 'UI-BACKEND/admin/san-pham/common/san-pham',
            ]);
            $bySapo[$sapoId] = $newId;
            $byName->put($nameKey, (object) ['ID' => $newId, 'NAME' => $name]);
            $added++;
        }

        $published = 0;
        if ($this->option('publish')) {
            foreach ($toyish as $row) {
                if ($row['published'] || $row['id'] <= 0) {
                    continue;
                }
                try {
                    $sapo->put('/admin/custom_collections/'.$row['id'].'.json', [
                        'custom_collection' => [
                            'id' => $row['id'],
                            'published' => true,
                        ],
                    ]);
                    $published++;
                    usleep(120000);
                } catch (Throwable $e) {
                    $this->warn('Publish failed '.$row['id'].': '.$e->getMessage());
                }
            }
        }

        $active = DB::table('category_p')
            ->where('IS_ACTIVE', true)
            ->where('STATUS', AppConstant::STATUS_USING)
            ->count();

        $this->info("Sapo collections (toy): ".count($toyish));
        $this->info("Added: {$added} | Updated: {$updated} | Published: {$published}");
        $this->info("Local active categories: {$active} (không xóa danh mục cũ)");

        return self::SUCCESS;
    }
}
