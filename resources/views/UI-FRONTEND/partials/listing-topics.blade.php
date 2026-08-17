@php
  $topics = $listingTopics ?? [];
  // Nền pastel xoay vòng theo vị trí thẻ, giữ dải chủ đề nhiều màu như thiết kế.
  $topicPalette = ['#FFF4E8', '#EAF3FF', '#FDEEF5', '#EAFBF6', '#F2FAE3', '#EFEEFF'];
@endphp

@if (!empty($topics))
  <section class="ww-topics mb-4 md:mb-5" aria-label="Chủ đề sản phẩm">
    <h2 class="ww-topics__title text-h6 md:text-h5 font-bold mb-3">Chủ đề cho bạn</h2>

    <div class="ww-topics__viewport">
      <button type="button" class="ww-topics__nav ww-topics__nav--prev" data-ww-topics-prev aria-label="Chủ đề trước" hidden>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M15 5L8 12L15 19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <div class="ww-topics__scroller" data-ww-topics-scroller>
        @foreach ($topics as $index => $topic)
          <a href="{{ $topic['url'] }}"
            class="ww-topics__item {{ $topic['active'] ? 'is-active' : '' }}"
            style="--topic-bg: {{ $topicPalette[$index % count($topicPalette)] }}"
            title="{{ $topic['name'] }}"
            @if ($topic['active']) aria-current="page" @endif>
            <span class="ww-topics__thumb">
              <img src="{{ $topic['icon'] }}" alt="{{ $topic['name'] }}" width="72" height="72" loading="lazy">
            </span>
            <span class="ww-topics__label">{{ storefrontMenuLabel($topic['name']) }}</span>
          </a>
        @endforeach
      </div>

      <button type="button" class="ww-topics__nav ww-topics__nav--next" data-ww-topics-next aria-label="Chủ đề tiếp theo" hidden>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>
  </section>

  <script>
    (function () {
      var scroller = document.querySelector('[data-ww-topics-scroller]');
      if (!scroller) return;

      var prevBtn = document.querySelector('[data-ww-topics-prev]');
      var nextBtn = document.querySelector('[data-ww-topics-next]');

      function maxScroll() {
        return scroller.scrollWidth - scroller.clientWidth;
      }

      // Chỉ cuộn trong dải chủ đề, không dùng scrollIntoView để trang không bị nhảy dọc.
      function centerTopic(item, behavior) {
        if (!item) return;
        var target = item.offsetLeft - (scroller.clientWidth - item.offsetWidth) / 2;
        scroller.scrollTo({ left: Math.max(0, Math.min(target, maxScroll())), behavior: behavior || 'auto' });
      }

      function syncNav() {
        if (!prevBtn || !nextBtn) return;
        var max = maxScroll();
        // Trừ hao 2px vì scrollLeft có thể là số lẻ khi zoom trình duyệt.
        var scrollable = max > 2;
        prevBtn.hidden = !scrollable || scroller.scrollLeft <= 2;
        nextBtn.hidden = !scrollable || scroller.scrollLeft >= max - 2;
      }

      function step(direction) {
        var distance = Math.max(scroller.clientWidth * 0.8, 160);
        scroller.scrollBy({ left: direction * distance, behavior: 'smooth' });
      }

      if (prevBtn) prevBtn.addEventListener('click', function () { step(-1); });
      if (nextBtn) nextBtn.addEventListener('click', function () { step(1); });

      scroller.addEventListener('scroll', syncNav, { passive: true });
      window.addEventListener('resize', syncNav);

      scroller.addEventListener('click', function (event) {
        var item = event.target.closest('.ww-topics__item');
        if (item) centerTopic(item, 'smooth');
      });

      centerTopic(scroller.querySelector('.ww-topics__item.is-active'));
      syncNav();
    })();
  </script>
@endif
