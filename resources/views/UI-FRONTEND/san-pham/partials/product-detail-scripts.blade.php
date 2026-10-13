@include('UI-FRONTEND.san-pham.partials.product-hydrate')

<script src="{{ storefrontThemeAsset('main.js') }}"></script>
@include('UI-FRONTEND.common.cart-scripts')
{{-- product.js đã được load trong theme-portals — không load lại (tránh CompareButton duplicate) --}}
<script src="{{ storefrontThemeAsset('flashsale.js') }}" defer fetchpriority="low"></script>
<script src="{{ storefrontThemeAsset('coupon.js') }}" defer fetchpriority="low"></script>
<script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>
