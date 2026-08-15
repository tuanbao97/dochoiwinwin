<!DOCTYPE html>
<html lang="en">

<head>
<!-- Required meta tags -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<!-- /Added by HTTrack -->
<meta name="viewport"
	content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Win Win Trái Cây Nhập Khẩu - Đăng ký tài khoản</title>
<!-- base:css -->
<link rel="stylesheet"
	href="{{ asset('css/UI-BACKEND/vendors/typicons/typicons.css') }}">
<link rel="stylesheet"
	href="{{ asset('css/UI-BACKEND/vendors/css/vendor.bundle.base.css') }}">
<!-- endinject -->
<!-- plugin css for this page -->
<!-- End plugin css for this page -->
<!-- inject:css -->
<link rel="stylesheet"
	href="{{ asset('css/UI-BACKEND/vertical-layout-light/style.css') }}">
<!-- endinject -->
<link rel="shortcut icon"
	href="{{ storefrontFaviconUrl() }}" />

<link rel="stylesheet" href="{{ asset('css/UI-BACKEND/vendors/jquery-toast-plugin/jquery.toast.min.css') }}">
</head>

@include('common')

<style>
	.content-wrapper {
		padding: unset;
	}

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
        left: calc(50% - 100px);
        position: absolute;
        z-index: 999999;
    }

	@media (max-width: 575px) {
		#loadingAjaxCall img {
            top: calc(30%);
            left: calc(25%);
        }
	}
</style>

