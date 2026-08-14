<link href="100/531/894/themes/1018832/assets/bpr-products-module.css?1768901692132" rel="stylesheet" type="text/css" media="all">
<div class="sapo-product-reviews-module"></div>
@php
  $ww = wwWebContact();
  $wwZalo = $ww['zaloUrl'] ?: $ww['zaloPageUrl'];
  $wwMessenger = $ww['messengerUrl'] ?: $ww['facebookUrl'];
  $wwHotline = $ww['hotline'];
@endphp
@include('UI-FRONTEND.common.footer')


@if(false)
			{{-- Popup Sapo gá»£i Ã½ á»©ng dá»¥ng (táº¯t) --}}
			<link rel="preload" as="style" media="all" href="100/531/894/themes/1018832/assets/sapo-popup.css?1768901692132">
<link rel="stylesheet" href="100/531/894/themes/1018832/assets/sapo-popup.css?1768901692132" media="all">
<div class="popup-sapo active">
	<div class="icon">
		<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewbox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M224 0c-17.7 0-32 14.3-32 32V51.2C119 66 64 130.6 64 208v18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416H416c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8V208c0-77.4-55-142-128-156.8V32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3H224 160c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"></path></svg>
	</div>
	<div class="content">
		<div class="title">CÃ³ thá»ƒ tÃ­ch há»£p thÃªm cÃ¡c á»©ng dá»¥ngg</div>
		<ul>
			<li>

				<a href="https://apps.sapo.vn/danh-gia-san-pham-v2" target="_blank" title="ÄÃ¡nh giÃ¡ sáº£n pháº©m" aria-label="ÄÃ¡nh giÃ¡ sáº£n pháº©m">- ÄÃ¡nh giÃ¡ sáº£n pháº©m</a>
			</li>

			<li>
				<a href="https://apps.sapo.vn/quan-ly-affiliate-v2" target="_blank">- á»¨ng dá»¥ng Affiliate</a>
			</li>

			<li>
				<a href="https://apps.sapo.vn/mua-x-tang-y-v2" target="_blank">- Mua X Táº·ng Y
 </a>
			</li>
			<li>
				<a href="https://apps.sapo.vn/app-combo" target="_blank">- Combo sáº£n pháº©m</a>
			</li>

		</ul>
		<div class="ghichu">LÆ°u Ã½ vá»›i cÃ¡c á»©ng dá»¥ng tráº£ phÃ­ báº¡n cáº§n cÃ i Ä‘áº·t vÃ  mua á»©ng dá»¥ng nÃ y trÃªn App store Sapo Ä‘á»ƒ sá»­ dá»¥ng ngay</div>
		<span title="ÄÃ³ng" class="close-popup-sapo">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewbox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001;" xml:space="preserve"> <g> <g> <path d="M284.286,256.002L506.143,34.144c7.811-7.811,7.811-20.475,0-28.285c-7.811-7.81-20.475-7.811-28.285,0L256,227.717    L34.143,5.859c-7.811-7.811-20.475-7.811-28.285,0c-7.81,7.811-7.811,20.475,0,28.285l221.857,221.857L5.858,477.859    c-7.811,7.811-7.811,20.475,0,28.285c3.905,3.905,9.024,5.857,14.143,5.857c5.119,0,10.237-1.952,14.143-5.857L256,284.287    l221.857,221.857c3.905,3.905,9.024,5.857,14.143,5.857s10.237-1.952,14.143-5.857c7.811-7.811,7.811-20.475,0-28.285    L284.286,256.002z"></path> </g> </g> </svg>
		</span>
	</div>
</div>

@if (!app()->environment('local'))
  <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
@endif
<script>
	$('.popup-sapo .icon').click(function() {
		$(".popup-sapo").toggleClass("active");
	});
	$('.close-popup-sapo').click(function() {
		$(".popup-sapo").toggleClass("active");
	});
	setTimeout(()=>{
	$(".popup-sapo").removeClass("active");

	}, 15000)
</script>
@endif
			  	<menu-drawer class="portal portal--drawer" id="menu-drawer" data-type="drawer" data-animation="slide-in-left">
  <dialog class="portal-dialog">
    <div class="portal-overlay"></div>
    <div class="portal-inner animation bg-background h-full grid grid-rows-[auto_1fr_auto]">
      <div class="navigation-header pt-4 flex justify-between items-center border-b pb-3 border-neutral-50 px-4">

          <a href="{{ url('/account/login') }}" title="Đăng nhập" class="header-icon-group flex gap-2 items-center account-group  hover:bg-neutral-50 active:scale-95 transition-all duration-150 px-2 py-1 rounded-sm ">
            <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-sm flex items-center justify-center border border-neutral-50">
              <i class="icon icon-user"></i>
            </div>
            <div class=" ">
              <span class="text-xs">Tài khoản</span>
              <span class="font-semibold block">Đăng nhập</span>
            </div>
          </a>

        <button type="button" id="PortalClose-menu-crawer" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin" title="Đóng" aria-label="Đóng">
          <i class="icon icon-cross"></i>
        </button>
      </div>
      <nav class="navigation-vertical overflow-y-auto no-scrollbar ">
        @include('UI-FRONTEND.partials.menu-category-nav')

    <ul class="">


          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="{{ url('/') }}" data-prefetch="/" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="TRANG CHỦ">
              <span>
                TRANG CHỦ              </span>

            </a>

                                  </li>


          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="{{ url('/tat-ca-san-pham') }}" data-prefetch="/tat-ca-san-pham" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="TẤT CẢ SẢN PHẨM">
              <span>
                TẤT CẢ SẢN PHẨM              </span>

            </a>

                                  </li>


          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="{{ url('/gioi-thieu') }}" data-prefetch="/gioi-thieu" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="GIỚI THIỆU">
              <span>
                GIỚI THIỆU              </span>

            </a>

                                  </li>


          {{-- Menu KHUYẾN MÃI (tạm ẩn)
          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="flash-sale-1-khung-gio.html" data-prefetch="" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="KHUYẾN MÃI">
              <span>KHUYẾN MÃI</span>
            </a>
          </li>
          --}}


          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="{{ url('tin-tuc') }}" data-prefetch="" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="TIN TỨC">
              <span>
                TIN TỨC              </span>

            </a>

                                  </li>


          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="{{ url('/video') }}" data-prefetch="/video" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="VIDEO">
              <span>
                VIDEO              </span>

            </a>

                                  </li>


          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="{{ url('/lien-he') }}" data-prefetch="/lien-he" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="LIÊN HỆ">
              <span>
                LIÊN HỆ              </span>

            </a>

                                  </li>


          <li class="menu-item px-6 group hover:bg-neutral-50 -mt-[1px]">
            <a href="{{ url('/chinh-sach-bao-hanh') }}" data-prefetch="/chinh-sach-bao-hanh" class="menu-item__link flex items-center gap-3.5 py-2 font-semibold " title="BẢO HÀNH">
              <span>
                Báº¢O HÃ€NH              </span>

            </a>

                                  </li>

          </ul>
        </nav>
      <div class="navigation-footer 4 border-t border-neutral-50 flex">

          <div class="w-1/2">
            <a href="{{ url('/lien-he') }}" title="Địa chỉ cửa hàng" class="header-icon-group flex gap-2 items-center  hover:bg-neutral-50 transition-all duration-150 px-2 py-4 store-group">
              <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-sm flex items-center justify-center border border-neutral-50">
                <i class="icon icon-store"></i>
              </div>
              <div>
                <span class="text-xs">Địa chỉ cửa hàng</span>
              </div>
            </a>
          </div>


          <div class="w-1/2">
            <a class="header-icon-group flex gap-2 items-center  hover:bg-neutral-50  transition-all duration-150 px-2 py-4  phone-group " href="{{ $wwHotline['tel'] ?? 'tel:' }}" data-ww-contact="hotline" title="{{ $wwHotline['display'] ?? 'Hotline' }}">
              <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-sm flex items-center justify-center border border-neutral-50">
                <i class="icon icon-calling-phone"></i>
              </div>
              <div>
                <span class="text-xs">Hotline: <b data-ww-contact-slot="hotline-number">{{ $wwHotline['display'] ?? '' }}</b> </span>
              </div>
            </a>
          </div>

      </div>
    </div>
  </dialog>
