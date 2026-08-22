@include('UI-FRONTEND.san-pham.partials.product-hydrate')

<script src="{{ storefrontThemeAsset('main.js') }}"></script>
@include('UI-FRONTEND.common.cart-scripts')
{{-- product.js đã được load trong theme-portals — không load lại (tránh CompareButton duplicate) --}}
<script src="{{ storefrontThemeAsset('flashsale.js') }}" defer fetchpriority="low"></script>
<script src="{{ storefrontThemeAsset('coupon.js') }}" defer fetchpriority="low"></script>
<script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>

@if (false)
  {{-- Cloudflare RUM beacon — tắt: endpoint /cdn-cgi/rum thường 500 trên host không dùng CF analytics --}}
  <script
    defer
    src="beacon.min.js/v8c78df7c7c0f484497ecbca7046644da1771523124516"
    integrity="sha512-8DS7rgIrAmghBFwoOTujcf6D9rXvH8xm8JQ1Ja01h9QX8EzXldiszufYa4IFfKdLUKTTrnSFXLDkUEOTrZQ8Qg=="
    data-cf-beacon='{"version":"2024.11.0","token":"6c92bbc133584e029f09e826272d3606","server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}'
    crossorigin="anonymous"
  ></script>
@endif
