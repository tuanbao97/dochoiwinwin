<!DOCTYPE html>
<html lang="en">

<head>
<!-- Required meta tags -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<!-- /Added by HTTrack -->
<meta name="viewport"
	content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Đồ Chơi Win Win - Đặt lại mật khẩu</title>
<!-- base:css -->
<link rel="stylesheet"
	href="{{ versionedAsset('css/UI-BACKEND/vendors/typicons/typicons.css') }}">
<link rel="stylesheet"
	href="{{ versionedAsset('css/UI-BACKEND/vendors/css/vendor.bundle.base.css') }}">
<!-- endinject -->
<!-- plugin css for this page -->
<!-- End plugin css for this page -->
<!-- inject:css -->
<link rel="stylesheet"
	href="{{ versionedAsset('css/UI-BACKEND/vertical-layout-light/style.css') }}">
<!-- endinject -->
<link rel="shortcut icon"
	href="{{ storefrontFaviconUrl() }}" />

<link rel="stylesheet" href="{{ versionedAsset('css/UI-BACKEND/vendors/jquery-toast-plugin/jquery.toast.min.css') }}">
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

							<h6 class="font-weight-light">Cập nhật mật khẩu:</h6>
							<form class="pt-3" id="FORM_LOGIN">

								<div class="form-group">
									<input type="email" required class="form-control form-control-lg" id="EMAIL" placeholder="Email" autocomplete="new-email" readonly>
								</div>

								<div class="form-group">
									<input type="password" required class="form-control form-control-lg" id="NEW_PASSWORD" placeholder="Mật khẩu" autocomplete="new-password">
									<span class="error-message" id="MSG_NEW_PASSWORD"></span>
								</div>

								<div class="form-group">
									<input type="password" required class="form-control form-control-lg" id="CONFIRM_NEW_PASSWORD" placeholder="Nhập lại mật khẩu" autocomplete="new-password">
									<span class="error-message" id="MSG_CONFIRM_NEW_PASSWORD"></span>
								</div>

								<div class="my-3">
									<button class="btn btn-block btn-info btn-lg font-weight-medium auth-form-btn w-100" type="submit">THAY ĐỔI</button>
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
		// Tìm page mặc định
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		const resetKeyFrUrl = urlParams.get('reset_key');

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
			getUserDetailFrResetKey = function(resetKey) {
				$.ajax({
					type: "GET",
					url: '{{ url("/api/auth/reset-password") }}',
					contentType: "application/json",
					cache: false, // Đảm bảo không cache dữ liệu
					traditional: false, // Bắt buộc đặt là false
					showLoading: true,
					data: (function() { // IIFE 
						let dataInput = {};

						// Query params
						dataInput.RESET_KEY = resetKey;

						return dataInput; // Trả về object input data
					})(),
					success: function(data, textStatus, request) {
						var result = data.DATAS.USER;
						if (isEmpty(result)) {
							showToastFailure('top-right', 'Yêu cầu reset mật khẩu không hợp lệ hoặc đã hết hạn!');
							return;
						}
						$('#EMAIL').val(result.EMAIL);

					},
					error: function(request, textStatus, errorThrown) {
						if (request.status !== 401 && request.status !== 403) {
							request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
							showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
						}
					},
					complete: function() {
					}
				});
			}
			getUserDetailFrResetKey(resetKeyFrUrl);



			$('#FORM_LOGIN').submit(function(e) {
				e.preventDefault(); // Vô hiệu hóa submit form html

				// Reset all error msg
				resetAllErrorMsg();

				var data = {
					RESET_KEY :  resetKeyFrUrl || null
					, NEW_PASSWORD: !isEmpty($('#NEW_PASSWORD').val()) ? $('#NEW_PASSWORD').val() : null
					, CONFIRM_NEW_PASSWORD: !isEmpty($('#CONFIRM_NEW_PASSWORD').val()) ? $('#CONFIRM_NEW_PASSWORD').val() : null
				};
				
				$.ajax({
					type: "POST", 
					url: '{{ url("/api/auth/reset-password") }}', 
					contentType: "application/json",
					showLoading: true,
					data: JSON.stringify(data), 
					success: function(data, textStatus, request) {
						// Ajax call completed successfully
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

			$('#BTN_QUYEN_MAT_KHAU').on('click', function(e) {
				window.location = '{{ url('/admin/forgot-password') }}';

				/* showSwalInfoPopup(function callback(result) {
					if (result.isConfirmed === true) {
					} else if (result.isDismissed === true) {
						
					} else if (result.isDenied === true) {

					}
				}, 'Vui lòng liên hệ Admin để đổi mật khẩu.'); */
			});
		});
	</script>
</body>

</html>

