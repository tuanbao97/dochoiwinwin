{{-- Menu danh mục sản phẩm — lấy từ category_p (seed theo folder Đồ chơi Win Win) --}}
@php
  use App\Enum\AppConstant;
  use App\Models\CategoryP;

  $menuIconFor = static function (string $name): string {
      $n = mb_strtolower($name);
      if (str_contains($n, 'điều khiển') || str_contains($n, 'dieu khien')) {
          return 'do-choi-dieu-khien.svg';
      }
      if (str_contains($n, 'lắp ghép') || str_contains($n, 'lap ghep') || str_contains($n, 'lego')) {
          return 'do-choi-lap-ghep.svg';
      }
      if (str_contains($n, 'mô hình') || str_contains($n, 'mo hinh')) {
          return 'do-choi-mo-hinh.svg';
      }
      if (str_contains($n, 'nước') || str_contains($n, 'nuoc')) {
          return 'do-choi-nuoc.svg';
      }
      if (str_contains($n, 'giáo dục') || str_contains($n, 'giao duc')) {
          return 'do-choi-giao-duc.svg';
      }

      return 'do-choi-dieu-khien.svg';
  };

  try {
      $menuRoots = CategoryP::query()
          ->whereNull('PARENT_ID')
          ->where('STATUS', AppConstant::STATUS_USING)
          ->where('IS_ACTIVE', true)
          ->orderByRaw('CASE WHEN SORT_ORDER IS NOT NULL THEN SORT_ORDER ELSE 999999999 END ASC')
          ->orderBy('NAME', 'ASC')
          ->with(['childrens'])
          ->get();
  } catch (\Throwable) {
      $menuRoots = collect();
  }
@endphp

<ul class="">
  @forelse ($menuRoots as $cat)
    @php
      $catName = (string) ($cat->NAME ?? '');
      $catId = (int) ($cat->ID ?? 0);
      $catUrl = storefrontProductCategoryUrl($catId, $catName);
      $icon = asset('UI-FRONTEND/assets/ww-menu-icons/' . $menuIconFor($catName));
      $children = $cat->childrens ?? collect();
      $hasChildren = $children->isNotEmpty();
    @endphp
    <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
      <a
        class="menu-item__link flex items-center gap-3 py-2 font-semibold min-w-0"
        title="{{ $catName }}"
        href="{{ $catUrl }}"
        data-prefetch="{{ parse_url($catUrl, PHP_URL_PATH) ?: $catUrl }}"
      >
        <img loading="lazy" width="36" height="36" class="w-9 h-9 shrink-0" src="{{ $icon }}" alt="{{ $catName }}">
        <span class="truncate">{{ $catName }}</span>
        @if ($hasChildren)
          <span class="ml-auto text-neutral-200 flex items-center" data-toggle-submenu="">
            <i class="icon icon-carret-right"></i>
          </span>
        @endif
      </a>

      @if ($hasChildren)
        <div class="submenu absolute lg:group-hover:grid p-4 overflow-auto default">
          <div
            data-toggle-submenu=""
            class="relative toggle-submenu -mt-4 -mx-4 p-3 mb-4 bg-neutral-50 font-semibold flex justify-between lg:hidden"
          >
            <span>
              <i class="icon icon-carret-left mr-auto text-neutral-200"></i>
            </span>
            <span class="mx-auto">{{ $catName }}</span>
          </div>
          <div class="mega-menu__inner flex-wrap gap-3 flex items-start">
            <ul class="submenu__list flex flex-col gap-4 w-full">
              @foreach ($children as $child)
                @php
                  $childName = (string) ($child->NAME ?? '');
                  $childId = (int) ($child->ID ?? 0);
                  $childUrl = storefrontProductCategoryUrl($childId, $childName);
                @endphp
                <li class="submenu__item submenu__item--main font-semibold">
                  <a class="link" title="{{ $childName }}" href="{{ $childUrl }}" data-prefetch="{{ parse_url($childUrl, PHP_URL_PATH) ?: $childUrl }}">
                    {{ $childName }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif
    </li>
  @empty
    <li class="menu-item px-6 py-3 text-neutral-300 text-sm">Chưa có danh mục sản phẩm</li>
  @endforelse

  @include('UI-FRONTEND.partials.menu-san-pham-moi-related')
</ul>
