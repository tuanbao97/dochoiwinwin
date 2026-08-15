@php
  $ww = wwWebContact();
  $wwZalo = $ww['zaloUrl'] ?: $ww['zaloPageUrl'];
  $wwMessenger = $ww['messengerUrl'] ?: $ww['facebookUrl'];
@endphp
<footer class="bg-white">
  <div class="container">
    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-x-gutter gap-y-6 pt-6 pb-3  md:pt-[var(--spacing-10)] md:pb-[var(--spacing-12)]">
      <div class="footer-col min-w-0">


          <a class="footer-logo mb-3 block" href="{{ url('/') }}" title="{{ $ww['storeName'] }}">
            <img class="object-contain" loading="lazy" src="{{ storefrontLogoUrl() }}" alt="{{ $ww['storeName'] }}" width="120" height="120" style="max-width: 120px;">
          </a>


		  	<div class="text-base font-semibold mb-2" data-ww-contact-slot="store-name">
				{{ $ww['storeName'] }}
		  </div>


		  @if($ww['description'] !== '')
		  <div class="mb-3" data-ww-contact-slot="store-description">
			  {{ $ww['description'] }}
		  </div>
		  @endif


		  @if($ww['taxCode'] !== '')
		  <div class="mb-3">
			Mã số thuế: <span data-ww-contact-slot="tax-code">{{ $ww['taxCode'] }}</span>
		  </div>
		  @endif


          <div class="contact-group mb-5">
            @if($ww['address'] !== '')
            <div class="flex gap-1 items-start mb-3">
              <i class="icon icon-location text-neutral-200"></i>
              <div class="">
                <p class="leading-snug">
                  <span class="text-forground">Địa chỉ</span>
                  <span class="font-semibold" data-ww-contact-slot="address"> {{ $ww['address'] }}</span>
                </p>
              </div>
            </div>
            @endif
            <div class="xl:flex flex-wrap grid gap-2" style="column-gap: var(--spacing-8);">
              @if(count($ww['hotlines']) > 0)
              <div class="flex gap-1 items-start">
                <i class="icon icon-call text-neutral-200"></i>
                <div class="">
                  <p class="text-forground">Hotline</p>
                  <div class="font-semibold text-primary" data-ww-contact-slot="hotline-list">
                    @foreach($ww['hotlines'] as $i => $hl)
                      @if($i > 0)<span> · </span>@endif
                      <a class="link text-primary font-semibold" href="{{ $hl['tel'] }}" @if($i === 0) data-ww-contact="hotline" @endif title="{{ $hl['display'] }}">{{ $hl['display'] }}</a>
                    @endforeach
                  </div>
                </div>
              </div>
              @endif
              @if($ww['email'] !== '')
              <div class="flex gap-1 items-start">
                <i class="icon icon-sms text-neutral-200"></i>
                <div class="">
                  <p class="text-forground">Email</p>
                  <a class="font-semibold link" href="mailto:{{ $ww['email'] }}" data-ww-contact="email" data-ww-contact-fill-text title="{{ $ww['email'] }}">{{ $ww['email'] }}</a>
                </div>
              </div>
              @endif
            </div>
          </div>


          <div class="social-icons">
            <p class="font-semibold mb-3">Mạng xã hội</p>
            <div class="flex gap-3">

	@if($ww['facebookUrl'] !== '')
	<div class="facebook" data-ww-social>
		<a href="{{ $ww['facebookUrl'] }}" target="_blank" rel="noopener noreferrer" data-ww-contact="facebook" aria-label="Facebook" title="Facebook" class="border border-neutral-50 rounded-sm flex items-center justify-center w-20 h-20 hover:bg-neutral-50">
		  <img src="100/531/894/themes/1018832/assets/social-facebook.svg" class="w-10 h-10 object-contain" width="30" height="30" alt="" decoding="async" loading="lazy">
		</a>
	</div>
	@endif

	@if($wwMessenger !== '')
	<div class="messenger" data-ww-social>
		<a href="{{ $wwMessenger }}" target="_blank" rel="noopener noreferrer" data-ww-contact="messenger" aria-label="Messenger" title="Messenger" class="border border-neutral-50 rounded-sm flex items-center justify-center w-20 h-20 hover:bg-neutral-50">
		  <img src="100/531/894/themes/1018832/assets/addthis-messenger.svg" class="w-10 h-10 object-contain" width="30" height="30" alt="" decoding="async" loading="lazy">
		</a>
	</div>
	@endif

  @if($wwZalo !== '')
  <div class="zalo" data-ww-social>
    <a href="{{ $wwZalo }}" target="_blank" rel="noopener noreferrer" data-ww-contact="zalo" title="Zalo" aria-label="Zalo" class="border border-neutral-50 rounded-sm flex items-center justify-center w-20 h-20 hover:bg-neutral-50">
      <img src="100/531/894/themes/1018832/assets/addthis-zalo.svg" class="w-10 h-10 object-contain" width="30" height="30" alt="" decoding="async" loading="lazy">
    </a>
  </div>
  @endif

  @if($ww['tiktokUrl'] !== '')
  <div class="tiktok" data-ww-social>
    <a href="{{ $ww['tiktokUrl'] }}" target="_blank" rel="noopener noreferrer" data-ww-contact="tiktok" title="TikTok" aria-label="TikTok" class="border border-neutral-50 rounded-sm flex items-center justify-center w-20 h-20 hover:bg-neutral-50">
      <img src="100/531/894/themes/1018832/assets/social-tiktok.svg" class="w-10 h-10 object-contain" width="30" height="30" alt="" decoding="async" loading="lazy">
    </a>
  </div>
  @endif

	  @if($ww['youtubeUrl'] !== '')
	  <div class="youtube" data-ww-social>
		<a href="{{ $ww['youtubeUrl'] }}" target="_blank" rel="noopener noreferrer" data-ww-contact="youtube" title="YouTube" aria-label="YouTube" class="border border-neutral-50 rounded-sm flex items-center justify-center w-20 h-20 hover:bg-neutral-50">
		  <img src="100/531/894/themes/1018832/assets/social-youtube.svg" class="w-10 h-10 object-contain" width="30" height="30" alt="" decoding="async" loading="lazy">
		</a>
	  </div>
	  @endif

