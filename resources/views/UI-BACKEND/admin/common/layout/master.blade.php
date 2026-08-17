<?php 
   $timeUpdate = config('app.cache_version'); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
<!-- Mã CSRF token trong tất cả các form gửi request POST, PUT, PATCH hoặc DELETE. -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Required meta tags -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<!-- /Added by HTTrack -->
<meta name="viewport"
	content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">
<title>Đồ Chơi Win Win - Quản trị hệ thống</title>
<meta name="description" content="Trang quản trị Đồ Chơi Win Win giúp quản lý sản phẩm đồ chơi và nội dung website một cách thuận tiện, hiệu quả.">
<meta name="keywords" content="Đồ Chơi Win Win, quản trị cửa hàng, quản lý sản phẩm, đồ chơi trẻ em">
<!-- base:css -->
<link rel="stylesheet"
	href="{{ asset('css/UI-BACKEND/vendors/typicons/typicons.css') }}?v={{ $timeUpdate }}">
<link rel="stylesheet"
	href="{{ asset('css/UI-BACKEND/vendors/css/vendor.bundle.base.css') }}?v={{ $timeUpdate }}">
<!-- endinject -->
<!-- plugin css for this page -->
<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/font-awesome/css/font-awesome.min.css') }}?v={{ $timeUpdate }}"/>
<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/simple-line-icons/css/simple-line-icons.css') }}?v={{ $timeUpdate }}">
<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/ti-icons/css/themify-icons.css') }}?v={{ $timeUpdate }}">

<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/jquery-toast-plugin/jquery.toast.min.css') }}?v={{ $timeUpdate }}">

<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/select2/select2.min.css') }}?v={{ $timeUpdate }}">
<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/select2/select2totree.css') }}?v={{ $timeUpdate }}">
<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}?v={{ $timeUpdate }}">

<!-- START thư viện cropper.js -->
<link rel="stylesheet" href="{{ asset('js/UI-BACKEND/vendors/jquery-cropper-js/dist/cropper.css') }}?v={{ $timeUpdate }}">
<!-- END thư viện cropper.js -->

<!-- START thư viện Datatables -->
<link href="{{ asset('css/UI-BACKEND/vendors/datatables/datatables.min.css') }}?v={{ $timeUpdate }}" rel="stylesheet">
<link href="{{ asset('css/UI-BACKEND/vendors/datatables/custom-datatables.css') }}?v={{ $timeUpdate }}" rel="stylesheet">
<!-- START thư viện Datatables -->

@yield('plugin-css-for-this-page')
<!-- End plugin css for this page -->
<!-- inject:css -->
<link rel="stylesheet"
	href="{{ asset('css/UI-BACKEND/vertical-layout-light/style.css') }}?v={{ $timeUpdate }}">
<!-- endinject -->
<!-- Menu highlighting CSS -->
<link rel="stylesheet"
	href="{{ asset('css/UI-BACKEND/menu-highlighting.css') }}?v={{ $timeUpdate }}">
<!-- endinject -->
<link rel="shortcut icon"
	href="{{ storefrontFaviconUrl() }}" />
