@include('UI-FRONTEND.partials.storefront-image-cache')
{{--
<div class="top-banner">
  <a href="https://dochoiwinwin.com/" class="top-banner__link" title="Ưu đãi đặc biệt" aria-label="Ưu đãi đặc biệt"></a>
</div>
--}}

@include('UI-FRONTEND.partials.menu-category-links-script')


	<header class="header relative flex items-center justify-center  top-0 py-4-5 h-max ">
  <div class="container">
    <div class="
        header-wrapper flex items-center  justify-between gap-3 lg:grid lg:gap-2

        lg:grid-cols-[286px_1fr_auto]

        flex-wrap
      ">
      <div class="flex items-center gap-5">
        <div class="flex gap-2 xl:gap-6 xl:flex-row-reverse items-center transition-all duration-150 ">

          <div class=" menu-opener  xl:hidden   ">
            <portal-opener>
              <div data-portal="#menu-drawer" class="hamburger-button cursor-pointer link group inline-flex  items-center  justify-center gap-2 active:scale-95 transition-all duration-150 bg-relative text-primary rounded p-2" id="toggle-menu-mobile">
                <div class="w-[3rem] h-[3rem] p-2 rounded bg-relative  justify-center items-center  grid grid-cols-2 gap-0.5 ">

                    <span class="h-[7px] w-[7px] rounded-full border-[2px]  block border-primary"></span>

                    <span class="h-[7px] w-[7px] rounded-full border-[2px]  block border-primary"></span>

                    <span class="h-[7px] w-[7px] rounded-full border-[2px]  block border-primary"></span>

                    <span class="h-[7px] w-[7px] rounded-full border-[2px]  block border-primary"></span>

                </div>
                <span class="font-semibold hidden xl:">Danh mục sản phẩm</span>
              </div>
            </portal-opener>
          </div>
          <a href="{{ url('/') }}" data-prefetch="" class="logo-wrapper mx-auto" title="Đồ Chơi Win Win">

              <img src="{{ storefrontLogoWideUrl() }}" alt="Đồ Chơi Win Win" style="max-height: var(--logo-height-header); width: auto; max-width: min(var(--logo-max-width-header), 100%); object-fit: contain;">

          </a>
        </div>

      </div>
      <div class="mx-auto header-search dnone md:block order-1 lg:order-none">
        <div class=" mx-auto md:px-5">
        @include('UI-FRONTEND.partials.header-search-form')
        </div>
      </div>
      <div class="header-icon-wrapper flex items-center justify-end  gap-2 xl:gap-5.5 ">

					<div class="header-box-live dnone" aria-hidden="true">
						<button type="button" data-mobile-link="https://fb.watch/t6bCpir7ve/" style="--header-live-color:#ff0000" class="btn-live transition-all hover:scale-105">LIVE</button>
					</div>
        <portal-opener class=" search-opener block lg:hidden">
          <a data-portal="#search-drawer" href="search-3.html" title="tìm kiếm" class=" header-icon-group flex gap-2 items-center cart-group  hover:bg-neutral-50 active:scale-95 transition-all duration-150  md:px-2 px-1 py-1 rounded-sm ">
            <div class="header-icon w-[3.6rem] h-[3.6rem]  p-2 rounded-full flex items-center justify-center relative border border-neutral-50 ">
              <i class="icon icon-search text-5"></i>
            </div>
          </a>
        </portal-opener>

          @include('UI-FRONTEND.partials.header-account')

        <portal-opener>
          @php $wwCartUser = $storefrontUser ?? null; @endphp
          <a
            @if($wwCartUser) data-portal="#cart-drawer" @endif
            href="{{ $wwCartUser ? url('/cart') : storefrontLoginUrl(url('/cart')) }}"
            title="{{ $wwCartUser ? 'Giỏ hàng' : 'Đăng nhập để xem giỏ hàng' }}"
            class="mini-cart header-icon-group flex gap-2 items-center cart-group  hover:bg-neutral-50 active:scale-95 transition-all duration-150  md:px-2 px-1  py-1 rounded-sm"
          >
  <div class="header-icon w-[3.6rem] h-[3.6rem]  p-2 rounded-full flex items-center justify-center relative border border-neutral-50">
    <i class="icon icon-cart"></i>
    <span class="cart-count flex items-center count_item count_item_pr justify-center rounded-full absolute font-semibold"><span class="cart-count__num">{{ storefrontCartQuantity() }}</span></span>
  </div>