</menu-drawer>


@if(false)
{{-- Popup ega-sale-pop / sales-pop (táº¯t) --}}
<link rel="stylesheet" href="100/531/894/themes/1018832/assets/sales-pop.css?1768901692132" media="print" onload="this.media='all'">

<noscript><link href="100/531/894/themes/1018832/assets/sales-pop.css?1768901692132" rel="stylesheet" type="text/css" media="all"></noscript>
<div id="ega-sale-pop" class="sales-pop hidden" style="--sale-pop-color: #0284c7">
	<div class="sale-pop-wrap">

	</div>
	<div class="sale-pop-close">
		<i class="icon icon-cross"></i>
	</div>

</div>
<script>
	var salePopArr = [];
	let timePerPop = 15000;
	let timeDelay = 15000;
	let salesPopDesc = `KhÃ¡ch hÃ ng [name] táº¡i [address] vá»«a mua sáº£n pháº©m cÃ¡ch Ä‘Ã¢y [time]`;
	let count = 0;
	let fakerScript ="https://mixcdn.egany.com/themes/assets/faker.js"
	let customerGender = undefined;

		salePopArr = [		{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/ta-dan-merries-size-l-54-mieng-cho-be-9-14kg-1-1.jpg?v=1730192677887",
		"pd_title": "TÃ£ dÃ¡n Merries size L 54 miáº¿ng (9 - 14 kg)",
		"pd_url": "/ta-dan-merries-size-l-54-mieng-9-14-kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/goi-memory-foam-cao-su-non-chong-mop-dau-so-sinh-animo-b2305-dq001-pub121-xanh.jpg?v=1730771679073",
		"pd_title": "Gá»‘i Cao Su Non Size To Cho BÃ©",
		"pd_url": "/goi-cao-su-non-size-to-cho-be",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/7ed6a7bb-cebd-4e89-a980-348773a2f7c7.png?v=1730772153303",
		"pd_title": "Chiáº¿u Ä‘iá»u hoÃ  cao su non cho bÃ© sÆ¡ sinh - 5 tuá»‘i",
		"pd_url": "/chieu-dieu-hoa-cao-su-non-cho-be-so-sinh-5-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/goi-chong-trao-nguoc-cho-be-animo-b2210-ar001-xanh-65x65x20cm.jpg?v=1730772364247",
		"pd_title": "Gá»‘i chá»¯ U chá»‘ng giáº­t mÃ¬nh cho bÃ©, cÃ³ Ä‘á»‹nh hÃ¬nh chá»‘ng báº¹p Ä‘áº§u cho tráº» sÆ¡ sinh",
		"pd_url": "/goi-chu-u-chong-giat-minh-cho-be-co-dinh-hinh-chong-bep-dau-cho-tre-so-sinh",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/vn-11134207-7r98o-ltnk1p0ceya223.jpg?v=1730772583673",
		"pd_title": "KhÄƒn quáº¥n mÃ¹a hÃ¨ thay tháº¿ tÃºi ngá»§ cho bÃ© 0-1 tuá»•i",
		"pd_url": "/khan-quan-mua-he-thay-the-tui-ngu-cho-be-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/30320141497na-pearl-velvet-blanket-autumn-rose-detail-aw22-pp.jpg?v=1730773092447",
		"pd_title": "ChÄƒn XÃ´ Muslin, ChÄƒn xÃ´ nhung háº¡t Ä‘áº­u Ä‘áº¯p thu Ä‘Ã´ng",
		"pd_url": "/chan-xo-muslin-chan-xo-nhung-hat-dau-dap-thu-dong",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-7ce8db775f0b487d9b20a6fb956f92d4.jpg?v=1730192674797",
		"pd_title": "Miáº¿ng lÃ³t sÆ¡ sinh Huggies Skin Perfect NB1 64+6 miáº¿ng (DÆ°á»›i 5kg)",
		"pd_url": "/mieng-lot-so-sinh-huggies-skin-perfect-nb1-64-6-mieng-duoi-5kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777",
		"pd_title": "Bá»‰m Goldgi Eco DÃ¡n L56",
		"pd_url": "/bim-goldgi-eco-dan-l56-9-14kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-9c5c0eedf5594a579b367b1cab0f0060.jpg?v=1730192670537",
		"pd_title": "Bá»‰m Bobby quáº§n L68 (9-13kg)",
		"pd_url": "/bim-bobby-quan-l68-9-13kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/untitled-6-989b0ea3da524744909b957947b30571.png?v=1730192669953",
		"pd_title": "Bá»‰m Goldgi Eco Quáº§n XXL32",
		"pd_url": "/bim-goldgi-eco-quan-xxl32-15kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/untitled-6-d9aedadd2e3540f785b8352c45dcf122.png?v=1730192666927",
		"pd_title": "Bá»‰m Goldgi ECO dÃ¡n M66 (6-11kg)",
		"pd_url": "/bim-goldgi-eco-dan-m66-6-11kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/l-56-1c4632cf6e2246acac0c61942679538d.jpg?v=1730192665683",
		"pd_title": "Bá»‰m Goldgi+ X5 (TÃ£ dÃ¡n)",
		"pd_url": "/bim-goldgi-x5-dan",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/mieng-lot-molfix-thien-nhien-newborn-1-1-thang-90-mieng-664a6665b0994b47b55a7cc9b930234f.jpg?v=1730192664110",
		"pd_title": "Bá»‰m Molfix dÃ¡n NB1 90+10 (<1 thÃ¡ng)",
		"pd_url": "/bim-molfix-dan-nb1-90-10-1-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/bim-goldgi-eco-quan-xl44-12-17kg-5a0aa6d1dfda4639a8934facca9b1941.png?v=1730192662390",
		"pd_title": "Bá»‰m Goldgi ECO quáº§n XL44 (12-17kg)",
		"pd_url": "/bim-goldgi-eco-quan-xl44-12-17kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/cb8f2a1a334e4811a13d89ad6a9a4094-418fd93e208d42d981fb1267d2aac39b.jpg?v=1730192661427",
		"pd_title": "BIM Bá»‰m Huggies XL 60 miáº¿ng (cho bÃ© 11 - 16kg)",
		"pd_url": "/bim-bim-huggies-xl-60-mieng-cho-be-11-16kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/19d37b25d3c24fc4bf55edf13b14a56d-bc59a1d4701c4946ad962a80d2258b9f.png?v=1730192660477",
		"pd_title": "Bá»‰m Meries ná»™i Ä‘á»‹a quáº§n XXL 26+2 (15-28kg)",
		"pd_url": "/bim-meries-noi-dia-quan-xxl-26-2-15-28kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-f6e18460adcf46eb8fc8381d1831eeda.jpg?v=1730192659267",
		"pd_title": "Bá»‰m Moony dÃ¡n M56 (6-11kg)",
		"pd_url": "/bim-moony-dan-m56-6-11kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/untitled-6321-541e0aff85044904b153f83186f1dd76.png?v=1730192657537",
		"pd_title": "Bá»‰m Goldgi ECO quáº§n L48 (9-14kg)",
		"pd_url": "/bim-goldgi-eco-quan-l48-9-14kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/bim-ta-quan-merries-size-xl-38-6-mieng-cho-be-12-22kg-06953f90a5ac4b00a3cd39cb0965fcc2.jpg?v=1730192654073",
		"pd_title": "Bá»‰m Merries ná»™i Ä‘á»‹a quáº§n XL38 (12-22kg) - Cá»™ng miáº¿ng",
		"pd_url": "/bim-bim-merries-noi-dia-quan-xl38-12-22kg-cong-mieng",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/7-5cd5b7f5bc7746a48bafc0809999c094.png?v=1730192652887",
		"pd_title": "Bá»‰m Goldgi ECO quáº§n M54 (6-11kg)",
		"pd_url": "/bim-goldgi-eco-quan-m54-6-11kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-bot-meiji-so-9-nhat-800g-cho-be-1y-3y-14-4d04684b0a2f4291964ca30ac41af599.jpg?v=1730192650747",
		"pd_title": "Sá»¯a Meiji ná»™i Ä‘á»‹a Nháº­t Báº£n sá»‘ 9, 800g (1 - 3 tuá»•i)",
		"pd_url": "/sua-meiji-noi-dia-so-9-1-3-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-meiji-so-0-noi-dia-nhat-800g-cho-be-0-12m-2c6a66c942fc470b89c092cdd17464d7.jpg?v=1730192648710",
		"pd_title": "Sá»¯a Meiji ná»™i Ä‘á»‹a Nháº­t Báº£n sá»‘ 0, 800g (0 -  1 tuá»•i)",
		"pd_url": "/sua-meiji-noi-dia-nhat-ban-so-0-800g-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-thanh-meiji-so-0-noi-dia-nhat-648g-cho-be-0-1y-6-35d5c162e3814f5d86b5a0265d9fdf3f.jpg?v=1730802987087",
		"pd_title": "Sá»¯a Meiji ná»™i Ä‘á»‹a Nháº­t Báº£n dáº¡ng thanh sá»‘ 0, (0 - 1 tuá»•i)",
		"pd_url": "/sua-meiji-noi-dia-nhat-ban-dang-thanh-so-0-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-30ad4f905b9f480eadfdd56f417b7951.jpg?v=1730192642667",
		"pd_title": "Sá»¯a Aptamil Profutura Ãšc sá»‘ 1 (0-6 thÃ¡ng)",
		"pd_url": "/sua-aptamil-profutura-uc-so-1-0-6-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-meiji-growing-up-formula-448g-dang-thanh-cho-be-1y-3y-10-536a8136be7545c4ab3dfc4e784c4732.jpg?v=1730192642067",
		"pd_title": "Sá»¯a Meiji thanh nháº­p kháº©u Growing Up Formula (1 - 3 tuá»•i)",
		"pd_url": "/sua-sua-meiji-nhap-khau-dang-thanh-1-3-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/8384c62aad3d40b997d7d81d73e4d165-d3795f2537734b9ea143a62ee6433c05.jpg?v=1730192640737",
		"pd_title": "Sá»¯a Meiji Kids Formula 900g (3 - 10 tuá»•i)",
		"pd_url": "/sua-meiji-kids-formula-900g-3-10-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-sua-meiji-nhap-khau-dang-thanh-0-1-tuoi-5-6e359e2124074560b86f9ec08168dd33.jpg?v=1730192639857",
		"pd_title": "Sá»¯a Meiji thanh nháº­p kháº©u Infant Formula EZcube (0-1 tuá»•i)",
		"pd_url": "/sua-meiji-thanh-nhap-khau-0-1-tuoi-thanh-le",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/2-caeddf76b3a0438c83f8ffd2529fcb05.jpg?v=1730192635770",
		"pd_title": "Sá»¯a Aptamil Profutura Ãšc sá»‘ 2 (6-12 thÃ¡ng)",
		"pd_url": "/sua-aptamil-profutura-uc-so-2-6-12-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-meiji-infant-formula-nhap-khau-so-0-800g-0-1-tuoi-748b9bb0d02f43cdba7af74c86824203.jpg?v=1730192634477",
		"pd_title": "Sá»¯a Meiji nháº­p kháº©u sá»‘ 0 800g (0 - 1 tuá»•i)",
		"pd_url": "/sua-meiji-nhap-khau-so-0-800g-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-bot-meiji-growing-up-formula-800g-cho-be-1y-3y-1-1-078ff8bd992147d0ac074d87e70a666c.jpg?v=1730192633040",
		"pd_title": "Sá»¯a Meiji nháº­p kháº©u sá»‘ 9 lon 800g (1-3 tuá»•i)",
		"pd_url": "/sua-meiji-nhap-khau-so-1-800g-1-3-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-74b4489cc5c4432fb1c6da1b66d99e78.jpg?v=1730192630720",
		"pd_title": "Sá»¯a Nan Nga sá»‘ 1 Optipro (0-6 thÃ¡ng)",
		"pd_url": "/sua-nan-nga-so-1-optipro-0-6-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/f062b1b54fa2abb3417466a43265-90674bff370d4d519f24c7e79a104863-1-copy-cfdfbb979a1641348c3f8a327e476e4a.jpg?v=1730192629650",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Ã´i Galena GA-01",
		"pd_url": "/may-hut-sua-dien-doi-galena-ga-01",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-dien-doi-gluck-gp39-1-412bff21184e4eeab01c8b439d9c5653.jpg?v=1730192627767",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Ã´i Gluck Baby GP39",
		"pd_url": "/may-hut-sua-dien-doi-gluck-gp39",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/061d95f5f4ac45788cec7d3e7569f815-65321850c2e1473a82f949b9a3007c29.jpg?v=1730192625160",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Ã´i Gluck GP38 Plus",
		"pd_url": "/may-hut-sua-dien-doi-gluck-gp38plus-bh-1-nam",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/666174d31794401abcc8a63f91268bb5-ecfc33b70164490886fe747a0896be9a.png?v=1730192623383",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Ã´i Gluck GP60",
		"pd_url": "/may-hut-sua-dien-doi-gluck-gp60",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/a874ed4acf238c3637a7dbf270aefdc7.webp?v=1730779334893",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Ã´i Kamidi Max",
		"pd_url": "/may-hut-sua-dien-doi-kamidi-max",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-doi-ranh-tay-3-che-do-chibe-cb010-pin-sac-646af12691e5d-22052023113550.png?v=1730779418513",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Ã´i ráº£nh tay ChiBÃ© CB010",
		"pd_url": "/may-hut-sua-dien-doi-chibe-cb010",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/b8ff741412f247fa8edb90e98981cb2c-661446b802a944b2bf5189cbec26f044.jpg?v=1730192615120",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Ã´i Spectra 9 Plus",
		"pd_url": "/may-hut-sua-dien-doi-spectra-9-plus",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/12-54b71f98d7da4775884556aadd47a22f.jpg?v=1730192614230",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Æ¡n Gluck GP31 Plus",
		"pd_url": "/ddcm-may-hut-sua-dien-don-gluck-gp31-plus-bh-1-nam",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-dien-don-spectra-q.jpg?v=1730778453563",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n Ä‘Æ¡n Spectra Q (BH 2 nÄƒm)",
		"pd_url": "/may-hut-sua-dien-don-spectra-q-bh-2-nam",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-dien-don-spectra-m1-afa095a8f82b4f4081dbb1e63f666029-jpeg.jpg?v=1730192611980",
		"pd_title": "MÃ¡y hÃºt sá»¯a Ä‘iá»‡n M1 Spectra",
		"pd_url": "/may-hut-sua-dien-m1-spectra",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/template-web-48eeae2b48064b3b8c118dbd070dea42.jpg?v=1730192608820",
		"pd_title": "BÃ¡nh Äƒn dáº·m Gerber",
		"pd_url": "/banh-an-dam-gerber",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/ebf1858459584b22912942c768ca794e-99a3b46e209e4bb29e2cc4a3f6323e69.jpg?v=1730192607690",
		"pd_title": "BÃ¡nh gáº¡o há»¯u cÆ¡ Pororo - vá»‹ TÃ¡o&CÃ  rá»‘t - 25g/tÃºi",
		"pd_url": "/banh-gao-huu-co-pororo-vi-tao-ca-rot",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/9a485408666249de89f409e3d38f4235-1785c3e5687b4c6684f56588c86e575a.png?v=1730192607197",
		"pd_title": "BÃ¡nh gáº¡o Pigeon",
		"pd_url": "/banh-an-dam-pigeon",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1064d32b83e54b7680a8a9c04c49f7f8-bdeb44c0d63e469aaee391a8d568f067.jpg?v=1730192604997",
		"pd_title": "BÃ¡nh gáº¡o vá»‹ tÃ¡o vÃ  cherry Agusha (1Y+)",
		"pd_url": "/banh-gao-vi-tao-va-cherry-agusha-1y",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/aac0f8ba01ea4559bf8eb8ed937b6f7e-0c95cf4c66364041b23f467f92f0e745.jpg?v=1730192604117",
		"pd_title": "BÃ¡nh gáº¡o vá»‹ tÃ¡o, chuá»‘i vÃ  lÃª Agusha (1Y+)",
		"pd_url": "/banh-gao-vi-tao-chuoi-va-le-agusha-1y",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/vn-11134207-7qukw-li727huwgn4c03.webp?v=1730779808387",
		"pd_title": "BÃ¡nh há»¯u cÆ¡ gáº¡o lá»©t Pororo 25g",
		"pd_url": "/banh-huu-co-gao-lut-pororo-25g",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/z4898643141335-5de11f84ed92485e3f8fbd7f5871d1af-fb3c7fc591ac4bcaa3829456ff8b6706-master.jpg?v=1730779591473",
		"pd_title": "BÃ¡nh há»¯u cÆ¡ vá»‹ khoai lang, bÃ­ Ä‘á» Pororo 25g",
		"pd_url": "/banh-huu-co-vi-khoai-lang-bi-do-pororo-25g",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/681b03e9db154b6ea9395daaf7c619b3-aaa1fb414779474e984eb550db79272d.jpg?v=1730192601387",
		"pd_title": "BÃ¡nh Pororo vá»‹ chuá»‘i - 10g/tÃºi",
		"pd_url": "/banh-pororo-vi-chuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/c0c8397a57bf43eebbb3a6f9420fdde1-07bec5287b6648568da17cc4b85281d9.jpg?v=1730192600643",
		"pd_title": "BÃ¡nh Pororo vá»‹ sá»¯a chua dÃ¢u tÃ¢y - 10g/tÃºi",
		"pd_url": "/banh-huu-co-pororo-vi-sua-chua-dau-tay",
	}				]

		function showSalePop() {
			if(!faker) return
			$('#ega-sale-pop.salepop-show').removeClass('salepop-show').addClass('salespop-close');
			let pdRanIndex = Math.floor(Math.random() * salePopArr.length),
				salePopProduct = salePopArr[pdRanIndex],
				randomMin = `${Math.floor(Math.random() * 59) + 1} phÃºt`;
			const name= `${faker.name.lastName(customerGender)} ${faker.name.firstName(customerGender)}`
			const phone = `${faker.phone.phoneNumber().replace(' ')}`
			const address = faker.random.arrayElement(faker.locales.vi.address.city_root)
			const desc = salesPopDesc
			.replace(`[name]`, name)
			.replace(`[phone]`, `xxx${phone.substr(phone.length - 8)}`)
			.replace(`[time]`, randomMin)
			.replace('[address]', address)
			const salesPopContent = `
			<div class="sale-pop-img">
			<img src="${salePopProduct.img_url}" class="img-fluid" loading="lazy" alt="${salePopProduct.title}" width="180" height="180"/>
				</div>
			<div class="sale-pop-body">
			<div class="sale-pop-name">
			<a href="${salePopProduct.pd_url}" title="${salePopProduct.pd_title}">${salePopProduct.pd_title}</a>
				</div>
			<span class="sale-pop-desc">${desc}</span>
				</div>
`
			$('.sale-pop-wrap').html(salesPopContent)
			setTimeout(()=>{
				$('#ega-sale-pop').addClass('salepop-show').removeClass('salespop-close');
				setTimeout(()=>{
					$('#ega-sale-pop.salepop-show').removeClass('salepop-show').addClass('salespop-close');
					setTimeout(()=>{
						showSalePop()
					},timeDelay)
				},10000)
			},3000)


		}


	function initSalesPop() {
		if(!salePopArr.length) return;
		setTimeout(()=>{
		$.getScript(fakerScript).then(()=>{
		if(faker){
				showSalePop()
			}
		})

		},timeDelay)


	}

	$(document).ready(() => {
		$('#ega-sale-pop').removeClass('hidden');
			initSalesPop()
			$(".sale-pop-close").click(function () {
				$("#ega-sale-pop").removeClass('salepop-show').removeClass('salespop-close');
			})
			$(".sale-pop-cta").click(function (e) {
				e.preventDefault();
				$(".sale-pop-regis").show();
			})
			$(".regis-close").click(function () {
				$(".sale-pop-regis").hide();
			})
	})