<style>
	/* CSS để vô hiệu hóa tất cả các sự kiện bằng thuộc tính pointer-events: none;. 
	Điều này sẽ khiến tất cả các sự kiện tương tác như click, hover không hoạt động. */
	.disable-events {
		pointer-events: none;
	}

	textarea {
		line-height: 1.5 !important;
	}
	
	.modal .modal-dialog .modal-content .modal-body {
		padding: 1.2rem;
	}
 
	.accordion .card-body {
        padding: 1rem 1rem !important;
    }

    /* custom scrollbar */
    /* ::-webkit-scrollbar {
        width: 20px;
    }
    
    ::-webkit-scrollbar-track {
        background-color: transparent;
    }
    
    ::-webkit-scrollbar-thumb {
        background-color: #d6dee1;
        border-radius: 20px;
        border: 6px solid transparent;
        background-clip: content-box;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background-color: #a8bbbf;
    } */

    .form-group {
        margin-bottom: 1.2rem;
    }

    .text-align-right {
        text-align: right;
    }
    .one-line {
        display: inline-block;
    }
	.two-line {
		white-space: pre-wrap !important;
		overflow: hidden;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
	}
	.text-wrap-auto {
		text-wrap: auto !important;
	}

	.text-left {
		text-align: left !important;
	}
 
	.display-block {
    	display: block;
    }
    .width-50 {
        width: 50px !important
    }
	.width-60 {
        width: 60px !important
    }
	.width-70 {
        width: 70px !important
    }
	.width-80 {
        width: 80px !important
    }
	.width-85 {
        width: 85px !important
    }
	.width-90 {
        width: 90px !important
    }
	.width-100 {
        width: 100px !important
    }
	.width-110 {
        width: 110px !important
    }
	.aspect-ratio-4-3 {
        aspect-ratio: 4/3;
    }
	.aspect-ratio-5-3 {
        aspect-ratio: 5/3;
    }
	.aspect-ratio-1-1 {
        aspect-ratio: 1/1;
    }
	.aspect-ratio-3-2 {
        aspect-ratio: 3/2;
    }
    .height-45 {
        height: 45px !important
    }
    .width-100-percent {
        width: 100%;
    }
    .rounded-3 {
        border-radius: 0.5rem !important;
    }
    
    .text-decoration-none {
        text-decoration: none;    
    }
    .float-right {
        float: right;
    }
    .margin-top-15px {
        margin-top: 15px;
    }
    .padding-top-15px {
        padding-top: 15px;
    }
    .border-radius-10px {
        border-radius: 10px !important;
    }
    .border-radius-5px {
        border-radius: 5px !important;
    }
    .border-radius-2px {
        border-radius: 2px !important;
    }
    .border-radius-0px {
        border-radius: 0px !important;
    }
    .border-top-left-radius-0px {
        border-top-left-radius: 0px !important;
    }
    .border-top-right-radius-0px {
        border-top-right-radius: 0px !important;
    }
    .border-bottom-left-radius-0px {
        border-bottom-left-radius: 0px !important;
    }
    .border-bottom-right-radius-0px {
        border-bottom-right-radius: 0px !important;
    }

    .input-group .input-group-prepend .dropdown-toggle {
        height: 100%;
    }

    select.form-control-default {
        padding: 0.76rem 2.45rem 0.82rem 0.79rem;
		/* height: 100%; */
		padding-left: 0.79rem;
		padding-right: 2.45rem;
    }
    /* Start css btn outline blue same color navbar */
    .btn-outline-blue {
    	color: black;
    	border-color: #1a89f7;
    }
    
    .btn-check:checked+.btn-outline-blue {
    	color: #ffffff;
    	background-color: #3b86d1;
    	border-color: #1a89f7;
    }
    /* End css btn outline blue same color navbar */

    /* Start css switch blue */
    /* The switch - the box around the slider */
    .switch {
    	position: relative;
    	display: inline-block;
    	width: 48px;
    	height: 28px;
    }
    
    /* Hide default HTML checkbox */
    .switch input {
    	display: none;
    }
    
    /* The slider */
    .slider {
    	position: absolute;
    	cursor: pointer;
    	top: 0;
    	left: 0;
    	right: 0;
    	bottom: 0;
    	background-color: #f4f5fa;
    	-webkit-transition: .4s;
    	transition: .4s;
    	border: 1px solid silver;
    	border-radius: 5px;
    }
    
    .slider:before {
    	position: absolute;
    	content: "";
    	height: 18px;
    	width: 18px;
    	left: 4px;
    	bottom: 4px;
    	background-color: white;
    	-webkit-transition: .4s;
    	transition: .4s;
    	border: 1px solid #c7c2c2;
    	border-radius: 3px;
    }
    
    input.primary:checked+.slider {
    	background-color: #3b86d1;
    }
    
    input:focus+.slider {
    	box-shadow: 0 0 1px #2196F3;
    }
    
    input:checked+.slider:before {
    	-webkit-transform: translateX(20px);
    	-ms-transform: translateX(20px);
    	transform: translateX(20px);
    }
    /* End css switch blue */

    #loadingAjaxCall {
        background-color:#eeeeff;
        position: fixed;
        display: block;
        top: 0;
        bottom: 0;
        z-index: 1000000;
        opacity: 0.3;
        width: 100%;
        height: 100%;
        text-align: center;
    }
    
    #loadingAjaxCall img {
        margin: auto;
        display: block;
        top: calc(50% - 100px);
        left: calc(50% - 10px);
        position: absolute;
        z-index: 999999;
    }

    .inline-block {
        display: inline-block !important;
    }

    /* css button go back của modal */
    .modal .section-go-back {
        width: 30px;
    }

    .modal .icon.go-back {
        fill: #6a6a6a;
        stroke: #6a6a6a;
    }
    .modal .icon.go-back:hover{
        fill: black;
        stroke: black;
    }
    
    /* css hover button close của modal */
    .modal .close span:hover{
        /* color: black !important; */
    }

    /* Start css button combobox */
    .btn.btn-combobox {
		width: 100%;
		text-align: left;
		background: unset;
		border-color: #afafaf;
		color: #000;
		position: relative;
		overflow: hidden;
		white-space: nowrap;
		padding: 0.825rem 1.0rem;
		min-height: 3.09rem;
    }
	.btn.btn-combobox span {
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 97%;
		display: inline-block;
		padding-top: 0.2rem;
	}

	.dropdown .btn-combobox.dropdown-toggle:after {
        float: right;
		color: #aaa;
		position: absolute;
		right: 10px;
		top: 50%;
		transform: translateY(-50%);
    }
    .dropdown .btn-combobox.dropdown-toggle:focus {
        border-color: #86b7fe;
    }
    /* End css button combobox */

    @media (min-width: 1200px) { /* lg breakpoint in Bootstrap */
        .col-lg-5-5 {
            width: 48.9%;
        }

		.dataTable-td-stt {
			width: 50px ;
		}
		
		.dataTable-td-thao-tac {
			width: 100px !important;
		}
    }

    @media (min-width: 992px) and (max-width: 1200px) { /* lg breakpoint in Bootstrap */
        .dataTable-td-thao-tac {
			width: 100px;
		}
    }   

    @media ( max-width : 1025px) {              
    	.btn-light.dropdown-toggle {
           padding: 0.44rem 0.8rem;
    	}

		
    }

    @media ( max-width : 767px) {              
    	.footer {
    	    padding-left: 1rem;
            padding-right: 1rem;
    	}

		
    }

    @media (min-width: 576px) {
        .action-mobile {
            display: none !important;
        }
        .action-web {
            display: unset !important;
        }

		.dataTable-td-thao-tac {
			width: 100px;
		}

    }

    @media (max-width: 575px) {
        .action-mobile {
            display: unset !important;
        }
        .action-web {
            display: none !important;
        }

		.dataTable-td-thao-tac {
			width: 80px;
		}
  
		div.dt-scroll, div.dtfh-floatingparent {
			position: static;
		}
		
		#loadingAjaxCall img {
            top: calc(30%);
            left: calc(25%);
        }

        .page-body-wrapper {
            padding-top: 0rem;
        }
        
        /* Khi là giao diện mobile thì chuyển modal nằm ở cuối màn hình, thay vì căn giữa màn hình */
		.modal .modal-dialog {
    		position: relative !important;
			margin: 0; /* Loại bỏ margin mặc định */
			width: 100%; /* Chiếm toàn bộ chiều rộng */
  			height: 100%; /* Chiếm toàn bộ chiều cao */
			max-width: 100%; /* Đảm bảo modal không vượt quá 100% chiều rộng */
			max-height: 100%; /* Đảm bảo modal không vượt quá 100% chiều cao */
			/* padding: 5px; */ /* Padding xung quanh modal */
    	}
		.modal .modal-content {
			height: 100% !important;
			border-radius: unset !important;
		}
    }

