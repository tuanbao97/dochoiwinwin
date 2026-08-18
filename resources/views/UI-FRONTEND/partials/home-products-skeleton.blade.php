{{-- Placeholder trước khi AJAX đổ section danh mục — tránh trang trống rồi nhảy layout --}}
<section
  id="ww-home-products-skeleton"
  class="section section-home-products-skeleton"
  aria-busy="true"
  aria-live="polite"
  style="--section-padding: 0; --section-margin: 24px 0 24px; --section-padding-mb: 0; --section-margin-mb: 24px 0 24px;"
>
  <div class="container">
    <div class="section-card">
      <div class="ww-skel-section-head">
        <div class="heading-bar ww-skel-section-title">
          <span class="ww-skel-bone ww-skel-section-title__text" aria-hidden="true"></span>
        </div>
        <div class="ww-skel-section-tabs" aria-hidden="true">
          <span class="ww-skel-bone ww-skel-section-tab"></span>
          <span class="ww-skel-bone ww-skel-section-tab ww-skel-section-tab--md"></span>
          <span class="ww-skel-bone ww-skel-section-tab"></span>
          <span class="ww-skel-bone ww-skel-section-tab ww-skel-section-tab--lg"></span>
        </div>
      </div>

      <div class="product-list grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
        @for ($i = 0; $i < 10; $i++)
          @include('UI-FRONTEND.partials.product-card-skeleton')
        @endfor
      </div>

      <div class="ww-skel-section-more" aria-hidden="true">
        <span class="ww-skel-bone ww-skel-section-more__btn"></span>
      </div>
    </div>
  </div>
</section>