</script>
@endif


	<link rel="stylesheet" href="100/531/894/themes/1018832/assets/addthis-sharing.css?1768901692132" media="print" onload="this.media='all'">

<noscript><link href="100/531/894/themes/1018832/assets/addthis-sharing.css?1768901692132" rel="stylesheet" type="text/css" media="all"></noscript>
<div class="addThis_listSharing">
			{{--
			<div class="floating_banner relative">
			<a class="block p-2 hover:scale-105 transition-all" href="https://traicaywinwin.com/" title="sticky banner">
				<img src="100/531/894/themes/1018832/assets/floating-banner.png?1768901692132" alt="sticky banner" width="100" height="100">
			</a>
			<button class="btn p-0  absolute -top-3 right-1 link">
				 <i class="icon icon-cross"></i>
			</button>
		</div>
			--}}

<a href="#" id="back-to-top" class="backtop back-to-top flex items-center justify-center" title="LÃªn Ä‘áº§u trang">
	<i class="icon icon-carret-up"></i>
</a>

					<div class="header-box-live dnone" aria-hidden="true">
						<button type="button" data-mobile-link="https://fb.watch/t6bCpir7ve/" style="--header-live-color:#ff0000" class="btn-live">
							<span class="relative z-[1] font-semibold">LIVE</span></button>
					</div>
	<call-center-group class="addThis_group active" style="--color-primary: #0ea5e9">


	<ul class="addThis_listing list-unstyled hidden md:block">

		@if($wwHotline)
		<li class="addThis_item" data-ww-social>
			<a class="addThis_item--icon" href="{{ $wwHotline['tel'] }}" data-ww-contact="hotline" rel="nofollow">
				<img class="img-fluid" src="100/531/894/themes/1018832/assets/addthis-phone.svg?1768901692132" alt="Gọi điện thoại" loading="lazy" width="44" height="44">
				<span class="tooltip-text">Gọi điện thoại</span>
			</a>
		</li>
		@endif
		@if($wwZalo !== '')
		<li class="addThis_item" data-ww-social>
			<a class="addThis_item--icon" href="{{ $wwZalo }}" data-ww-contact="zalo" target="_blank" rel="nofollow">
				<img class="img-fluid" src="100/531/894/themes/1018832/assets/addthis-zalo.svg?1768901692132" alt="Chat với chúng tôi qua Zalo" loading="lazy" width="44" height="44">
				<span class="tooltip-text">Chat với chúng tôi qua Zalo</span>
			</a>
		</li>
		@endif
		@if($wwMessenger !== '')
			<li class="addThis_item" data-ww-social>
				<a class="addThis_item--icon" href="{{ $wwMessenger }}" data-ww-contact="messenger" target="_blank" rel="nofollow">
					<img class="img-fluid" src="100/531/894/themes/1018832/assets/addthis-messenger.svg?1768901692132" alt="Chat với chúng tôi qua Messenger" loading="lazy" width="44" height="44">
					<span class="tooltip-text">Chat với chúng tôi qua Messenger</span>
				</a>
			</li>
		@endif

	</ul>
	{{-- NÃºt toggle (addThis_item--toggle): áº©n â€” hiá»ƒn thá»‹ luÃ´n phone / Zalo / Messenger á»Ÿ trÃªn
	<div class="addThis_item relative z-[1]" data-toggle="">
			<div class="addThis_item--icon addThis_item--toggle rounded-full">
			<img src="100/531/894/themes/1018832/assets/call-center.png?1768901692132" alt="call-center" width="30" height="30">
								 <i class="icon icon-cross"></i>

			</div>
		</div>
	--}}
		</call-center-group>
