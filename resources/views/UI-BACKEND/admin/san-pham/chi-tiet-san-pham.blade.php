<?php
	// Random 1 lần uuid để làm id duy nhất
	$uuid1 = 'section' . Str::random(6);
	$uuid2 = 'section' . Str::random(6);
	$uuid3 = 'section' . Str::random(6);
	$uuid4 = 'section' . Str::random(6);
	$uuid5 = 'section' . Str::random(6);
	$categoryRoots = \App\Models\CategoryP::query()
		->whereNull('PARENT_ID')
		->where('STATUS', \App\Enum\AppConstant::STATUS_USING)
		->where('IS_ACTIVE', true)
		->with(['childrens' => function ($query) {
			$query->where('STATUS', \App\Enum\AppConstant::STATUS_USING)
				->where('IS_ACTIVE', true)
				->orderBy('SORT_ORDER')
				->orderBy('NAME');
		}])
		->orderBy('SORT_ORDER')
		->orderBy('NAME')
		->get();
?>

@extends('UI-BACKEND.admin.common.layout.master')

@section('plugin-css-for-this-page') @stop @section('custom-css')
<style>
    
	@media (min-width: 1500px) {
		#CHI_TIET_SAN_PHAM .section.col-lg-4-custom {
			flex: 0 0 auto;
			width: 350px;
		}
		#CHI_TIET_SAN_PHAM .section.col-lg-8-custom {
			flex: 0 0 auto;
			width: calc(100% - 350px);
		}
	}
	@media (min-width: 1250px) and (max-width: 1500px) {
		#CHI_TIET_SAN_PHAM .section.col-lg-4-custom {
			flex: 0 0 auto;
			width: 300px;
		}
		#CHI_TIET_SAN_PHAM .section.col-lg-8-custom {
			flex: 0 0 auto;
			width: calc(100% - 300px);
		}
	}
	@media (max-width: 1600px) {
		.{{ 'section_' . $uuid2 }}.box-upload-one-img {
			margin: 0 auto;
		}
	}
 
	.block-chi-tiet-san-pham .card-description {
    	font-size: 1.035rem;
    }
    
    .block-chi-tiet-san-pham {
    	margin-top: 15px;
    }
    
    .list-group-flush {
        margin-left: 0px;
        margin-right: 0px;
    }

    .list-group .list-group-item {
        /* border: none; */
        padding-left: 0px;
        padding-right: 0px;
    }
    
    .list-group-flush > .list-group-item:last-child {
        border-bottom-width: 1px;
    }
    
    .div-chi-tiet-san-pham .div-vui-long-chon img{
        margin-top: 50px !important;
        border-radius: 20px;
        width: 150px;
        margin: auto;
        display: block;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
    }
    .div-chi-tiet-san-pham .div-vui-long-chon label {
        text-align: center;
        display: block;
        margin-top: 25px;
        font-size: 18px;
        font-weight: 600;
    }

    .div-chi-tiet-san-pham .div-vui-long-chon-dmsp code {
        display: block;
        text-align: center;
    }
	.div-chi-tiet-san-pham .div-vui-long-chon-dmsp img{
        margin-top: 50px !important;
        border-radius: 20px;
        width: 250px;
        margin: auto;
        display: block;
    }
    .div-chi-tiet-san-pham .div-vui-long-chon-dmsp label {
        text-align: center;
        display: block;
        margin-top: 25px;
        font-size: 18px;
        font-weight: 600;
    }
    .div-chi-tiet-san-pham .div-vui-long-chon code {
        display: block;
        text-align: center;
    }
	
	@media (max-width: 992px) {

	}
</style>

@stop @section('nav-item')
<li class="nav-item">
	<div class="d-flex align-items-baseline">
		<p class="mb-0">Admin</p>
		<i class="typcn typcn-chevron-right"></i>
		<p class="mb-0">Quản lý sản phẩm</p>
	</div>
