{{-- Open Graph + Twitter Card — render server-side (Facebook không chạy JS) --}}
@php
  $seo = storefrontSeo([
    'title' => $seoTitle ?? null,
    'description' => $seoDescription ?? null,
    'image' => $seoImage ?? null,
    'type' => $seoType ?? null,
    'url' => $seoUrl ?? null,
  ]);
@endphp
<meta name="description" content="{{ $seo['description'] }}" id="ww-meta-description">
<title id="ww-page-title">{{ $seo['title'] }}</title>

<meta property="og:type" content="{{ $seo['type'] }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:image:secure_url" content="{{ $seo['image'] }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="{{ $seoImageWidth ?? 1024 }}">
<meta property="og:image:height" content="{{ $seoImageHeight ?? 618 }}">
<meta property="og:image:alt" content="{{ $seo['siteName'] }}">
<meta property="og:url" content="{{ $seo['url'] }}">
<meta property="og:site_name" content="{{ $seo['siteName'] }}">
<meta property="og:locale" content="vi_VN">
@php($fbAppId = storefrontFacebookAppId())
@if($fbAppId !== '')
<meta property="fb:app_id" content="{{ $fbAppId }}">
@endif
<meta name="zalo-platform-site-verification" content="E8wdAFEQBXCrfBzCjO4RUc2IYqMCv50OCpWt" />

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">

<link rel="icon" href="{{ storefrontFaviconUrl() }}" type="image/png">
<link rel="apple-touch-icon" href="{{ storefrontFaviconUrl() }}">
<link rel="shortcut icon" href="{{ storefrontFaviconUrl() }}" type="image/png">