</div>
<script>
		class CallCenterGroup extends HTMLElement {
				constructor() {
					super();
					this.toggleButton = this.querySelector('[data-toggle]');
					this.listing = this.querySelector('.addThis_listing');
					if (this.toggleButton && this.listing) {
						this.addClickListener();
					}
				}

				addClickListener() {
					this.toggleButton.addEventListener('click', () => this.toggleListing());
				}

				toggleListing() {
					const isShowing = this.listing.classList.toggle('show');
					const animations = {
						show: [
							{ transform: 'translateY(60%)', opacity: 0 },
							{ transform: 'translateY(0)', opacity: 1 }
						],
						hide: [
							{ transform: 'translateY(0)', opacity: 1 },
							{ transform: 'translateY(60%)', opacity: 0 }
						]
					};

					if (isShowing) {
						this.listing.classList.remove('dnone');
						this.classList.add('active')
						this.animate(animations.show);
					} else {
						this.classList.remove('active');
						this.animate(animations.hide, () => this.listing.classList.add('dnone'));
					}
				}

				animate(keyframes, onFinish) {
					this.listing.animate(keyframes, {
						duration: 300,
						easing: 'ease',
						fill: 'forwards'
					}).onfinish = onFinish;
				}
			}

			customElements.define('call-center-group', CallCenterGroup);
		</script>
	<div data-template="index" class="cro-btns sticky md:hidden block z-10 min-h-[5.6rem] bottom-0 left-0  slide-in-bottom  ">
  <div class=" bg-background rounded-t-sm w-full min-h-[5.6rem] px-2 justify-between items-center inline-flex slide-in-bottom" style="box-shadow:var(--shadow-l)">
        <div class="cro-btns-container w-full h-full justify-center items-center gap-0.5 grid grid-cols-[repeat(auto-fit,minmax(0,1fr))]">

      @if($wwZalo !== '')
                                                            	              <a data-ww-social class="cro-btn-item cro-btn-item--1 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5  text-foreground h-full flex flex-col justify-center items-center gap-0.5" title="Zalo" href="{{ $wwZalo }}" data-ww-contact="zalo" target="_blank" rel="noopener noreferrer" style="order:4">
        <div class="w-4 h-4 relative ">
          <img class="w-full h-full object-contain" alt="Zalo" src="100/531/894/themes/1018832/assets/addthis-zalo.svg?1768901692132" loading="lazy" width="16" height="16">

        </div>
        <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Zalo</div>
      </a>
      @endif


                                                      	              <portal-opener class="cro-btn-item cro-btn-item--2 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5 text-foreground h-full flex flex-col justify-center items-center gap-0.5" style="order:1">
        <a class="w-full h-full flex flex-col justify-center items-center gap-0.5" title="Sản phẩm" href="javascript:void(0)" data-portal="#menu-drawer" role="button">
          <div class="w-4 h-4 relative ">
            <img class="w-full h-full object-contain" alt="Sản phẩm" src="thumb/small/100/531/894/themes/1018832/assets/cro-btn-2-icon.png?1768901692132" loading="lazy">
          </div>
          <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Sản phẩm</div>
        </a>
      </portal-opener>

      @php
        $themeCartQty = $themeCartQty ?? collect(session('theme_storefront_cart', []))->sum(fn ($line) => (int) ($line['quantity'] ?? 0));
      @endphp
      <portal-opener class="cro-btn-item cro-btn-item--cart w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5 text-foreground h-full flex flex-col justify-center items-center gap-0.5" style="order:2">
        <a class="w-full h-full flex flex-col justify-center items-center gap-0.5" title="Giỏ hàng" href="{{ url('/cart') }}" data-portal="#cart-drawer" role="button">
          <div class="w-4 h-4 relative flex items-center justify-center">
            <i class="icon icon-cart cro-btn-cart-icon" aria-hidden="true"></i>
            <span class="cart-count flex items-center count_item count_item_pr justify-center rounded-full absolute font-semibold"><span class="cart-count__num">{{ $themeCartQty }}</span></span>
          </div>
          <div class="text-ellipsis overflow-hidden max-w-full text-xs text-center line-clamp-1">Giỏ hàng</div>
        </a>
      </portal-opener>


      @if($wwMessenger !== '')
                                                      	              <a data-ww-social class="cro-btn-item cro-btn-item--3 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5  text-foreground h-full flex flex-col justify-center items-center gap-0.5" title="Messenger" href="{{ $wwMessenger }}" data-ww-contact="messenger" target="_blank" rel="noopener noreferrer" style="order:3">
        <div class="w-4 h-4 relative ">
          <img class="w-full h-full object-contain" alt="Messenger" src="100/531/894/themes/1018832/assets/addthis-messenger.svg?1768901692132" loading="lazy" width="16" height="16">

        </div>
        <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Messenger</div>
      </a>
      @endif


      @if($wwHotline)
                                                      	              <a data-ww-social class="cro-btn-item cro-btn-item--4 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5  text-foreground h-full flex flex-col justify-center items-center gap-0.5" title="Điện thoại — {{ $wwHotline['display'] }}" href="{{ $wwHotline['tel'] }}" data-ww-contact="hotline" style="order:5">
        <div class="w-4 h-4 relative ">
          <img class="w-full h-full object-contain" alt="Điện thoại" src="100/531/894/themes/1018832/assets/addthis-phone.svg?1768901692132" loading="lazy" width="16" height="16">

        </div>
        <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Điện thoại</div>
      </a>
      @endif


    </div>
            </div>
