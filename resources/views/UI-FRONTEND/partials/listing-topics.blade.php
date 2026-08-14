@php
  $topics = $listingTopics ?? [];
  // Nền pastel xoay vòng theo vị trí thẻ, giữ dải chủ đề nhiều màu như thiết kế.
  $topicPalette = ['#FFF4E8', '#EAF3FF', '#FDEEF5', '#EAFBF6', '#F2FAE3', '#EFEEFF'];
@endphp

@if (!empty($topics))
  <section class="ww-topics mb-4 md:mb-5" aria-label="Chủ đề sản phẩm">
    <h2 class="ww-topics__title text-h6 md:text-h5 font-bold mb-3">Chủ đề cho bạn</h2>

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
  </section>

  <script>
    (function () {
      var scroller = document.querySelector('[data-ww-topics-scroller]');
      if (!scroller) return;

      // Chỉ cuộn trong dải chủ đề, không dùng scrollIntoView để trang không bị nhảy dọc.
      function centerTopic(item, behavior) {
        if (!item) return;
        var target = item.offsetLeft - (scroller.clientWidth - item.offsetWidth) / 2;
        var max = scroller.scrollWidth - scroller.clientWidth;
        scroller.scrollTo({ left: Math.max(0, Math.min(target, max)), behavior: behavior || 'auto' });
      }

      centerTopic(scroller.querySelector('.ww-topics__item.is-active'));

      scroller.addEventListener('click', function (event) {
        var item = event.target.closest('.ww-topics__item');
        if (item) centerTopic(item, 'smooth');
      });
    })();
  </script>
@endif