</style>

@include('common')

<script type="text/javascript">
	var arrPermission = [];

	// Event pageshow khi load mỗi lần trang. F5, back, forward đều vào đây
	window.addEventListener('pageshow', function(e) {
		if (performance.navigation.type === performance.navigation.TYPE_BACK_FORWARD
			|| e.persisted
		) { // Check chỉ tải trang khi bấm nút back/forward hoặc tải từ bộ nhớ đệm (nút back/forward)
			// Reload lại trang giống như khi nhấn F5
			window.location.reload();
		}
	});

	var isRefreshing = false;
	var requestQueue = [];

	// Token đăng nhập cửa hàng (Google/Facebook/USER) không được dùng trong admin
	if (localStorage.getItem('AUTH_SCOPE') === 'storefront') {
		localStorage.clear();
		window.location = '{{ url('/admin/login') }}';
	}

	var activeRequests = 0; // Đếm số request đang hoạt động
	/* Handle show/ hidden loading */
    $(document).ready(function() {
      	// Global ajax cursor change
     	$(document).ajaxSend(function(event, jqxhr, settings) { // Kích hoạt khi có 1 request ajax mới
	    	if (settings.showLoading == true) {
				activeRequests++;
			 	$("#loadingAjaxCall").show();
			}
			// Tự động gắn access token nếu có
			var accessToken = localStorage.getItem('ACCESS_TOKEN');
			if (!isEmpty(accessToken)) {
				jqxhr.setRequestHeader('Authorization', 'Bearer ' + accessToken);
			}
	    });

      	$(document).ajaxComplete(function(event, jqxhr, settings) { // Kích hoạt khi request ajax đã hoàn tất
			if (settings.showLoading == true) {
				activeRequests--;
				if (activeRequests == 0) {
					$("#loadingAjaxCall").hide(); // Đến khi nào biến này về 0. Tức không còn request nào hoạt động thì mới ẩn loading
				}
			}

			// Nếu lỗi 401 thì xử lý refresh token
			if (jqxhr.status === 401 && !settings._retry) {
				if (!isRefreshing) {
					isRefreshing = true;

					refreshAccessToken().then((newToken) => {
						isRefreshing = false;

						// Gửi lại các request đã chờ
						requestQueue.forEach((retryFn) => retryFn(newToken));
						requestQueue = [];
					}).catch(() => {
						isRefreshing = false;
						alert("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
						localStorage.clear();
						
						// Về trang login
						window.location = '{{ url('/admin/login') }}';
					});
				}

				// Xếp hàng chờ gửi lại request này
				requestQueue.push(function(newToken) {
					const retrySettings = $.extend(true, {}, settings, {
						_retry: true
					});

					// Cập nhật header mới
					retrySettings.beforeSend = function(xhr) {
						xhr.setRequestHeader('Authorization', 'Bearer ' + newToken);
					};

					$.ajax(retrySettings);
				});
			}

	    });

		changeAvatarUser = function(data) {
			// Set image thumnail avatar hình đại diện
			let objAvatarUpload = data['HINH_ANH_DAI_DIEN'];
			let fallbackImg = '{{ asset("image/UI-BACKEND/profile-male.png") }}';
			let $img = $('img[name="IMG_AVATAR"]');
			let srcImg = fallbackImg;
			if (!isEmpty(objAvatarUpload)) {
				// Hình ảnh đại diện - lấy aspect ratio từ API
				let aspectRatio = objAvatarUpload.ASPECT_RATIO || '1x1';
				srcImg = '{{asset('') }}' + objAvatarUpload.DIRECTORY + '/' + aspectRatio + '_' + objAvatarUpload.NAME;
			}
			$img.off('error').on('error', function () {
				$(this).attr('src', fallbackImg);
			});

			$img.attr('src', srcImg + '?time=' + new Date().getTime());
		}
		
		checkPermissionToAccessThisPage = function() {
			// Bỏ suffix /{filters?} của route danh-sách (URL path-based paging)
			var routeUri = "{{ Route::current()->uri() }}".replace(/\/\{filters\?\}$/, '');
			var isShowMasterBody = false;
			// Create object data to check
			var data = {
			};
			$.ajax({
				type : "GET",
				url : '{{ url("/api/auth/my-info") }}',
				contentType : "application/json",
				traditional: true,
				showLoading: false,
				data : data,
				success : function(data, textStatus, request) {
					data = data.DATAS.USER;
					// Set image thumnail avatar hình đại diện
					changeAvatarUser(data);

					arrPermission = data.PERMISSIONS || [];
					arrPermission.forEach(permission => {
						switch (permission.CODE) {
							case 'QL_DANH_MUC_SAN_PHAM':
								$('#MENU_DANH_MUC_SAN_PHAM').show();
								$('#MENU_PARENT_LIEN_QUAN_SAN_PHAM').show();
								if (routeUri === 'admin/danh-muc-san-pham/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_DANH_MUC_SAN_PHAM_CHI_TIET':
								$('#MENU_DANH_MUC_SAN_PHAM').show();
								$('#MENU_PARENT_LIEN_QUAN_SAN_PHAM').show();
								if (routeUri === 'admin/danh-muc-san-pham/chi-tiet/{thongTinId}' || routeUri === 'admin/danh-muc-san-pham/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_SAN_PHAM':
								$('#MENU_SAN_PHAM').show();
								$('#MENU_PARENT_LIEN_QUAN_SAN_PHAM').show();
								if (routeUri === 'admin/san-pham/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_SAN_PHAM_CHI_TIET':
								$('#MENU_SAN_PHAM').show();
								$('#MENU_PARENT_LIEN_QUAN_SAN_PHAM').show();
								if (routeUri === 'admin/san-pham/chi-tiet/{sanPhamId}' || routeUri === 'admin/san-pham/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_DANH_MUC_TIN_TUC':
								$('#MENU_DANH_MUC_TIN_TUC').show();
								$('#MENU_PARENT_LIEN_QUAN_TIN_TUC').show();
								if (routeUri === 'admin/danh-muc-tin-tuc/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_TIN_TUC':
								$('#MENU_TIN_TUC').show();
								$('#MENU_PARENT_LIEN_QUAN_TIN_TUC').show();
								if (routeUri === 'admin/tin-tuc/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_TIN_TUC_CHI_TIET':
								$('#MENU_TIN_TUC').show();
								$('#MENU_PARENT_LIEN_QUAN_TIN_TUC').show();
								if (routeUri === 'admin/tin-tuc/chi-tiet/{tinTucId}' || routeUri === 'admin/tin-tuc/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_DANH_MUC_TIN_TUC_CHI_TIET':
								$('#MENU_DANH_MUC_TIN_TUC').show();
								$('#MENU_PARENT_LIEN_QUAN_TIN_TUC').show();
								if (routeUri === 'admin/danh-muc-tin-tuc/chi-tiet/{thongTinId}' || routeUri === 'admin/danh-muc-tin-tuc/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
								
							case 'QL_VIDEO':
								$('#MENU_VIDEO').show();
								$('#MENU_PARENT_LIEN_QUAN_VIDEO').show();
								if (routeUri === 'admin/video/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_VIDEO_DANH_SACH':
								$('#MENU_VIDEO').show();
								$('#MENU_PARENT_LIEN_QUAN_VIDEO').show();
								if (routeUri === 'admin/video/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_VIDEO_CHI_TIET':
								$('#MENU_VIDEO').show();
								$('#MENU_PARENT_LIEN_QUAN_VIDEO').show();
								if (routeUri === 'admin/video/chi-tiet/{videoId}' || routeUri === 'admin/video/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_VIDEO_THEM_MOI':
								$('#MENU_VIDEO').show();
								$('#MENU_PARENT_LIEN_QUAN_VIDEO').show();
								if (routeUri === 'admin/video/chi-tiet/{videoId}' || routeUri === 'admin/video/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_VIDEO_CHINH_SUA':
								$('#MENU_VIDEO').show();
								$('#MENU_PARENT_LIEN_QUAN_VIDEO').show();
								if (routeUri === 'admin/video/chi-tiet/{videoId}' || routeUri === 'admin/video/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_VIDEO_XOA':
								$('#MENU_VIDEO').show();
								$('#MENU_PARENT_LIEN_QUAN_VIDEO').show();
								if (routeUri === 'admin/video/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_VIDEO_XEM':
								$('#MENU_VIDEO').show();
								$('#MENU_PARENT_LIEN_QUAN_VIDEO').show();
								if (routeUri === 'admin/video/danh-sach' || routeUri === 'admin/video/chi-tiet/{videoId}' || routeUri === 'admin/video/chi-tiet') {
									isShowMasterBody = true;
								}
								break;

							case 'QL_DON_HANG':
							case 'QL_DON_HANG_DANH_SACH':
							case 'QL_DON_HANG_CHI_TIET':
							case 'QL_DON_HANG_CHINH_SUA':
							case 'QL_DON_HANG_XEM':
								$('#MENU_DON_HANG').show();
								$('#MENU_PARENT_LIEN_QUAN_DON_HANG').show();
								if (routeUri === 'admin/don-hang/danh-sach' || routeUri === 'admin/don-hang/chi-tiet/{transactionId}' || routeUri === 'admin/don-hang/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
								
							case 'QL_THONG_TIN_CA_NHAN':
								$('#MENU_THONG_TIN_CA_NHAN').show();
								$('#MENU_PARENT_LIEN_QUAN_CA_NHAN').show();
								if (routeUri === 'admin/thong-tin-ca-nhan' || routeUri === 'admin/doi-mat-khau') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_NGUOI_DUNG':
								$('#MENU_NGUOI_DUNG').show();
								$('#MENU_PARENT_LIEN_QUAN_NGUOI_DUNG').show();
								if (routeUri === 'admin/nguoi-dung/danh-sach') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_NGUOI_DUNG_CHI_TIET':
								$('#MENU_NGUOI_DUNG').show();
								$('#MENU_PARENT_LIEN_QUAN_NGUOI_DUNG').show();
								if (routeUri === 'admin/nguoi-dung/chi-tiet/{userId}' || routeUri === 'admin/nguoi-dung/chi-tiet') {
									isShowMasterBody = true;
								}
								break;
							case 'QL_CAI_DAT':
								$('#MENU_PARENT_CAI_DAT').show();
								$('#MENU_CAI_DAT').show();
								if (routeUri === 'admin/cai-dat') {
									isShowMasterBody = true;
								}
								break;
							default:
								break;
						}
					});
					if (isShowMasterBody === true) {
						$('span[name="USER_FULL_NAME"]').text(data.FULL_NAME);
						$('#MASTER_BODY').show();
					} else {
						// Không có quyền
						// Clear key localStorage
						localStorage.clear();
						// Về trang login
						window.location = '{{ url('/admin/login') }}';
					}
					
				},
				error : function(request, textStatus, errorThrown) {
					if (request.status !== 401 && request.status !== 403) {
						request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
						showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
					}
				},
				complete : function() {
				}
			});
		}
		checkPermissionToAccessThisPage();

		$('[name="BTN_DANG_XUAT"]').on('click', function(e) {
			// Create object data to save parameters
			var data = {
			}

			$.ajax({
				type: "POST", 
				url: '{{ url("/api/auth/logout-user") }}', 
				contentType: "application/json",
				showLoading: true,
				data: JSON.stringify(data), 
				success: function(data, textStatus, request) {
					// Xoá cookie skip view phía client (phòng trường hợp trình duyệt không áp dụng ngay Set-Cookie xoá)
					document.cookie = 'COOKIE_SKIP_COUNT_VIEW_WEBSITE=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax';
					
					// Clear key localStorage
					localStorage.clear();
					// Về trang login
					window.location = '{{ url('/admin/login') }}';
				}, 
				error: function(request, textStatus, errorThrown) {
					if (request.status !== 401 && request.status !== 403) {
						request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
						showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
					}
				},
				complete: function(){
				}
			});
		});

		$('[name="BTN_THAY_DOI_THONG_TIN"]').on('click', function(e) {
			window.location = '{{ url('/admin/thong-tin-ca-nhan') }}';
		});

		$('[name="BTN_THAY_DOI_MAT_KHAU"]').on('click', function(e) {
			window.location = '{{ url('/admin/doi-mat-khau') }}';
		});

    });

    
	function refreshAccessToken() {
		return new Promise((resolve, reject) => {
			const refreshToken = localStorage.getItem('REFRESH_TOKEN');

			if (isEmpty(refreshToken)) {
				// Về trang login
				// window.location = '{{ url('/admin/login') }}';
			}
			
			var data = {
				REFRESH_TOKEN : refreshToken
			};
			$.ajax({
				type: "POST", 
				url: '{{ url("/api/auth/refresh-token") }}', 
				contentType: "application/json",
				data: JSON.stringify(data)
			}).done((data) => {
				localStorage.setItem('ACCESS_TOKEN', data.DATAS.access_token);
				localStorage.setItem('REFRESH_TOKEN', data.DATAS.refresh_token);
				resolve(data.access_token);
			}).fail(reject);
		});
	}


    /* 
	document.onreadystatechange = function() {
		if (document.readyState !== "complete") {
			$("#loadingAjaxCall").show();
		} else {
			$("#loadingAjaxCall").hide();
		}
    }; 
	*/


</script>

@yield('custom-css')

</head>
<body style="display: none;" id="MASTER_BODY">
	@include('UI-BACKEND.admin.common.loading')
	<div class="container-scroller">
		@include('UI-BACKEND.admin.common.header')
		
		<div class="container-fluid page-body-wrapper">
			<!-- partial:partials/_settings-panel.html -->
			<!--
			<div class="theme-setting-wrapper">
				<div id="settings-trigger">
					<i class="typcn typcn-cog-outline"></i>
				</div>
				<div id="theme-settings" class="settings-panel">
					<i class="settings-close typcn typcn-times"></i>
					<p class="settings-heading">SIDEBAR SKINS</p>
					<div class="sidebar-bg-options selected" id="sidebar-light-theme">
						<div class="img-ss rounded-circle bg-light border mr-3"></div>
						Light
					</div>
					<div class="sidebar-bg-options" id="sidebar-dark-theme">
						<div class="img-ss rounded-circle bg-dark border mr-3"></div>
						Dark
					</div>
					<p class="settings-heading mt-2">HEADER SKINS</p>
					<div class="color-tiles mx-0 px-4">
						<div class="tiles success"></div>
						<div class="tiles warning"></div>
						<div class="tiles danger"></div>
						<div class="tiles info"></div>
						<div class="tiles dark"></div>
						<div class="tiles default"></div>
					</div>
				</div>
			</div>
			-->
			<div id="right-sidebar" class="settings-panel">
				<i class="settings-close typcn typcn-times"></i>
				<ul class="nav nav-tabs" id="setting-panel" role="tablist">
					<li class="nav-item"><a class="nav-link active" id="todo-tab"
						data-toggle="tab" href="#todo-section" role="tab"
						aria-controls="todo-section" aria-expanded="true">TO DO LIST</a></li>
					<li class="nav-item"><a class="nav-link" id="chats-tab"
						data-toggle="tab" href="#chats-section" role="tab"
						aria-controls="chats-section">CHATS</a></li>
				</ul>
				<div class="tab-content" id="setting-content">
					<div class="tab-pane fade show active scroll-wrapper"
						id="todo-section" role="tabpanel" aria-labelledby="todo-section">
						<div class="add-items d-flex px-3 mb-0">
							<form class="form w-100">
								<div class="form-group d-flex">
									<input type="text" class="form-control todo-list-input"
										placeholder="Add To-do">
									<button type="submit"
										class="add btn btn-primary todo-list-add-btn" id="add-task">Add</button>
								</div>
							</form>
						</div>
						<div class="list-wrapper px-3">
							<ul class="d-flex flex-column-reverse todo-list">
								<li>
									<div class="form-check">
										<label class="form-check-label"> <input class="checkbox"
											type="checkbox"> Team review meeting at 3.00 PM
										</label>
									</div> <i class="remove typcn typcn-delete-outline"></i>
								</li>
								<li>
									<div class="form-check">
										<label class="form-check-label"> <input class="checkbox"
											type="checkbox"> Prepare for presentation
										</label>
									</div> <i class="remove typcn typcn-delete-outline"></i>
								</li>
								<li>
									<div class="form-check">
										<label class="form-check-label"> <input class="checkbox"
											type="checkbox"> Resolve all the low priority tickets due
											today
										</label>
									</div> <i class="remove typcn typcn-delete-outline"></i>
								</li>
								<li class="completed">
									<div class="form-check">
										<label class="form-check-label"> <input class="checkbox"
											type="checkbox" checked> Schedule meeting for next week
										</label>
									</div> <i class="remove typcn typcn-delete-outline"></i>
								</li>
								<li class="completed">
									<div class="form-check">
										<label class="form-check-label"> <input class="checkbox"
											type="checkbox" checked> Project review
										</label>
									</div> <i class="remove typcn typcn-delete-outline"></i>
								</li>
							</ul>
						</div>
						<div class="events py-4 border-bottom px-3">
							<div class="wrapper d-flex mb-2">
								<i class="typcn typcn-media-record-outline text-primary mr-2"></i>
								<span>Feb 11 2018</span>
							</div>
							<p class="mb-0 font-weight-thin text-gray">Creating component
								page</p>
							<p class="text-gray mb-0">build a js based app</p>
						</div>
						<div class="events pt-4 px-3">
							<div class="wrapper d-flex mb-2">
								<i class="typcn typcn-media-record-outline text-primary mr-2"></i>
								<span>Feb 7 2018</span>
							</div>
							<p class="mb-0 font-weight-thin text-gray">Meeting with Alisa</p>
							<p class="text-gray mb-0 ">Call Sarah Graves</p>
						</div>
					</div>
					<!-- To do section tab ends -->
					<div class="tab-pane fade" id="chats-section" role="tabpanel"
						aria-labelledby="chats-section">
						<div
							class="d-flex align-items-center justify-content-between border-bottom">
							<p
								class="settings-heading border-top-0 mb-3 pl-3 pt-0 border-bottom-0 pb-0">Friends</p>
							<small
								class="settings-heading border-top-0 mb-3 pt-0 border-bottom-0 pb-0 pr-3 font-weight-normal">See
								All</small>
						</div>
						<ul class="chat-list">
							<li class="list active">
								<div class="profile">
									<img src="{{ asset('image/UI-BACKEND/faces/face1.jpg') }}"
										alt="image"><span class="online"></span>
								</div>
								<div class="info">
									<p>Thomas Douglas</p>
									<p>Available</p>
								</div> <small class="text-muted my-auto">19 min</small>
							</li>
							<li class="list">
								<div class="profile">
									<img src="{{ asset('image/UI-BACKEND/faces/face2.jpg') }}"
										alt="image"><span class="offline"></span>
								</div>
								<div class="info">
									<div class="wrapper d-flex">
										<p>Catherine</p>
									</div>
									<p>Away</p>
								</div>
								<div class="badge badge-success badge-pill my-auto mx-2">4</div>
								<small class="text-muted my-auto">23 min</small>
							</li>
							<li class="list">
								<div class="profile">
									<img src="{{ asset('image/UI-BACKEND/faces/face3.jpg') }}"
										alt="image"><span class="online"></span>
								</div>
								<div class="info">
									<p>Daniel Russell</p>
									<p>Available</p>
								</div> <small class="text-muted my-auto">14 min</small>
							</li>
							<li class="list">
								<div class="profile">
									<img src="{{ asset('image/UI-BACKEND/faces/face4.jpg') }}"
										alt="image"><span class="offline"></span>
								</div>
								<div class="info">
									<p>James Richardson</p>
									<p>Away</p>
								</div> <small class="text-muted my-auto">2 min</small>
							</li>
							<li class="list">
								<div class="profile">
									<img src="{{ asset('image/UI-BACKEND/faces/face5.jpg') }}"
										alt="image"><span class="online"></span>
								</div>
								<div class="info">
									<p>Madeline Kennedy</p>
									<p>Available</p>
								</div> <small class="text-muted my-auto">5 min</small>
							</li>
							<li class="list">
								<div class="profile">
									<img src="{{ asset('image/UI-BACKEND/faces/face6.jpg') }}"
										alt="image"><span class="online"></span>
								</div>
								<div class="info">
									<p>Sarah Graves</p>
									<p>Available</p>
								</div> <small class="text-muted my-auto">47 min</small>
							</li>
						</ul>
					</div>
					<!-- chat tab ends -->
				</div>
			</div>
			<!-- partial -->
			<!-- partial:partials/_sidebar.html -->

			@include('UI-BACKEND.admin.common.menu')

			<!-- partial -->
			<div class="main-panel">
				<div class="content-wrapper">
					@yield('content')
				</div>
				<!-- content-wrapper ends -->
				<!-- partial:../../partials/_footer.html -->
				@include('UI-BACKEND.admin.common.footer')
				<!-- partial -->
			</div>
			
			<!-- main-panel ends -->
		</div>
		<!-- page-body-wrapper ends -->
	</div>
	<!-- container-scroller -->

	<!-- inject:js -->
	<script src="{{ asset('js/UI-BACKEND/off-canvas.js') }}?v={{ $timeUpdate }}"></script>
	<script src="{{ asset('js/UI-BACKEND/hoverable-collapse.js') }}?v={{ $timeUpdate }}"></script>
	<script src="{{ asset('js/UI-BACKEND/template.js') }}?v={{ $timeUpdate }}"></script>
	<script src="{{ asset('js/UI-BACKEND/settings.js') }}?v={{ $timeUpdate }}"></script>
	<script src="{{ asset('js/UI-BACKEND/todolist.js') }}?v={{ $timeUpdate }}"></script>
	<script src="{{ asset('js/UI-BACKEND/admin-list-url.js') }}?v={{ $timeUpdate }}"></script>

	@yield('plugin-js-for-this-page')
	<!-- End plugin js for this page-->

	@yield('custom-js-for-this-page')
	<!-- End custom js for this page-->
	 
</body>

</html>