</a>
        </portal-opener>
      </div>
    </div>
</div>
</header>
<div class="navigation-wrapper hidden xl:flex items-center sub-header">
  <div class="container relative">
    <div class="">

      <div class="flex justify-between items-center">
                    <div class="navigation--horizontal dnone  lg:flex items-center ">
    <div class=" navigation-horizontal-wrapper overflow-hidden ">
      <nav>
        <ul class="navigation-horizontal">


            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="TRANG CHỦ" href="{{ url('/') }}" data-prefetch="/">
                <span>
                  TRANG CHỦ                </span>

              </a>

                                        </li>


            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="TẤT CẢ SẢN PHẨM" href="{{ url('/tat-ca-san-pham') }}" data-prefetch="/tat-ca-san-pham">
                <span>
                  TẤT CẢ SẢN PHẨM                </span>

              </a>

                                        </li>


            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="GIỚI THIỆU" href="{{ url('/gioi-thieu') }}" data-prefetch="/gioi-thieu">
                <span>
                  GIỚI THIỆU                </span>

              </a>

                                        </li>


            {{-- Menu KHUYẾN MÃI (tạm ẩn)
            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="KHUYẾN MÃI" href="flash-sale-1-khung-gio.html" data-prefetch="/flash-sale-1-khung-gio">
                <span>KHUYẾN MÃI</span>
              </a>
            </li>
            --}}


            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="TIN TỨC" href="{{ url('tin-tuc') }}" data-prefetch="/tin-tuc">
                <span>
                  TIN TỨC                </span>

              </a>

                                        </li>


            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="VIDEO" href="{{ url('/video') }}" data-prefetch="/video">
                <span>
                  VIDEO                </span>

              </a>

                                        </li>


            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="LIÊN HỆ" href="{{ url('/lien-he') }}" data-prefetch="/lien-he">
                <span>
                  LIÊN HỆ                </span>

              </a>

                                        </li>


            <li class="menu-item group ">
              <a class="menu-item__link hover:text-[var(--color-sub-header-link)]  flex items-center gap-3.5 py-2 font-semibold " title="BẢO HÀNH" href="{{ url('/chinh-sach-bao-hanh') }}" data-prefetch="/chinh-sach-bao-hanh">
                <span>
                  BẢO HÀNH                </span>

              </a>

                                        </li>

        </ul>
      </nav>
    </div>
    <div class=" navigation-arrows ">
	   <button class="btn prev px-1">
      <i class="  disabled icon icon-carret-left link"> </i>
    </button>
    <button class="btn next px-1 ">
      <i class=" icon icon-carret-right link"> </i>
    </button>

    </div>
  </div>


        <div class="flex gap-[var(--spacing-8)] whitespace-nowrap ">

            <a href="{{ url('/lien-he') }}" title="Địa chỉ cửa hàng" class="inline-flex gap-1 items-center store-group hover:text-[var(--color-sub-header-link)]">
              <div class="rounded-sm flex items-center justify-center">
                <i class="icon icon-store"></i>
              </div>
              <div>
                <span class="font-semibold">Địa chỉ cửa hàng</span>
              </div>
            </a>


            @php($wwHeader = wwWebContact())
            <a class="inline-flex gap-1 items-center phone-group hover:text-[var(--color-sub-header-link)]" href="{{ $wwHeader['hotline']['tel'] ?? 'tel:' }}" data-ww-contact="hotline" title="{{ $wwHeader['hotline']['display'] ?? 'Hotline' }}">
              <div class="rounded-sm flex items-center justify-center">
                <i class="icon icon-calling-phone"></i>
              </div>
              <div>
                <span>
                  Hotline: <b data-ww-contact-slot="hotline-number">{{ $wwHeader['hotline']['display'] ?? '' }}</b>
                </span>
              </div>
            </a>

        </div>
      </div>
    </div>
  </div>
</div>
