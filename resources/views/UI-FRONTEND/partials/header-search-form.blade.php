{{-- Ô tìm kiếm header / drawer — gọi GET /search và API product/list qua ThemeStorefrontController --}}
<div class="search-bar relative">
  <quick-search class="quick-search">
    <form action="{{ url('/tim-kiem') }}" method="get" data-ww-search-form class="m-0 flex bg-background pl-1 pr-0 py-0 rounded items-stretch relative border border-neutral-50 overflow-hidden" role="search">
      <div class="search-input-group w-full relative flex items-stretch min-h-[4rem]">
        <input
          type="text"
          name="query"
          value="{{ request()->route('query') ? rawurldecode((string) request()->route('query')) : request('query', '') }}"
          autocomplete="off"
          class="border-0 bg-transparent focus:ring-transparent py-0 pl-1 pr-2 w-full text-base text-foreground text-ellipsis self-center"
          required
          data-placeholder="Tìm đồ chơi, xe điều khiển, lego,...; Tìm theo loại đồ chơi...;"
          placeholder="Tìm đồ chơi, xe điều khiển, lego,..."
        >
        <button type="submit" aria-label="search" class="search-button btn flex items-center justify-center shrink-0 text-foreground rounded-none rounded-r bg-[var(--color-search)] text-white">
          <i class="icon icon-search"></i>
        </button>
      </div>
    </form>
    <script>
      (function () {
        var form = document.currentScript && document.currentScript.previousElementSibling;
        if (!form || form.tagName !== 'FORM') {
          form = document.querySelector('[data-ww-search-form]');
        }
        if (!form) return;
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          var input = form.querySelector('input[name="query"]');
          var q = (input && input.value ? input.value : '').trim();
          if (!q) return;
          window.location.href = @json(rtrim(url('/'), '/')) + '/tim-kiem/' + encodeURIComponent(q);
        });
      })();
    </script>
    <div class="search-dropdown absolute bottom-0 top-full mt-1 left-0 bg-background rounded-lg py-3 text-foreground w-full px-2">
      <div class="search-loading absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full z-[1] bg-background flex items-center justify-center">
        <span class="loading-icon gap-1 hidden items-center justify-center">
          <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
          <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
          <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
        </span>
      </div>
      <div class="search-result space-y-3"></div>
      <div class="search-history-list border-neutral-50"></div>
      <div class="search-keywords space-y-2 pt-2 px-2">
        <span class="text-primary font-semibold">Từ khóa phổ biến</span>
        <div class="search-keyword-list flex gap-1 items-center flex-wrap">
          @php
            $popularKeywords = [
              [
                'label' => 'Điều khiển',
                'url' => storefrontProductCategoryUrl(2001, 'Đồ chơi điều khiển'),
              ],
              [
                'label' => 'Lắp ghép',
                'url' => storefrontProductCategoryUrl(2002, 'Đồ chơi lắp ghép'),
              ],
              [
                'label' => 'Mô hình',
                'url' => storefrontProductCategoryUrl(2003, 'Đồ chơi mô hình'),
              ],
            ];
          @endphp
          @foreach ($popularKeywords as $keyword)
          <a
            href="{{ $keyword['url'] }}"
            class="search-keyword text-secondary px-3 py-1 bg-relative rounded-full inline-flex items-center justify-center hover:border-secondary border border-transparent"
            title="{{ $keyword['label'] }}"
          >{{ $keyword['label'] }}</a>
          @endforeach
        </div>
      </div>
    </div>
  </quick-search>
</div>