</div>
          </div>

      </div>
      <div class="footer-col min-w-0">
		  <details open="" class="footer-details">
		  	<summary class="text-base font-semibold mb-2 flex items-center justify-between">
			    Chính sách

           		  <i class="icon icon-carret-right inline-block md:hidden"></i>


			 </summary>


            <ul class="list-menu space-y-4  list-disc pl-5">

                <li class="li_menu">
                  <a class="link" href="{{ url('/chinh-sach-bao-hanh') }}" title="Chính sách bảo hành">Chính sách bảo hành</a>
                </li>

                <li class="li_menu">
                  <a class="link" href="{{ url('/chinh-sach-thanh-toan') }}" title="Chính sách thanh toán">Chính sách thanh toán</a>
                </li>

            </ul>

		  </details>

        @if(count($ww['hotlines']) > 0)
        <div class="mt-6">
          <p class="text-base font-semibold mb-2">
            Tổng đài hỗ trợ
          </p>
          <ul class="list-menu space-y-2 list-disc pl-5">
              @foreach($ww['hotlines'] as $i => $hl)
              <li>
                <a class="link font-semibold" href="{{ $hl['tel'] }}" @if($i === 0) data-ww-contact="hotline" @endif title="{{ $hl['display'] }}">
                  <span @if($i === 0) data-ww-contact-slot="hotline-number" @endif>{{ $hl['display'] }}</span>
                </a>
              </li>
              @endforeach
          </ul>
          @if($ww['workingHours'] !== '')
          <p class="text-sm text-neutral-200 mt-2 mb-0" data-ww-contact-slot="working-hours">{{ $ww['workingHours'] }}</p>
          @endif
        </div>
        @endif
      </div>
      <div class="footer-col min-w-0 md:col-span-2 lg:col-span-1">

          <p class="font-semibold mb-2">Bản đồ cửa hàng</p>
          @if($ww['mapUrl'] !== '')
          <div class="w-full overflow-hidden rounded border border-neutral-50 bg-neutral-50 ww-footer-map" style="min-height:200px">
            <iframe
              class="block w-full h-[200px] sm:h-[240px] md:h-[200px]"
              data-ww-contact="map"
              src="{{ $ww['mapUrl'] }}"
              width="600"
              height="300"
              style="border:0;display:block;min-height:200px;background:#f1f5f9;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="{{ $ww['storeName'] }} — Google Maps"
            ></iframe>
          </div>
          @endif

          @if($ww['address'] !== '')
          <div class="flex gap-1 items-start mt-4">
            <i class="icon icon-location text-neutral-200 shrink-0 mt-0.5"></i>
            <div class="min-w-0">
              <p class="leading-snug">
                <span class="text-forground">Địa chỉ: </span><span class="font-semibold" data-ww-contact-slot="address">{{ $ww['address'] }}</span>
              </p>
            </div>
          </div>
          @endif

      </div>
    </div>
    <div class="footer-copyright border-t border-neutral-50 py-3 text-center gap-2 grid gird-cols-1 md:grid-cols-[1fr_auto] items-center">
      <span class="wsp font-semibold" data-ww-contact-slot="commitment-text">
        {{ $ww['commitmentText'] !== '' ? $ww['commitmentText'] : $ww['storeName'] }}
      </span>
		    </div>
	</div>
</footer>
@include('UI-FRONTEND.common.winwin-contact-settings')