</div>
	@include('UI-FRONTEND.partials.search-drawer')

	<quick-view class="portal portal--modal" id="quick-view-product" data-type="modal" data-animation="scale-in-hor-left">
		<dialog class="portal-dialog">
			<div class=" flex items-center justify-center w-full h-full">
				<div class="portal-overlay"></div>

            <div class="portal-inner    h-full  ">
				  <button type="button" id="PortalClose-quick-view-product" data-animation="fade-in" class="portal-close-button animation rounded-full w-[3.2rem] h-[3.2rem]  border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin">
                  <i class="icon icon-cross"> </i>
                </button>
				<div class="product-wrapper animation  bg-background  w-full h-full  md:rounded-lg">

				</div>
				<span class="loading-icon gap-1 hidden items-center justify-center">

            <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>

            <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>

            <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>

</span>
              </div>
			</div>

        </dialog>

	</quick-view>

<script src="100/531/894/themes/1018832/assets/product.js?ww-portal-opener-fix-1" defer fetchpriority="low"></script>
	<script src="100/531/894/themes/1018832/assets/quick-view-enhance.js?ww-qv-cart-prompt-1" defer fetchpriority="low"></script>

	@include('UI-FRONTEND.common.cart-drawer')

<portal-component class="portal portal--drawer" id="cart-note-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">
  <dialog class="portal-dialog">
    <div class=" w-full h-full flex">
      <div class="portal-overlay"></div>
      <div class="portal-inner w-full ml-auto bg-background   h-screen px-4 animation">
        <div class="portal-header px-3 pb-3 pt-5 ">
          <div class="font-semibold text-h4 flex items-center gap-2">
            <i class="icon icon-arrow-left  cursor-pointer text-h6 md:text-h4" id="PortalClose-cart-note-drawer"></i>Ghi
            chÃº Ä‘Æ¡n hÃ ng
          </div>
        </div>
        <div class="r-bill px-3">
          <cart-note>
            <form>
              <div class="form-group">
                <label class="label block mb-2">Ghi chÃº</label>
                <textarea class="form-textarea" name="note" rows="6"></textarea>
              </div>
              <button type="submit" class="btn w-full mt-5  btn--large font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2">
                LÆ°u thÃ´ng tin
              </button>
            </form>
          </cart-note>
        </div>
      </div>
    </div>
  </dialog>