</li>
@stop @section('content')
<div class="row section-main">

	<div class="col-lg-12 grid-margin stretch-card div-chi-tiet-san-pham" id="CHI_TIET_SAN_PHAM">
		<div class="card">
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-12 col-sm-12">
						<h4 class="card-title">
							CHI TIẾT <span class="one-line">SẢN PHẨM</span>
						</h4>
					</div>
				</div>

				<div>
					<div class="row">
						<div class="form-group d-none">
							<label for="ID">Id sản phẩm</label> <input type="text"
								class="form-control" id="EDIT_ID" placeholder=""> <span
								class="error-message" id="MSG_EDIT_ID"></span>
						</div>

						<div class="section col-lg-4-custom col-md-12">
							<div class="row">
								<div class="col-12 form-group form-box-upload-anh">
									
									<!-- START Include box upload 1 file vào -->
									@include('UI-BACKEND.admin.common.component.upload-file.box-upload-1-file'
										, [
											'sectionId' => 'section_' . $uuid2
											, 'aspectRatio' => '1x1'
											, 'ratio' => '1/1'
											, 'maxWidth' => '350px'
										]
									)
									<!-- END Include box upload 1 file vào -->

									<span class="error-message text-center"
										id="MSG_ANH_DAI_DIEN"></span>
								</div>
							
								<div class="col-12">
									<div class="form-group text-center">
										<button id="BTN_DANH_SACH_HINH_ANH" type="button" class="btn btn-outline-info btn-fw btn-icon-text me-2">
											<i class="icon-picture btn-icon-prepend"></i>Hình ảnh (<span id="SO_LUONG_DANH_SACH_HINH_ANH">0</span>)
										</button>

										<button id="BTN_DANH_SACH_HINH_VIDEO" type="button" class="btn btn-outline-info btn-fw btn-icon-text">
											<i class="icon-film btn-icon-prepend"></i>Video (<span id="SO_LUONG_DANH_SACH_VIDEO">0</span>)
										</button>
									</div>
								</div>
							
							</div>
						</div>
						<div class="section col-lg-8-custom col-md-12">
							<div class="col-lg-12 col-md-12">
								<div class="form-group">
									<label for="NAME">Danh mục sản phẩm<code>*</code></label>

									<select id="EDIT_DANH_MUC_SAN_PHAM_IDS" class="form-control w-100" multiple="multiple">
										@foreach ($categoryRoots as $categoryRoot)
											<option value="{{ $categoryRoot->ID }}">{{ $categoryRoot->NAME }}</option>
											@foreach ($categoryRoot->childrens as $categoryChild)
												<option value="{{ $categoryChild->ID }}">↳ {{ $categoryChild->NAME }}</option>
											@endforeach
										@endforeach
									</select>
									<small class="form-text text-muted">Có thể chọn nhiều danh mục.</small>

									<span class="error-message" id="MSG_EDIT_DANH_MUC_SAN_PHAM"></span>
								</div>
							</div>

							<div class="col-12 div-vui-long-chon-dmsp" id="SECTION_VUI_LONG_CHON_DMSP" style="display: none;">
								<img src="{{ asset('image/UI-BACKEND/vector-ecommerce.jpg') }}">
								
								<label>Chọn danh mục sản phẩm</label>		
								<code>để tiếp tục</code>			
							</div>

							<!-- START section include page -->
    							<div id="DIV_CHI_TIET_SAN_PHAM">

								</div>
							<!-- END section include page -->

						</div>


					</div>
				</div>

			</div>
		</div>
	</div>

</div>

<!-- Include popups -->
@include('UI-BACKEND.admin.common.component.popup.upload-file.popup-upload-multiple-hinh-anh',
	[
		'sectionId' => 'section_' . $uuid4
		, 'aspectRatio' => '1x1'
		, 'ratio' => '1/1'
	]
)

@include('UI-BACKEND.admin.common.component.popup.upload-file.popup-upload-multiple-video',
	[
		'sectionId' => 'section_' . $uuid5
		, 'aspectRatio' => '1x1'
		, 'ratio' => '1/1'
	]
)
@stop

@section('plugin-js-for-this-page')
@stop