<body>
	@include('UI-BACKEND.admin.common.loading')
	<div class="container-scroller">
		<div class="container-fluid page-body-wrapper full-page-wrapper">
			<div class="content-wrapper d-flex align-items-center auth ">
				<div class="row w-100 mx-0">
					<div class="col-lg-4 mx-auto" style="max-width: 500px;">
						<div class="auth-form-light text-start py-5 px-4 px-sm-5">

							<a href="{{ url('/') }}">
							<div class="brand-logo" style="text-align: center;">
								<img src="{{ storefrontLogoUrl() }}" alt="Đồ Chơi Win Win" style="max-width: 180px; width: 100%; height: auto;">
							</div>
							</a>

							<h6 class="font-weight-light">Đăng ký tài khoản:</h6>
							<form class="pt-3" id="FORM_REGISTER">

								<div class="form-group">
									<input type="text" required class="form-control form-control-lg" id="FULL_NAME" placeholder="Họ và tên">
									<span class="error-message" id="MSG_FULL_NAME"></span>
								</div>

								<div class="form-group">
									<input type="text" required class="form-control form-control-lg" id="SO_DIEN_THOAI" placeholder="Số điện thoại liên hệ">
									<span class="error-message" id="MSG_SO_DIEN_THOAI"></span>
								</div>
								
								<div class="form-group">
									<input type="email" required class="form-control form-control-lg" id="EMAIL" placeholder="Email" autocomplete="new-email">
									<span class="error-message" id="MSG_EMAIL"></span>
								</div>

								<div class="form-group">
									<input type="password" required class="form-control form-control-lg" id="PASSWORD" placeholder="Mật khẩu" autocomplete="new-password">
									<span class="error-message" id="MSG_PASSWORD"></span>
								</div>

								<div class="form-group">
									<input type="password" required class="form-control form-control-lg" id="CONFIRM_PASSWORD" placeholder="Nhập lại mật khẩu" autocomplete="new-password">
									<span class="error-message" id="MSG_CONFIRM_PASSWORD"></span>
								</div>

								<!-- <div class="my-2 d-flex justify-content-between align-items-center">
									<div class="form-check">
										<label class="form-check-label text-muted"> 
											<input type="checkbox" class="form-check-input" id="CHECKBOX_REMEMBER_ME" checked> 
											Duy trì đăng nhập <i class="input-helper"></i><i class="input-helper"></i>
										</label>
									</div>
									<a href="#" class="auth-link text-black">Quên mật khẩu?</a>
								</div> -->

								<div class="my-3">
									<button class="btn btn-block btn-info btn-lg font-weight-medium auth-form-btn w-100" type="submit">ĐĂNG KÝ</button>
								</div>

								<!-- <div class="mb-2 d-flex">
									<button type="button" class="btn btn-facebook auth-form-btn flex-grow mr-1">
										<i class="typcn typcn-social-facebook me-2"></i>Facebook
									</button>
									<button type="button" class="btn btn-google auth-form-btn flex-grow ms-1">
										<i class="typcn typcn-social-google-plus-circular me-2"></i>Google
									</button>
								</div> -->
					
								<div class="text-center mt-4 font-weight-light">
									<a href="{{ url('/admin/login') }}" class="text-primary">Quay lại trang Đăng nhập</a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- content-wrapper ends -->
		</div>
		<!-- page-body-wrapper ends -->
	</div>
	<!-- container-scroller -->

	<script>
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


			});
		});

		$(document).ready(function () {
			$('#FORM_REGISTER').submit(function(e) {
				e.preventDefault(); // Vô hiệu hóa submit form html
				
				// Reset all error msg
				resetAllErrorMsg();
				
				var data = {
					EMAIL: !isEmpty($('#EMAIL').val()) ? $('#EMAIL').val() : null
					, SO_DIEN_THOAI: !isEmpty($('#SO_DIEN_THOAI').val()) ? sanitizePhoneNumberFromString($('#SO_DIEN_THOAI').val()) : null
					, FULL_NAME: !isEmpty($('#FULL_NAME').val()) ? $('#FULL_NAME').val() : null
					, PASSWORD: !isEmpty($('#PASSWORD').val()) ? $('#PASSWORD').val() : null
					, CONFIRM_PASSWORD: !isEmpty($('#CONFIRM_PASSWORD').val()) ? $('#CONFIRM_PASSWORD').val() : null
				};
				
				$.ajax({
					type: "POST", 
					url: '{{ url("/api/auth/register-user") }}', 
					contentType: "application/json",
					showLoading: true,
					data: JSON.stringify(data), 
					success: function(data, textStatus, request) {
						showToastSuccess('top-right', data.STATUS_DETAIL);

						setTimeout(() => {
							// Về trang danh sách sản phẩm
							window.location = '{{ url('/admin/login') }}';
						}, 4000);
						
					},
					error: function(request, textStatus, errorThrown) {
						if (request.status === 401 || request.status === 403) {
							return;
						}
						try {
							request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
							showToastFailure('top-right', request.responseJSON && !isEmpty(request.responseJSON.STATUS_DETAIL) ? request.responseJSON.STATUS_DETAIL : 'Internal server');

							// Set error msg
							var errors = request.responseJSON != null ? request.responseJSON.ERRORS : null;
							// Looping các key của error messages
							for (let key in errors) {
								if(errors.hasOwnProperty(key)){
									// Lopping danh sách lỗi
									let keyVals = errors[key];
									let errorMsg = '';
									for(var i in keyVals) {
										let keyVal = keyVals[i];
										errorMsg += keyVal;
										if (i < keyVals.length - 1) errorMsg += ' ';
									}

									// Set error message
									$('#MSG_' + key.replaceAll('.', '\\.')).text(errorMsg);
									switch (key) {
										case 'DANH_SACH_HINH_ANH_DAI_DIEN':
											$('#MSG_ANH_DAI_DIEN').text(errorMsg);
											break;
										case 'DANH_MUC_SAN_PHAM.ID':
											$('#MSG_EDIT_DANH_MUC_SAN_PHAM').text(errorMsg);
											break;
										default:
											break;
									}
								}
							}
						}
						catch(err) {
							// Block of code to handle errors
							showToastFailure('top-right', 'Internal server');
						}
						finally {
							// Khối mã sẽ được thực thi bất kể kết quả thành công hay lỗi
							// Clear key localStorage
							localStorage.clear();
						}
						
					},
					complete: function() {
					}
				});

			});
		});
	</script>
</body>

</html>