</portal-component>
<cart-vat-drawer class="portal portal--drawer" id="cart-vat-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">

    <dialog class="portal-dialog">
      <div class=" w-full h-full flex">
        <div class="portal-overlay"></div>
        <div class="portal-inner w-full ml-auto bg-background   h-screen px-4 animation">
			<div class="portal-header px-3 pb-3 pt-5 ">
				<div class="font-semibold text-h6 md:text-h4 flex items-center gap-2">
			<i class="icon icon-arrow-left text-h6 md:text-h4  cursor-pointer" id="PortalClose-cart-vat-drawer"></i>
					Xuáº¥t hÃ³a Ä‘Æ¡n cÃ´ng ty
				</div>
			</div>
         <div class="r-bill px-3">
			 <form>

<div class="bill-field  space-y-3 ">

		   <div class="flex items-center">
        <div class="flex items-center ">
		<input class="invoice" type="hidden" name="attributes[Xuáº¥t hÃ³a Ä‘Æ¡n]" value='khÃ´ng'>
          <input class="invoice-checkbox form-checkbox" type="checkbox">
        </div>
        <div class="ml-2 text-sm">
          <label>Xuáº¥t hÃ³a Ä‘Æ¡n</label>
        </div>
      </div>
		<div class="form-group">
			<label class="label block mb-2">TÃªn cÃ´ng ty</label>
			<input type="text" class="form-input" name="attributes[TÃªn cÃ´ng ty]" value="" data-rules="['required']" data-messages="{'required':'TrÆ°á»ng nÃ y khÃ´ng Ä‘Æ°á»£c bá» trá»‘ng' }" placeholder="TÃªn cÃ´ng ty">
			<span class="error  text-error mt-2 block "></span>
		</div>
		<div class="form-group">
			<label class="label block mb-2">MÃ£ sá»‘ thuáº¿</label>
			<input type="number" class="form-input" name="attributes[MÃ£ sá»‘ thuáº¿]" value="" data-rules="['minLength:10','required']" data-messages="{ 'minLength:10': 'Sá»‘ kÃ­ tá»± tá»‘i thiá»ƒu [size]', 'require':'TrÆ°á»ng nÃ y khÃ´ng Ä‘Æ°á»£c bá» trá»‘ng' }" placeholder="MÃ£ sá»‘ thuáº¿">
			 <span class="error text-error mt-2 block "></span>

		</div>
		<div class="form-group">
			<label class="label block mb-2">Äá»‹a chá»‰ cÃ´ng ty</label>
			<textarea class="form-textarea" data-rules="['required']" data-messages="{'required':'TrÆ°á»ng nÃ y khÃ´ng Ä‘Æ°á»£c bá» trá»‘ng' }" name="attributes[Äá»‹a chá»‰ cÃ´ng ty]" placeholder="Äá»‹a chá»‰ cÃ´ng ty"></textarea>
			<span class="error  text-error mt-2 block "></span>

		</div>
		<div class="form-group">
			<label class="label block mb-2">Email nháº­n hÃ³a Ä‘Æ¡n</label>
			<input type="email" class="form-input" name="attributes[Email nháº­n hÃ³a Ä‘Æ¡n]" value="" placeholder="Email nháº­n hÃ³a Ä‘Æ¡n" data-rules="['required','email']" data-messages="{'required':'TrÆ°á»ng nÃ y khÃ´ng Ä‘Æ°á»£c bá» trá»‘ng', 'email': 'Email khÃ´ng Ä‘Ãºng Ä‘á»‹nh dáº¡ng' }">
						<span class="error  text-error mt-2 block "></span>

		</div>

	</div>
				  <button type="submit" class="btn w-full mt-5  btn--large font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2">
        	LÆ°u thÃ´ng tin

          </button>
			 </form>
		</div>
        </div>
      </div>
    </dialog>