@section('custom-js-for-this-page')
<script>
$(document).ready(function () {
	var isFirstDanhMucSanPhamSelected = false;

	$('#EDIT_DANH_MUC_SAN_PHAM_IDS').select2({
		placeholder: 'Chọn một hoặc nhiều danh mục',
		width: '100%',
		closeOnSelect: false
	});
	
	@if (isset($productId))
		isFirstDanhMucSanPhamSelected = true;
	@endif
	
	/* START Handle upload box 1 ảnh  */
	/* Set dafault text bên trong box upload 1 ảnh */
	{{"section_" . $uuid2 }}_setDefaultTextBoxUplOneImg('Upload ảnh bìa');

	/* Xử lý get danh sách upload hình ảnh đại diện */
	getDanhSachUploadHinhAnhDaiDien = function() {
		return {{ "section_" . $uuid2 }}_getDanhSachUploadHinhAnhDaiDien();
	}
	/* END Handle upload box 1 ảnh */

	/* START Handle upload danh sách hình ảnh */
	/* Xử lý get danh sách upload multiple hình ảnh */
	getDanhSachUploadMultipleHinhAnh = function() {
		return {{ "section_" . $uuid4 }}_getDanhSachUploadMultipleHinhAnh();
	}

	/* Xử lý event click button danh sách hình ảnh */
	$('#BTN_DANH_SACH_HINH_ANH').on('click', function() {
		// Xử lý open popup
		{{"section_" . $uuid4 }}_handleOpenPopupUploadMultipleHinhAnh();
		
		// Hiển thị modal popup
		$('#{{"section_" . $uuid4 }}_MODAL_UPLOAD_MULTIPLE_HINH_ANH').modal('show');
	});

	// Call back
	{{"section_" . $uuid4 }}_callBackUploadMultipleHinhAnh = function(data) {
		$("#SO_LUONG_DANH_SACH_HINH_ANH").text(data.length);
	}
	
	/* END Handle upload danh sách hình ảnh */

	/* Xử lý event click button danh sách video */
	/* Xử lý get danh sách upload multiple video */
	getDanhSachUploadMultipleVideo = function() {
		return {{ "section_" . $uuid5 }}_getDanhSachUploadMultipleVideo();
	}
	$('#BTN_DANH_SACH_HINH_VIDEO').on('click', function() {
		// Xử lý open popup
		{{"section_" . $uuid5 }}_handleOpenPopupUploadMultipleVideo();
		
		// Hiển thị modal popup
		$('#{{"section_" . $uuid5 }}_MODAL_UPLOAD_MULTIPLE_VIDEO').modal('show');
	});

	// Call back
	{{"section_" . $uuid5 }}_callBackUploadMultipleVideo = function(data) {
		$("#SO_LUONG_DANH_SACH_VIDEO").text(data.length);
	}
	
	/* END Handle upload danh sách video */
	
	/* Load view form theo loại sản phẩm */
	loadViewLoaiSanPham = function(duLieu, productId, pathView) {
		let uuid = generateRandomString(6);
		var inputData = {
			pathView : pathView
			, productId: productId
			, duLieu: duLieu
			, uuid: uuid
		};

		// Cấu hình CSRF-TOKEN để vượt qua
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type : "POST",
			url : '{{ url("/product/view") }}',
			contentType : "application/json",
			traditional: true,
			showLoading: true,
			data : JSON.stringify(inputData),
			success : function(data, textStatus, request) {
				$('#SECTION_VUI_LONG_CHON_DMSP').hide();
				$('#DIV_CHI_TIET_SAN_PHAM').html(data);
			},
			error : function(request, textStatus, errorThrown) {
				// Block of code to handle errors
				showToastFailure('top-right', 'Internal server');
				$('#SECTION_VUI_LONG_CHON_DMSP').show();
				$('#DIV_CHI_TIET_SAN_PHAM').html(null);
			},
			complete : function() {
			}
		});
	}

	@if (isset($productId)) {
		$('#SECTION_VUI_LONG_CHON_DMSP').hide();
		/* Xử lý get chi tiết product */
		var productId = '{{ isset($productId) ? $productId : null }}';

		// Create object data to check
		var inputData = {
		};

		$.ajax({
			type : "GET",
			url: '{{ url("/api/product/detail") }}' + "/" + productId, 
			contentType : "application/json",
			traditional: true,
			showLoading: true,
			data : inputData,
			success : function(data, textStatus, request) {
				data = data.DATAS.PRODUCT;

				// Set id sản phẩm
				$('#EDIT_ID').val(data.ID);
				
				// Set image thumnail avatar hình đại diện
				let objAvatarUpload = data['DANH_SACH_HINH_ANH_DAI_DIEN'];
				if (!isEmpty(objAvatarUpload)) {

					// Hình ảnh đại diện
					let $objAvatarThumnail = $(objAvatarUpload).filter(function(idx) {
						return objAvatarUpload[idx].IS_THUMNAIL === true; // Filter là ảnh thumnail
					});
					if ($objAvatarThumnail.length > 0) {
						{{ "section_" . $uuid2 }}_setThumbnailBoxOneImg(
								$("#{{'section_' . $uuid2 }}_divDropZone")[0]
            					, !isEmpty($objAvatarThumnail[0].NAME) ? $objAvatarThumnail[0].NAME : ''
            					, !isEmpty($objAvatarThumnail[0].DIRECTORY) ? $objAvatarThumnail[0].DIRECTORY : ''
            			);

						//Chi tiết hình ảnh
						{{ "section_" . $uuid2 }}_chiTietHinhAnh($objAvatarThumnail[0]);

						// Append input danh sách hình ảnh đại diện
						{{ "section_" . $uuid2 }}_appendInputUploadHinhAnhDaiDien($objAvatarThumnail[0]);
					}
				}
				
				// Set danh sách hình ảnh
				let danhSachHinhAnh = data['DANH_SACH_HINH_ANH'];
				if (!isEmpty(danhSachHinhAnh)) {

					// Remove all append input upload multiple hình ảnh
					{{ "section_" . $uuid4 }}_removeAllAppendInputUploadMultipleHinhAnh();
					// Xử lý append input danh sách hình ảnh
					{{ "section_" . $uuid4 }}_appendInputUploadMultipleHinhAnh(danhSachHinhAnh);
					$("#SO_LUONG_DANH_SACH_HINH_ANH").text(danhSachHinhAnh.length);
				}

				// Set danh sách video
				let danhSachVideo = data['DANH_SACH_VIDEO'];
				if (!isEmpty(danhSachVideo)) {

					// Remove all append input upload multiple video
					{{ "section_" . $uuid5 }}_removeAllAppendInputUploadMultipleVideo();
					// Xử lý append input danh sách video
					{{ "section_" . $uuid5 }}_appendInputUploadMultipleVideo(danhSachVideo);
					$("#SO_LUONG_DANH_SACH_VIDEO").text(danhSachVideo.length);
				}

				// Danh mục sản phẩm
				let danhMucSanPhams = data['DANH_MUC_SAN_PHAMS'] || [];
				if (danhMucSanPhams.length === 0 && !isEmpty(data['DANH_MUC_SAN_PHAM'])) {
					danhMucSanPhams = [data['DANH_MUC_SAN_PHAM']];
				}
				let categoryIds = danhMucSanPhams
					.map(item => item && item.ID ? String(item.ID) : null)
					.filter(Boolean);
				$('#EDIT_DANH_MUC_SAN_PHAM_IDS').val(categoryIds).trigger('change.select2');
				
				// Load view loại sản phẩm
				let pathView = data['PATH_VIEW'];
				pathView = 'UI-BACKEND/admin/san-pham/common/san-pham';
				loadViewLoaiSanPham(data, productId, pathView, function() {
					// Callback khi view load xong
					console.log('View đã load xong, gọi function loadPriceData');
					let functionName = 'section_' + '{{ $uuid1 }}' + '_loadPriceData';
					if (typeof window[functionName] === 'function') {
						console.log('Gọi function loadPriceData');
						window[functionName](data);
					} else {
						console.log('Function không tồn tại:', functionName);
					}
				});
				
				// Sau khi load view xong, load dữ liệu giá cả
				// Gọi trực tiếp function nếu có sẵn
				let functionName = 'section_' + '{{ $uuid1 }}' + '_loadPriceData';
				if (typeof window[functionName] === 'function') {
					console.log('Function loadPriceData đã sẵn sàng, đang gọi...');
					window[functionName](data);
				} else {
					console.log('Function loadPriceData chưa sẵn sàng, bỏ qua');
				}
			},
			error : function(request, textStatus, errorThrown) {
				request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');

				// Show section error 404
				// redirectErrorPage(404);

				setTimeout(() => {
					uriDanhSachSanPham = '{{ url("/admin/san-pham") }}';
					window.location.href = uriDanhSachSanPham;
				}, 1500);
			},
			complete : function() {
			}
		});
	}
	@else
		/* Xử lý màn hình thêm mới sản phẩm */
		$('#SECTION_VUI_LONG_CHON_DMSP').show();
	@endif
	
	$('#EDIT_DANH_MUC_SAN_PHAM_IDS').on('change', function() {
		const selectedIds = $(this).val() || [];
		// Chỉ gọi API reset bên dưới đúng 1 lần đầu khi thêm mới
		if (selectedIds.length > 0 && !isFirstDanhMucSanPhamSelected) {
			@if (!isset($productId))
				let productId = null;
				let pathView = 'UI-BACKEND/admin/san-pham/common/san-pham';
				loadViewLoaiSanPham(null, productId, pathView);
			@endif
			isFirstDanhMucSanPhamSelected = true;
		}
	});
	
	/* Reset all messages sản phẩm */
	resetAllMsgSanPham = function() {
		$('#CHI_TIET_SAN_PHAM').find($('span[class*="error-message"]')).not('[type="radio"], [type="checkbox"]').each(function(i, obj) {
			$(this).text('');
		});
	}

	resetAllHinhAnhVaVideo = function() {
		// Remove all append input upload multiple hình ảnh
		{{ "section_" . $uuid4 }}_removeAllAppendInputUploadMultipleHinhAnh();
		$("#SO_LUONG_DANH_SACH_HINH_ANH").text('0');

		// Remove all append input upload multiple video
		{{ "section_" . $uuid5 }}_removeAllAppendInputUploadMultipleVideo();
		$("#SO_LUONG_DANH_SACH_VIDEO").text('0');

	}

	deleteContentBoxOneImgUpload = function() {
		{{"section_" . $uuid2 }}_deleteContentBoxOneImgUpload();
	}

	resetDanhMucSanPham = function() {
		$('#EDIT_DANH_MUC_SAN_PHAM_IDS').val(null).trigger('change');
		$('#DIV_CHI_TIET_SAN_PHAM').html(null);
		$('#SECTION_VUI_LONG_CHON_DMSP').show();
	}

});
</script>
@stop
