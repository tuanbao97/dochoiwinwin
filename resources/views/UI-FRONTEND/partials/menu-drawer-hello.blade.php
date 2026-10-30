@php
  $wwMenuName = trim((string) ($storefrontUser['FULL_NAME'] ?? ''));
@endphp
<div class="flex items-center gap-3">
  <p class="ww-menu-hello" data-ww-menu-hello @if($wwMenuName === '') hidden @endif>
    Xin chào, <span data-ww-menu-hello-name>{{ $wwMenuName }}</span>
  </p>
  <button type="button" id="PortalClose-menu-crawer" class="portal-close-button ml-auto w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin" title="Đóng" aria-label="Đóng">
    <i class="icon icon-cross"></i>
  </button>
</div>