</cart-vat-drawer>
<portal-component class="portal portal--drawer" id="cart-delivery-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">
  <dialog class="portal-dialog">
    <div class=" w-full h-full flex">
      <div class="portal-overlay"></div>
      <div class="portal-inner w-full ml-auto bg-background   h-screen px-4 animation">
        <div class="portal-header px-3 pb-3 pt-5 ">
          <div class="font-semibold text-h6 md:text-h4 flex items-center gap-2">
            <i class="icon icon-arrow-left text-h4 cursor-pointer" id="PortalClose-cart-delivery-drawer"></i>
Háº¹n giá» nháº­n hÃ ng
          </div>
        </div>
        <div class="r-bill px-3">
          <cart-delivery>
            <form>
              <div class="cart-delivery-time py-4">
      <div class="grid gap-2">
		   <div class="flex items-center">
        <div class="flex items-center ">
			<input id="use-delivery" type="hidden" name="attributes[Háº¹n giá» nháº­n hÃ ng]" value="cart.attributes[Háº¹n giá» nháº­n hÃ ng]">
          <input id="delivery-enabled" type="checkbox" class="form-checkbox">
        </div>
        <div class="ml-2 text-sm">
          <label for="delivery-enabled" class="">Háº¹n giá» nháº­n hÃ ng</label>
        </div>
      </div>
        <div>
          <label for="delivery-date-input" class="label block mb-1 text-base">NgÃ y nháº­n hÃ ng</label>
          <div class="relative">
            <i class="icon icon-calendar text-neutral-200 top-1/2 left-2 -translate-y-1/2 absolute"></i>
			<datepicker-input class="datepicker-input">
				<input type="text" value="" name="attributes[NgÃ y nháº­n hÃ ng]" id="delivery-date-input" class="form-input pl-2 pl-[var(--spacing-6-5)] py-2.5 min-h-[4rem]">
			  </datepicker-input>
          </div>
        </div>
        <div>
          <label for="delivery-time" class="label block mb-1 text-base ">Thá»i gian nháº­n hÃ ng</label>
          <select id="delivery-time" name="attributes[Thá»i gian nháº­n hÃ ng]" class="form-select min-h-[4rem]  px-2 py-2.5 border-neutral-50">
            <option selected="" value="">Chá»n thá»i gian</option>

				<option value="08h00 - 12h00">
					08h00 - 12h00
			  	</option>

				<option value="14h00 - 18h00">
					14h00 - 18h00
			  	</option>

				<option value="19h00 - 21h00">
					19h00 - 21h00
			  	</option>
			  			            </select>
        </div>

      </div>

    </div>              <button type="submit" class="btn w-full mt-5  btn--large font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2">
                LÆ°u thÃ´ng tin
              </button>
            </form>
          </cart-delivery>
        </div>
      </div>
    </div>
  </dialog>
</portal-component>

@if(false)
{{-- Popup thÃ´ng bÃ¡o sau khi thÃªm sáº£n pháº©m vÃ o giá» (táº¯t) --}}
<add-to-cart-popup class="portal" id="add-to-cart-popup" data-animation="fade-in">
  <dialog class="portal-dialog">
      <div class="flex items-center justify-center w-full h-full p-4">
        <div class="portal-overlay"></div>
        <div class="portal-inner  popup-content max-w-[400px] relative w-full">
        	<button type="button" id="PortalClose-add-to-cart-popup" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border flex items-center justify-center active:scale-95 transition-transform">
            <i class="icon icon-cross"> </i>
          </button>
          <div class="popup-content w-full bg-white rounded-lg ">
            <div class="popup-header text-success bg-relative py-2 px-4">
              <span class="font-semibold">âœ” ThÃªm vÃ o giá» hÃ ng thÃ nh cÃ´ng</span>
            </div>
            <div class="popup-body grid grid-cols-[80px_1fr] gap-2 p-4">
              <img class="popup-product-img" src="" alt="" style="width:80px;">
              <div>
                <div class="popup-product-title font-semibold"></div>
                <div class="popup-product-variant text-neutral-200 text-sm"></div>
              </div>
            </div>
            <div class="popup-footer p-4 border-t border-neutral-50">
              <div class="flex gap-2 justify-between">
                Giá» hÃ ng hiá»‡n cÃ³
                <div class="whitespace-nowrap text-right">
                  <div class="popup-cart-total price font-semibold"></div>
                 <div class="text-neutral-200"> (<span class="popup-cart-quantity"></span> sáº£n pháº©m)</div>
                </div>
              </div>
              <div class="flex gap-2 pt-4">
              <button type="button" class="btn w-full font-semibold border border-neutral-50 inline-flex justify-center items-center gap-2 hover:bg-neutral-100" onclick="document.querySelector('#add-to-cart-popup').hide()">Quay láº¡i</button>
              <button type="button" class="btn btn-cart font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2 w-full">Xem giá» hÃ ng</button>
              </div>
            </div>
          </div>
        </div>
      </div>
  </dialog>
</add-to-cart-popup>
@endif
	<coupon-drawer lass="portal portal--drawer" id="coupon-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">

  <dialog class="portal-dialog">
    <div class=" w-full h-full flex ">
      <div class="portal-overlay"></div>
      <div class="portal-inner  grid grid-rows-[auto_1fr_auto] w-full ml-auto bg-background  h-screen px-4 pb-4 animation">
        <div class="portal-header px-3 pb-3 pt-5 ">
          <div class="font-semibold text-h6 md:text-h4 flex items-center gap-2">
            <i class="icon icon-arrow-left text-h6 md:text-h4  cursor-pointer" id="PortalClose-coupon-drawer"></i> Chá»n mÃ£ giáº£m giÃ¡
          </div>
        </div>
        <div class="coupon-list py-4"></div>
		  <div>

		  </div>
      </div>
    </div>
  </dialog>
</coupon-drawer>

<coupon-modal class="portal portal--modal portal--modal-sm" id="coupon-modal" data-type="modal" data-animation="fade-in">
  <dialog class="portal-dialog">
    <div class=" flex items-center justify-center w-full h-full p-3">
      <div class="portal-overlay"></div>
      <div class="portal-inner animation ">
		  		<button type="button" id="PortalClose-coupon-modal" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin">
                  <i class="icon icon-cross "> </i>
                </button>
		  <div class=" p-3 md:p-5 rounded-lg  bg-background   h-full grid grid grid-rows-[auto_1fr_auto]">
		<div class="coupon-header  border-b pb-2.5 border-dashed border-neutral-50"></div>
		  <div class="coupon-desc "></div>
		  <div class="coupon-act grid grid-cols-2 gap-3 border-t  pt-2.5 border-dashed border-neutral-50">
			<button id="PortalClose-coupon-item-modal" class="btn  border border-neutral-50 hover:bg-neutral-100 py-1.5 ">ÄÃ³ng</button>
		 	<copy-button data-copied-text="ÄÃ£ sao chÃ©p">
                <input type="hidden" value="">
                <button type="button" class="btn copy-button w-full font-semibold text-primary border border-primary bg-primary text-white  py-1.5 ">
  					Sao chÃ©p
      			</button>
        </copy-button>
		  </div>
		</div>
      </div>
    </div>
  </dialog>
</coupon-modal>
	{{-- So sÃ¡nh sáº£n pháº©m: modal + nÃºt má»Ÿ (táº¡m táº¯t)
	<compare-qv class="portal portal--modal" id="quick-view-compare" data-type="modal" data-animation="slide-in-bottom">
  <dialog class="portal-dialog">
    <div class=" flex items-center justify-center w-full">
      <div class="portal-overlay "></div>
      <div class="portal-inner bg-background rounded-sm animation">
		   <button type="button" id="PortalClose-quick-view" class="absolute active:scale-95 transition-transform right-0 p-3 bg-background rounded-sm flex items-center justify-center gap-2 link">
			   			   Thu gá»n
                  <i class="icon icon-carret-down"> </i>
                </button>
			<div class="compare-product-list">

		  </div>

	</div>
    </div>
  </dialog>
</compare-qv>

  <portal-opener class="compare-opener hidden">
			<div class=" cursor-pointer font-semibold btn rounded-full bg-background border border-primary text-primary" data-portal="#quick-view-compare">
			  <p class="flex items-center gap-1">
				  	  <span class="line-clamp-1">  So sÃ¡nh </span>
				  				  	  <span class="compare-count"> </span>

				  <i class="ico icon-arrow-swap"></i>
				 </p>

			</div>
		  </portal-opener>
	--}}

	{{-- Popup banner ưu đãi (tắt) --}}
	@if(false)
	<promo-popup class="portal portal--modal" id="promo-popup" data-type="modal" data-animation="fade-in">
		<dialog class="portal-dialog">
			<div class=" flex items-center justify-center w-full h-full">
				  <div class="portal-overlay"></div>
            <div class="relative z-10 animation p-6 md:p-4">
				<button type="button" id="PortalClose-promo-popup" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin">
                  <i class="icon icon-cross"> </i>
                </button>
				 <a href="collections/all.html" title="Click ngay Ä‘á»ƒ nháº­n Æ°u Ä‘Ã£i hot!!">
                        <img loading="lazy" class="object-contain" src="100/531/894/themes/1018832/assets/banner-popup-img.png?1768901692132" alt="Click ngay Ä‘á»ƒ nháº­n Æ°u Ä‘Ã£i hot!!" width="540" height="540">
                    </a>
              </div>
			</div>

        </dialog>

	</promo-popup>
	@endif

	<error-popup class="portal portal--modal portal--modal-sm" id="error-modal" data-type="modal" data-animation="fade-in">
  <dialog class="portal-dialog">
    <div class="  flex items-start justify-end  p-3 w-full h-full">
      <div class="portal-overlay"></div>
      <div class="portal-inner animation ">

		  <div class="error-list flex gap-4 flex-col items-end">

		</div>
      </div>
    </div>
  </dialog>
</error-popup>
  {{-- Popup live Facebook (theme Sapo) â€” táº¯t: gÃ¢y cáº£nh bÃ¡o Permissions-Policy unload trong console --}}
