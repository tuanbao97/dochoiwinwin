<?php
// Random uuid1
$uuid1 = 'section_' . Str::random(6);
$uuid2 = 'section_' . Str::random(6);
?>

<style>
.block-chi-tiet-san-pham {
    margin-bottom: 20px;
}

.block-chi-tiet-san-pham .card-description {
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 8px;
}

.section-block {
    margin-bottom: 15px;
}

.input-group-text select {
    border: none;
    background: transparent;
    padding: 0 8px;
    font-size: 14px;
    color: #495057;
    min-width: 80px;
}

.input-group-text select:focus {
    outline: none;
    box-shadow: none;
}

/* Đảm bảo select trong input-group có styling đẹp */
.input-group .form-select {
    border: none;
    background: transparent;
    box-shadow: none;
}

.input-group .form-select:focus {
    border: none;
    box-shadow: none;
  }
</style>

<div id="{{ $uuid1 }}_SECTION_CHI_TIET_SAN_PHAM">
  <div class="row block-chi-tiet-san-pham">
    <p class="card-description">Thông tin sản phẩm</p>

    <div class="section-block col-lg-12 col-md-12">
      <div class="form-group">
        <label for="{{ $uuid1 }}_EDIT_TEN_SAN_PHAM">
          Tên sản phẩm<code>*</code>
        </label>
        <input type="text" class="form-control" id="{{ $uuid1 }}_EDIT_TEN_SAN_PHAM" placeholder="">
        <span class="error-message"></span>
      </div>
    </div>

    <div class="section-block col-lg-6 col-md-6">
      <div class="form-group">
        <label for="{{ $uuid1 }}_EDIT_MA_SAN_PHAM">Mã sản phẩm</label>
        <input type="text" class="form-control" id="{{ $uuid1 }}_EDIT_MA_SAN_PHAM" placeholder="Để trống sẽ dùng ID sản phẩm" maxlength="100">
        <div class="mt-2">
          <span class="text-muted">Nếu để trống, hệ thống mặc định lấy ID sản phẩm.</span>
        </div>
        <span class="error-message"></span>
      </div>
    </div>

  <p class="card-description">Giá cả</p>
  <div class="section-block col-lg-12 col-md-12">
    <div class="form-group">
      <label for="{{ $uuid1 }}_EDIT_GIA_LIEN_HE" style="color: blue;">Giá liên hệ</label>
      <div>
        <label class="switch">
        <input type="checkbox" class="primary" id="{{ $uuid1 }}_EDIT_GIA_LIEN_HE">
          <span class="slider"></span>
        </label>
        <span class="error-message"></span>
      </div>
    </div>
  </div>

  <div class="section-block col-lg-6 col-md-6">
    <div class="form-group">
      <label for="{{ $uuid1 }}_EDIT_GIA_CA">Giá cả<code>*</code></label>
      <div class="input-group">
    <input type="text" class="form-control" id="{{ $uuid1 }}_EDIT_GIA_CA" placeholder="" />
        <span class="input-group-text">VNĐ</span>
  </div>
<div class="mt-2">
  <span class="text-muted">
    <span id="{{ $uuid1 }}_PRICE_TEXT_LABEL" style="display: none;"></span>
  </span>
      </div>
      <span class="error-message" id="MSG_{{ $uuid1 }}_EDIT_GIA_CA"></span>
    </div>
  </div>

  <div class="section-block col-lg-6 col-md-6">
    <div class="form-group">
      <label for="{{ $uuid1 }}_EDIT_GIA_GOC">Giá gốc</label>
      <div class="input-group">
        <input type="text" class="form-control" id="{{ $uuid1 }}_EDIT_GIA_GOC" placeholder="" />
        <span class="input-group-text">VNĐ</span>
      </div>
      <div class="mt-2">
        <span class="text-muted">
          <span>Giá trước giảm (hiển thị gạch ngang). Có thể để trống</span>
        </span>
      </div>
      <span class="error-message" id="MSG_{{ $uuid1 }}_EDIT_GIA_GOC"></span>
    </div>
  </div>

  <div class="section-block col-lg-6 col-md-6">
    <div class="form-group">
      <label for="{{ $uuid1 }}_EDIT_GIA_HIEN_THI">Giá hiển thị trên website</label>
      <input type="text" class="form-control" id="{{ $uuid1 }}_EDIT_GIA_HIEN_THI" placeholder="" />
      <div class="mt-2">
        <span class="text-muted">
          <span>(VD: 1 tỷ 8xx) Có thể để trống</span>
  </span>
      </div>
      <span class="error-message"></span>
    </div>
  </div>

  <p class="card-description">SEO & Mô tả</p>
  <div class="section-block col-lg-12 col-md-12">
    <div class="form-group">
      <label for="{{ $uuid1 }}_EDIT_KEYWORDS_SEO_WEBSITE">Từ khóa SEO (Keywords)<code>*</code></label>
      <textarea rows="5" class="form-control" id="{{ $uuid1 }}_EDIT_KEYWORDS_SEO_WEBSITE" placeholder="Nhập từ khóa SEO, cách nhau bằng dấu phẩy..."></textarea>
      <div class="mt-2">
        <span class="text-muted">
          <span>VD: táo envy, nho Mỹ, cherry New Zealand</span>
        </span>
      </div>
      <span class="error-message" id="MSG_{{ $uuid1 }}_EDIT_KEYWORDS_SEO_WEBSITE"></span>
    </div>
  </div>

  <div class="section-block col-lg-12 col-md-12">
    <div class="form-group">
      <label for="{{ $uuid1 }}_EDIT_MO_TA_CHI_TIET">Mô tả chi tiết<code>*</code></label>
      <textarea rows="5" class="form-control" id="{{ $uuid1 }}_EDIT_MO_TA_CHI_TIET" placeholder=""></textarea>
      <span class="error-message"></span>
    </div>
  </div>

  <div class="section-block col-lg-12 col-md-12">
    <div class="form-group">
      <label for="{{ $uuid2 }}_EDIT_DAC_DIEM">Đặc điểm</label>
      <textarea rows="5" class="form-control" id="{{ $uuid2 }}_EDIT_DAC_DIEM" placeholder=""></textarea>
      <span class="error-message" id="MSG_{{ $uuid2 }}_EDIT_DAC_DIEM"></span>
    </div>
  </div>

  <p class="card-description">Tệp tải lên</p>
  <div class="section-block form-group col-md-12 col-sm-12">
    <label for="{{ $uuid1 }}_EDIT_FILE_DINH_KEM">File đính kèm</label>
        <div class="form-group">
      <button id="{{ $uuid1 }}_BTN_DANH_SACH_HINH_FILE_DINH_KEM" type="button" class="btn btn-outline-info btn-fw btn-icon-text me-2">
        <i class="icon-paper-clip btn-icon-prepend"></i>File đính kèm (<span id="{{ $uuid1 }}_SO_LUONG_FILE_DINH_KEM">0</span>)
      </button>
          <span class="error-message"></span>
        </div>
      </div>

  <p class="card-description">Trạng thái sản phẩm</p>
  <div class="section-block form-group col-md-12 col-sm-12">
  <label for="{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG" style="color: blue;">Hiển thị<code>*</code></label>
          <div>
            <label class="switch">
      <input type="checkbox" class="primary" id="{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG" checked>
              <span class="slider"></span>
            </label>
            <span class="error-message"></span>
          </div>
        </div>

  <div class="section-block form-group col-md-12 col-sm-12">
    <label for="{{ $uuid1 }}_EDIT_SAN_PHAM_NOI_BAT" style="color: blue;">Sản phẩm nổi bật</label>
    <div>
      <label class="switch">
        <input type="checkbox" class="primary" id="{{ $uuid1 }}_EDIT_SAN_PHAM_NOI_BAT">
        <span class="slider"></span>
      </label>
      <span class="error-message"></span>
    </div>
  </div>

  <div class="section-block form-group col-md-12 col-sm-12">
    <label for="{{ $uuid1 }}_EDIT_SAN_PHAM_VIP" style="color: blue;">Hiển thị ở “Chớp thời cơ. Giá như mơ!” (trang chủ)</label>
    <div>
      <label class="switch">
        <input type="checkbox" class="primary" id="{{ $uuid1 }}_EDIT_SAN_PHAM_VIP">
        <span class="slider"></span>
      </label>
      <span class="error-message"></span>
    </div>
  </div>

@if (isset($duLieu) && isset($duLieu["ID"]) && !blank($duLieu["ID"]))
<div class="section-block form-group col-md-12 col-sm-12">
          <label for="{{ $uuid1 }}_EDIT_TRANG_THAI_BAN" style="color: blue;">Sản phẩm đã bán hay chưa?</label>
  <div>
    <label class="switch">
              <input type="checkbox" class="primary" id="{{ $uuid1 }}_EDIT_TRANG_THAI_BAN">
      <span class="slider"></span>
    </label>
    <span class="error-message"></span>
  </div>
</div>
@endif

  </div>
</div>


<div class="col-12 d-flex justify-content-end margin-top-15px">
	<div class="action-web">
		<button type="button"
			class="btn btn-action btn-light btn-icon-text me-1"
			name="{{ $uuid1 }}_BTN_GO_BACK">
			<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
		</button>

		<!-- <button type="button"
			class="btn btn-outline-info btn-fw btn-icon-text me-1"
			name="{{ $uuid1 }}_BTN_REFRESH">
			<i class="fa fa-refresh btn-icon-prepend"></i>Làm mới
		</button> -->

		<button type="button"
			class="btn btn-danger btn-fw btn-icon-text me-1"
			name="{{ $uuid1 }}_BTN_DELETE">
			<i class="fa fa-trash-o btn-icon-prepend"></i>Xóa
		</button>

		<button type="button"
			class="btn btn-action btn-info btn-icon-text"
			name="{{ $uuid1 }}_BTN_SAVE">
			<i class="fa fa-save btn-icon-prepend"></i>Lưu
		</button>
	</div>

	<div class="action-mobile">
		<div class="dropdown inline-block">
			<button type="button" class="btn btn-light dropdown-toggle"
				id="dropdownMenuIconButton4" data-bs-toggle="dropdown"
				aria-haspopup="true" aria-expanded="false">Chức năng</button>
			<div class="dropdown-menu"
				aria-labelledby="dropdownMenuIconButton4">
				<button type="button" class="dropdown-item" name="{{ $uuid1 }}_BTN_DELETE">
					<i class="icon-trash icon-action-mobile"></i>Xóa
				</button>
				<div class="dropdown-divider"></div>

				<!-- <button type="button" class="dropdown-item" name="{{ $uuid1 }}_BTN_REFRESH">
					<i class="icon-refresh icon-action-mobile"></i>Làm mới
				</button>
				<div class="dropdown-divider"></div> -->

				<button type="button" class="dropdown-item" name="{{ $uuid1 }}_BTN_GO_BACK">
					<i class="icon-action-undo icon-action-mobile"></i>Quay về
				</button>
			</div>
		</div>

		<button type="button"
			class="btn btn-action btn-info btn-icon-text"
			name="{{ $uuid1 }}_BTN_SAVE">
			<i class="fa fa-save btn-icon-prepend"></i>Lưu
		</button>
	</div>
</div>

<!-- Include popups -->
@include('UI-BACKEND.admin.common.component.popup.upload-file.popup-upload-multiple-file-dinh-kem', [
  'sectionId' => $uuid1
])

<!-- Include setup tinyMCE -->
@include('UI-BACKEND.admin.common.component.editor.editor-noi-dung', [
  'sectionId' => $uuid1,
  'elementTinyMceId' => $uuid1 . '_' . 'EDIT_MO_TA_CHI_TIET'
])
@include('UI-BACKEND.admin.common.component.editor.editor-noi-dung', [
  'sectionId' => $uuid2,
  'elementTinyMceId' => $uuid2 . '_' . 'EDIT_DAC_DIEM'
])

<script>
$(document).ready(function () {
    {{ $uuid1 }}_initTinyMce('');
    {{ $uuid2 }}_initTinyMce('');

    /* START khởi tạo cleave format input giá cả */
    {{ $uuid1 }}_inputPrice = new Cleave('#{{ $uuid1 }}_EDIT_GIA_CA', {
        numeral: true,                   // Kích hoạt định dạng số
        numeralThousandsGroupStyle: 'thousand', // Nhóm số theo hàng nghìn
        delimiter: ',',                  // Dấu phân cách hàng nghìn là ','
        numeralDecimalMark: '.',         // Dấu phân cách thập phân là '.'
        numeralDecimalScale: 6,          // Giới hạn chữ số thập phân
        suffix: ' đ',                    // Thêm ký hiệu 'đ' sau số
        rawValueTrimPrefix: true,         // Đảm bảo lấy giá trị không chứa ký hiệu
        numeralPositiveOnly: true // Chỉ cho phép số dương
    });

    {{ $uuid1 }}_inputPriceGoc = new Cleave('#{{ $uuid1 }}_EDIT_GIA_GOC', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: ',',
        numeralDecimalMark: '.',
        numeralDecimalScale: 6,
        suffix: ' đ',
        rawValueTrimPrefix: true,
        numeralPositiveOnly: true
    });
    
         // Dùng jquery giới hạn đầu vào tối đa 15 chữ số và cập nhật label tiếng Việt
     $('#{{ $uuid1 }}_EDIT_GIA_CA').on('input', function(e) {
         let maxDigits = 15;
         let value = this.value.replace(/[^0-9]/g, ''); // Loại bỏ các ký tự không phải số

         if (value.length > maxDigits) {
             value = value.slice(0, maxDigits);
             this.value = value;
             {{ $uuid1 }}_inputPrice.setRawValue(value); // Cập nhật giá trị đã giới hạn vào input của Cleave
         }
         
         // Cập nhật label hiển thị số tiền bằng tiếng Việt
         updatePriceTextLabel(value);
     });

     $('#{{ $uuid1 }}_EDIT_GIA_GOC').on('input', function() {
         let maxDigits = 15;
         let value = this.value.replace(/[^0-9]/g, '');
         if (value.length > maxDigits) {
             value = value.slice(0, maxDigits);
             this.value = value;
             {{ $uuid1 }}_inputPriceGoc.setRawValue(value);
         }
     });
     
     // Function cập nhật label hiển thị số tiền bằng tiếng Việt
     function updatePriceTextLabel(value) {
         let $label = $('#{{ $uuid1 }}_PRICE_TEXT_LABEL');
         
         if (value && value.length > 0) {
             let numericValue = parseInt(value);
             if (!isNaN(numericValue) && numericValue > 0) {
                 let textValue = formatVNDToText(numericValue);
                 $label.text(textValue).show();
             } else {
                 $label.hide();
             }
         } else {
             $label.hide();
         }
     }
     /* END khởi tạo cleave format input giá cả */

    // Nạp giá hiển thị từ dữ liệu có sẵn (nếu có)
    ;(function preloadDisplayPrice() {
        let giaHienThi = @json($duLieu['GIA_HIEN_THI'] ?? ($duLieu['PRICE_DISPLAY_TEXT'] ?? null));
        if (giaHienThi) {
            $('#{{ $uuid1 }}_EDIT_GIA_HIEN_THI').val(giaHienThi);
        }
    })();

    /* Load view form theo loại sản phẩm */
    loadViewHtmlBoxUpload1File = function(uuid, tilte, callback) {
        // Cấu hình CSRF-TOKEN để vượt qua
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type : "POST",
            url : '{{ url("/html-box-upload-1-file/view") }}',
            contentType : "application/json",
            async :false,
            traditional: true,
            showLoading: true,
            data: JSON.stringify((function() { // IIFE
                let dataInput = {};

                dataInput.UUID = uuid;
                dataInput.TITLE = tilte;
                return dataInput;
            })()),
            success : function(data, textStatus, request) {
                callback(data);
            },
            error : function(request, textStatus, errorThrown) {
                // Block of code to handle errors
                showToastFailure('top-right', 'Internal server');
            },
            complete : function() {
            }
        });
    }

    $(document).on('click', '[name*="GIA_LIEN_HE"]', function(e) {
        e.preventDefault(); // Ngăn hành động mặc định

        let $checkbox = $(this);
        let isChecked = $checkbox.is(':checked');
        let priceFields = $checkbox.closest('.bien-the-san-pham')
                                   .find('input[placeholder="Giá cả"], input[placeholder="Giá gốc"]');

        showSwalWarningPopup(function callback(result) {
            if (result.isConfirmed) {
                let isDisabled = isChecked;
                priceFields.each(function(index, priceField) {
                    $(priceField).val(null);
                    $(priceField).prop('disabled', isDisabled);
                });
                $checkbox.prop('checked', isChecked);
            } else {
                $checkbox.prop('checked', !isChecked); // Trả lại trạng thái trước đó nếu không xác nhận
            }
        }, 'Bạn có muốn thay đổi <span style="display: inline-block;">trạng thái Giá liên hệ?</span><br><small>' + (isChecked ? 'Chuyển thành: <strong>Giá liên hệ</strong>' : 'Chuyển thành: <strong>Vui lòng nhập Giá cả bên dưới</strong>') + '</small>');
    });

    /* START FILE ĐÍNH KÈM */
    getDanhSachUploadMultipleFileDinhKem = function() {
        return {{ $uuid1 }}_getDanhSachUploadMultipleFileDinhKem();
    }

    $('#{{$uuid1}}_BTN_DANH_SACH_HINH_FILE_DINH_KEM').on('click', function() {
        {{$uuid1}}_handleOpenPopupUploadMultipleFileDinhKem();
        $('#{{$uuid1}}_MODAL_UPLOAD_MULTIPLE_FILE_DINH_KEM').modal('show');
    });

    // Call back
    {{ $uuid1 }}_callBackUploadMultipleFileDinhKem = function(data) {
      $("#{{$uuid1 }}_SO_LUONG_FILE_DINH_KEM").text(data.length);
    }
    /* END FILE ĐÍNH KÈM */

    /* END Handle upload danh sách FileDinhKem */
    {{ $uuid1 }}_{{$uuid1}}_EDIT_MO_TA_CHI_TIET_callBackTinyMCEAfterInit = function(editor) {
        // Mô tả chi tiết
        let moTaChiTiet = @json($duLieu['MO_TA_CHI_TIET'] ?? '');
        editor.setContent(moTaChiTiet);
    }

    {{ $uuid2 }}_{{$uuid2}}_EDIT_DAC_DIEM_callBackTinyMCEAfterInit = function(editor) {
        let dacDiem = @json($duLieu['DAC_DIEM'] ?? '');
        editor.setContent(dacDiem);
    }

         // Function để load dữ liệu giá cả từ API response
     {{ $uuid1 }}_loadPriceData = function(priceData) {
        console.log('Function loadPriceData được gọi với data:', priceData);
        
                 if (priceData && priceData.GIA_CA !== undefined) {
             let price = priceData.GIA_CA;
             console.log('Giá cả từ API:', price);
             
             // Khi GIA_CA là null, undefined, empty string hoặc 0 thì là giá liên hệ
             let isGiaLienHe = !price || price === '' || price === null || price === 0;
             console.log('Có phải giá liên hệ không:', isGiaLienHe);
             
             // Set trạng thái switch giá liên hệ
             $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').prop('checked', isGiaLienHe);
             console.log('Đã set switch giá liên hệ:', isGiaLienHe);
             
             if (!isGiaLienHe && !isEmpty(price)) {
                 let $priceInput = $('#{{ $uuid1 }}_EDIT_GIA_CA');
                 console.log('Input giá cả:', $priceInput);
                 
                 // Sử dụng Cleave object đã khởi tạo
                 // Sử dụng Cleave object đã khởi tạo
                 if (typeof {{ $uuid1 }}_inputPrice !== 'undefined' && typeof {{ $uuid1 }}_inputPrice.setRawValue === 'function') {
                     console.log('Sử dụng Cleave.js setRawValue');
                     {{ $uuid1 }}_inputPrice.setRawValue(price.toString());
                 } else {
                     console.log('Sử dụng .val() thông thường');
                     $priceInput.val(price);
                 }
                 console.log('Đã set giá cả vào input');
                 
                 // Cập nhật label hiển thị số tiền bằng tiếng Việt
                 updatePriceTextLabel(price.toString());
             }
             
             // Disable/enable input giá cả dựa trên trạng thái giá liên hệ
             $('#{{ $uuid1 }}_EDIT_GIA_CA').prop('disabled', isGiaLienHe);
             $('#{{ $uuid1 }}_EDIT_GIA_GOC').prop('disabled', isGiaLienHe);
             console.log('Đã disable/enable input giá cả:', isGiaLienHe);
         } else {
             // Khi không có dữ liệu GIA_CA, mặc định là giá liên hệ
             console.log('Không có dữ liệu GIA_CA, mặc định là giá liên hệ');
             $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').prop('checked', true);
             $('#{{ $uuid1 }}_EDIT_GIA_CA').prop('disabled', true);
             $('#{{ $uuid1 }}_EDIT_GIA_GOC').prop('disabled', true);
         }

         // Giá gốc (optional)
         let giaGoc = priceData && priceData.GIA_GOC !== undefined ? priceData.GIA_GOC : null;
         if (!isEmpty(giaGoc) && giaGoc !== 0) {
             if (typeof {{ $uuid1 }}_inputPriceGoc !== 'undefined' && typeof {{ $uuid1 }}_inputPriceGoc.setRawValue === 'function') {
                 {{ $uuid1 }}_inputPriceGoc.setRawValue(String(giaGoc));
             } else {
                 $('#{{ $uuid1 }}_EDIT_GIA_GOC').val(giaGoc);
             }
         } else if (typeof {{ $uuid1 }}_inputPriceGoc !== 'undefined' && typeof {{ $uuid1 }}_inputPriceGoc.setRawValue === 'function') {
             {{ $uuid1 }}_inputPriceGoc.setRawValue('');
         } else {
             $('#{{ $uuid1 }}_EDIT_GIA_GOC').val('');
         }
    }

    $("[name='{{ $uuid1 }}_BTN_GO_BACK']").on('click', function(e) {
        window.location = '{{ url('/admin/san-pham/danh-sach') }}';
    });

    $("[name='{{ $uuid1 }}_BTN_SAVE']").on('click', function(e) {
      {{ $uuid1 }}_saveSanPham();
    });


    /* Set thông tin sản phẩm */
    {{ $uuid1 }}_setInfoProduct = function() {
        @if (isset($duLieu) && isset($duLieu["ID"]) && !blank($duLieu["ID"])) { // Case chi tiết - chỉnh sửa
          $('#{{ $uuid1 }}_SECTION_CHI_TIET_SAN_PHAM').show();
        
          // Tên sản phẩm
          let tenSanPham = @json($duLieu['TEN_SAN_PHAM'] ?? null);
          $('#{{ $uuid1 }}_EDIT_TEN_SAN_PHAM').val(tenSanPham);

          // Mã sản phẩm
          let maSanPham = @json($duLieu['MA_SAN_PHAM'] ?? null);
          $('#{{ $uuid1 }}_EDIT_MA_SAN_PHAM').val(maSanPham);

          // Loại sản phẩm


          

          // Set danh sách file đính kèm
          let danhSachFileDinhKem = @json($duLieu['DANH_SACH_FILE_DINH_KEM'] ?? null);
          // Remove all append input upload multiple file đính kèm
          {{ $uuid1 }}_removeAllAppendInputUploadMultipleFileDinhKem();
          if (!isEmpty(danhSachFileDinhKem)) {
            // Xử lý append input danh sách file đính kèm
            {{ $uuid1 }}_appendInputUploadMultipleFileDinhKem(danhSachFileDinhKem);
            $("#{{ $uuid1 }}_SO_LUONG_FILE_DINH_KEM").text(danhSachFileDinhKem.length);
          }

          // Hiển thị
          let trangThaiHoatDong = @json($duLieu['TRANG_THAI_HOAT_DONG'] ?? null);
          trangThaiHoatDong = trangThaiHoatDong !== null ? trangThaiHoatDong : false;
          $('#{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG').prop('checked', trangThaiHoatDong);

          // Sản phẩm nổi bật
          let sanPhamNoiBat = @json($duLieu['SAN_PHAM_NOI_BAT'] ?? null);
          sanPhamNoiBat = sanPhamNoiBat === true || sanPhamNoiBat === 1 || sanPhamNoiBat === '1';
          $('#{{ $uuid1 }}_EDIT_SAN_PHAM_NOI_BAT').prop('checked', sanPhamNoiBat);

          // Chớp thời cơ trang chủ
          let sanPhamVip = @json($duLieu['SAN_PHAM_VIP'] ?? null);
          sanPhamVip = sanPhamVip === true || sanPhamVip === 1 || sanPhamVip === '1';
          $('#{{ $uuid1 }}_EDIT_SAN_PHAM_VIP').prop('checked', sanPhamVip);

          // Trạng thái sản phẩm đã bán
          let trangThaiBan = @json($duLieu['TRANG_THAI'] ?? null);
          let isBdsDaBan = trangThaiBan === 'SOLD';
          $('#{{ $uuid1 }}_EDIT_TRANG_THAI_BAN').prop('checked', isBdsDaBan);

          // Keywords SEO
          let keywordsSeo = @json($duLieu['KEYWORDS_SEO_WEBSITE'] ?? null);
          if (keywordsSeo) {
            $('#{{ $uuid1 }}_EDIT_KEYWORDS_SEO_WEBSITE').val(keywordsSeo);
          }

          // Giá cả - sẽ được load từ JavaScript khi có dữ liệu
          let giaCa = @json($duLieu['GIA_CA'] ?? null);
          let giaGoc = @json($duLieu['GIA_GOC'] ?? null);
          // Luôn gọi loadPriceData để xử lý cả trường hợp giaCa là null (giá liên hệ)
          {{ $uuid1 }}_loadPriceData({GIA_CA: giaCa, GIA_GOC: giaGoc});

      }
      @else // Case thêm mới
          $('#{{ $uuid1 }}_SECTION_CHI_TIET_SAN_PHAM').show();
      @endif
    }
    {{ $uuid1 }}_setInfoProduct();
     
     // Thêm function để gọi từ bên ngoài
     window['{{ $uuid1 }}_loadPriceData'] = {{ $uuid1 }}_loadPriceData;
     
     console.log('Đã đăng ký function vào window:', '{{ $uuid1 }}_loadPriceData');
     console.log('Function có sẵn:', typeof window['{{ $uuid1 }}_loadPriceData']);

    function {{ $uuid1 }}_saveSanPham() {
      // Reset all msg
      resetAllMsgSanPham();
      
      // Danh mục sản phẩm (multiple)
      let danhMucSanPhams = ($('#EDIT_DANH_MUC_SAN_PHAM_IDS').val() || [])
        .map(categoryId => ({ ID: Number(categoryId) }));

      // Create object data
      var data = {
          ID :  !isEmpty($('#EDIT_ID').val()) ? $('#EDIT_ID').val() : null
        , DANH_MUC_SAN_PHAMS: danhMucSanPhams
        , TEN_SAN_PHAM: $("#{{ $uuid1 }}_EDIT_TEN_SAN_PHAM").val()
        , MA_SAN_PHAM: (function() {
            let v = $("#{{ $uuid1 }}_EDIT_MA_SAN_PHAM").val();
            return !isEmpty(v) ? String(v).trim() : null;
          })()
        , KEYWORDS_SEO_WEBSITE: $('#{{ $uuid1 }}_EDIT_KEYWORDS_SEO_WEBSITE').val() || null
        , MO_TA_CHI_TIET: getEditorContent("{{ $uuid1 }}_EDIT_MO_TA_CHI_TIET")
        , MO_TA_CHI_TIET_ONLY_TEXT: getEditorContentOnlyText("{{ $uuid1 }}_EDIT_MO_TA_CHI_TIET")
        , DAC_DIEM: getEditorContent("{{ $uuid2 }}_EDIT_DAC_DIEM")
        , DAC_DIEM_ONLY_TEXT: getEditorContentOnlyText("{{ $uuid2 }}_EDIT_DAC_DIEM")
        , IS_GIA_CA_LIEN_HE: $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').is(':checked')
        , GIA_CA: (function() {
            let isGiaLienHe = $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').is(':checked');
            if (isGiaLienHe) {
                return null; // Khi bật giá liên hệ thì không lưu giá cả
            }
            return !isEmpty({{ $uuid1 }}_inputPrice.getRawValue()) ? {{ $uuid1 }}_inputPrice.getRawValue() : null;
        })()
        , GIA_GOC: (function() {
            let isGiaLienHe = $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').is(':checked');
            if (isGiaLienHe) {
                return null;
            }
            if (typeof {{ $uuid1 }}_inputPriceGoc === 'undefined' || typeof {{ $uuid1 }}_inputPriceGoc.getRawValue !== 'function') {
                return null;
            }
            let raw = {{ $uuid1 }}_inputPriceGoc.getRawValue();
            return !isEmpty(raw) ? raw : null;
        })()
        , GIA_HIEN_THI: $('#{{ $uuid1 }}_EDIT_GIA_HIEN_THI').val() || null
        , TRANG_THAI_HOAT_DONG: $('#{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG').is(':checked') ? true : false
        , SAN_PHAM_NOI_BAT: $('#{{ $uuid1 }}_EDIT_SAN_PHAM_NOI_BAT').is(':checked') ? true : false
        , SAN_PHAM_VIP: $('#{{ $uuid1 }}_EDIT_SAN_PHAM_VIP').is(':checked') ? true : false
      };

      // Danh sách hình ảnh đại diện
      let danhSachHinhAnhDaiDien = [];
      for (let [index, [key, value]] of Object.entries(getDanhSachUploadHinhAnhDaiDien()).entries()) {
        if (key === 'DANH_SACH_HINH_ANH_DAI_DIEN') {
          for (let key in value) {
            danhSachHinhAnhDaiDien.push(value[key]);
          }
        }
      }
      data['DANH_SACH_HINH_ANH_DAI_DIEN'] = danhSachHinhAnhDaiDien;

      // Danh sách hình ảnh
      let danhSachHinhAnh = [];
      for (let [index, [key, value]] of Object.entries(getDanhSachUploadMultipleHinhAnh()).entries()) {
        if (key === 'DANH_SACH_HINH_ANH') {
          for (let key in value) {
            danhSachHinhAnh.push(value[key]);
          }
        }
      }
      data['DANH_SACH_HINH_ANH'] = danhSachHinhAnh;

      // Danh sách video
      let danhSachVideo = [];
      for (let [index, [key, value]] of Object.entries(getDanhSachUploadMultipleVideo()).entries()) {
        if (key === 'DANH_SACH_VIDEO') {
          for (let key in value) {
            danhSachVideo.push(value[key]);
          }
        }
      }
      data['DANH_SACH_VIDEO'] = danhSachVideo;
      
      // Danh sách FileDinhKem
      let danhSachFileDinhKem = [];
      for (let [index, [key, value]] of Object.entries(getDanhSachUploadMultipleFileDinhKem()).entries()) {
        if (key === 'DANH_SACH_FILE_DINH_KEM') {
          for (let key in value) {
            danhSachFileDinhKem.push(value[key]);
          }
        }
      }
      data['DANH_SACH_FILE_DINH_KEM'] = danhSachFileDinhKem;

      $.ajax({
          type: "POST", 
          url: '{{ url("/api/product/save") }}', 
          contentType: "application/json",
          showLoading: true,
          data: JSON.stringify(data), 
          success: function(data, textStatus, request) {
              // Ajax call completed successfully 
              showToastSuccess('top-right', data.STATUS_DETAIL);

              // Cập nhật input id
              $('#EDIT_ID').val(data.DATAS.PRODUCT.ID);

              // Cập nhật url có id vừa save
              {{ $uuid1 }}_updUrlProductId(data.DATAS.PRODUCT.ID, data.DATAS.PRODUCT.TEN_SAN_PHAM_SLUG);
          },
          error: function(request, textStatus, errorThrown) {
          if (request.status === 401 || request.status === 403) {
            return;
          }
            let resp = null;
            try {
              resp = request.responseJSON || (request.responseText ? JSON.parse(request.responseText) : null);
            } catch (e) {}

            const statusDetail = resp && resp.STATUS_DETAIL ? resp.STATUS_DETAIL : 'Internal server';
            showToastFailure('top-right', statusDetail);

            const errors = resp && resp.ERRORS ? resp.ERRORS : null;
            if (!errors) {
              scrollSpanMsgInSection($('#CHI_TIET_SAN_PHAM'));
              return;
            }

            let firstErrorEl = null;

            // Map key thường -> selector
            const keyToSelector = {
              'DANH_SACH_HINH_ANH_DAI_DIEN': '#MSG_ANH_DAI_DIEN',
              'DANH_MUC_SAN_PHAMS': '#MSG_EDIT_DANH_MUC_SAN_PHAM',
              'TEN_SAN_PHAM': '#{{ $uuid1 }}_EDIT_TEN_SAN_PHAM',
              'MA_SAN_PHAM': '#{{ $uuid1 }}_EDIT_MA_SAN_PHAM',
              'KEYWORDS_SEO_WEBSITE': '#{{ $uuid1 }}_EDIT_KEYWORDS_SEO_WEBSITE',
              'GIA_CA': '#{{ $uuid1 }}_EDIT_GIA_CA',
              'GIA_GOC': '#{{ $uuid1 }}_EDIT_GIA_GOC',
              'MO_TA_CHI_TIET': '#{{ $uuid1 }}_EDIT_MO_TA_CHI_TIET',
              'DAC_DIEM': '#{{ $uuid2 }}_EDIT_DAC_DIEM',
              'TRANG_THAI_HOAT_DONG': '#{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG'
            };

            Object.keys(errors).forEach(function(key) {
              if (key.match(/DANH_SACH_BIEN_THE_SAN_PHAM\./)) return; // đã xử lý phía trên
              const selector = keyToSelector[key]
                || (key.startsWith('DANH_MUC_SAN_PHAMS.') ? '#MSG_EDIT_DANH_MUC_SAN_PHAM' : null);
              const msg = (errors[key] || []).join(' ');
              if (!selector) return;
              if (selector.startsWith('#MSG_')) {
                $(selector).text(msg);
              } else {
                const $el = $(selector);
                $el.closest('.form-group').find('.error-message').text(msg);
                if (!firstErrorEl && $el.length) firstErrorEl = $el;
              }
            });

            if (firstErrorEl && firstErrorEl.length) {
              firstErrorEl[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
          } else {
            scrollSpanMsgInSection($('#CHI_TIET_SAN_PHAM'));
          }
        },
        complete: function() {
        }
    });
	}

  /* Xử lý switch active hoạt động */
  $('#{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG').on('click', function(e) {
      e.preventDefault(); // Ngăn không cho active hoặc unactive hoạt động đến khi confirm

      let isActived = $('#{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG').is(':checked');
      showSwalWarningPopup(function callback(result) {
        if (result.isConfirmed === true) {
          $('#{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG').prop('checked', isActived);
            } else if (result.isDismissed === true) {
              /* Xử lý trả lại dữ liệu switch hoạt động trước đó */
              $('#{{ $uuid1 }}_EDIT_TRANG_THAI_HOAT_DONG').prop('checked', !isActived);
              } else if (result.isDenied === true) {

        }
      }, 'Bạn có muốn thay đổi <span style="display: inline-block;">Hiển thị?</span>');
	});

  {{ $uuid1 }}_updUrlProductId = function(productId, productNameSlug) {
        let url = '{{ url("/admin/san-pham/chi-tiet") }}';
        if (isEmpty(productId)) return updUrlWithoutReloadPage(url);
        if (!isEmpty(productNameSlug)) {
          url += '/' + productNameSlug + "-" + productId;
        } else {
          url += '/' + productId;
        }
        return updUrlWithoutReloadPage(url);
  }



  // Xử lý switch sản phẩm đã bán
  $('#{{ $uuid1 }}_EDIT_TRANG_THAI_BAN').on('click', function(e) {
      e.preventDefault(); // Ngăn không cho thay đổi trạng thái đến khi confirm

      let isChecked = $(this).is(':checked');
      let productId = '{{ $duLieu["ID"] ?? "" }}';
      
      if (!productId) {
          showToastFailure('top-right', 'Không tìm thấy ID sản phẩm');
          $(this).prop('checked', !isChecked);
          return;
      }
      
      showSwalWarningPopup(function callback(result) {
          if (result.isConfirmed === true) {
              // Gọi API sold
              $.ajax({
                  type: 'PATCH',
                  url: '{{ url("/api/product/sold") }}/' + productId,
                  contentType: 'application/json',
                  headers: {
                      'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  data: JSON.stringify({
                      STATUS: isChecked ? 'SOLD' : 'USING'
                  }),
                  success: function(response) {
                      if (response.STATUS === true) {
                          showToastSuccess('top-right', 'Cập nhật trạng thái sản phẩm thành công');
                          $('#{{ $uuid1 }}_EDIT_TRANG_THAI_BAN').prop('checked', isChecked);
                      } else {
                          showToastFailure('top-right', response.STATUS_DETAIL || 'Cập nhật thất bại');
                          $('#{{ $uuid1 }}_EDIT_TRANG_THAI_BAN').prop('checked', !isChecked);
                      }
                  },
                  error: function(xhr) {
                      showToastFailure('top-right', 'Có lỗi xảy ra khi cập nhật trạng thái');
                      $('#{{ $uuid1 }}_EDIT_TRANG_THAI_BAN').prop('checked', !isChecked);
                  }
              });
          } else if (result.isDismissed === true) {
              // Xử lý trả lại dữ liệu switch trước đó
              $('#{{ $uuid1 }}_EDIT_TRANG_THAI_BAN').prop('checked', !isChecked);
          } else if (result.isDenied === true) {
              // Xử lý trả lại dữ liệu switch trước đó
              $('#{{ $uuid1 }}_EDIT_TRANG_THAI_BAN').prop('checked', !isChecked);
          }
      }, 'Bạn có muốn thay đổi <span style="display: inline-block;">trạng thái sản phẩm đã bán?</span><br><small>' + (isChecked ? 'Chuyển thành: <strong>Đã bán</strong>' : 'Chuyển thành: <strong>Đang bán</strong>') + '</small>');
  });

  // Xử lý button xóa sản phẩm
  $('[name="{{ $uuid1 }}_BTN_DELETE"]').on('click', function(e) {
      e.preventDefault();
      
      let productId = '{{ $duLieu["ID"] ?? "" }}';
      let productName = '{{ $duLieu["TEN_SAN_PHAM"] ?? "sản phẩm" }}';
      
      if (!productId) {
          showToastFailure('top-right', 'Không tìm thấy ID sản phẩm');
          return;
      }
      
      showSwalWarningPopup(function callback(result) {
          if (result.isConfirmed === true) {
              // Gọi API xóa sản phẩm
              $.ajax({
                  type: 'DELETE',
                  url: '{{ url("/api/product/delete") }}/' + productId,
                  contentType: 'application/json',
                  headers: {
                      'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(response) {
                      if (response.STATUS === true) {
                          showToastSuccess('top-right', 'Xóa sản phẩm thành công');
                          // Chuyển về trang danh sách
                          setTimeout(function() {
                              window.location.href = '{{ url("/admin/san-pham/danh-sach") }}';
                          }, 1500);
                      } else {
                          showToastFailure('top-right', response.STATUS_DETAIL || 'Xóa thất bại');
                      }
                  },
                  error: function(xhr) {
                      showToastFailure('top-right', 'Có lỗi xảy ra khi xóa sản phẩm');
                  }
              });
          }
      }, 'Bạn có muốn xóa <span style="display: inline-block;">sản phẩm này?</span><br><small class="text-danger">Hành động này không thể hoàn tác!</small>');
  });

  // Xử lý switch giá liên hệ
  $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').on('click', function(e) {
      e.preventDefault(); // Ngăn không cho thay đổi trạng thái đến khi confirm

      let isChecked = $(this).is(':checked');
      let $priceField = $('#{{ $uuid1 }}_EDIT_GIA_CA');
      let $priceGocField = $('#{{ $uuid1 }}_EDIT_GIA_GOC');
      
      showSwalWarningPopup(function callback(result) {
          if (result.isConfirmed === true) {
              if (isChecked) {
                  // Khi bật giá liên hệ: xóa giá cả, disable input và ẩn text label
                  if (typeof {{ $uuid1 }}_inputPrice !== 'undefined' && typeof {{ $uuid1 }}_inputPrice.setRawValue === 'function') {
                      {{ $uuid1 }}_inputPrice.setRawValue('');
                  } else {
                      $priceField.val('');
                  }
                  if (typeof {{ $uuid1 }}_inputPriceGoc !== 'undefined' && typeof {{ $uuid1 }}_inputPriceGoc.setRawValue === 'function') {
                      {{ $uuid1 }}_inputPriceGoc.setRawValue('');
                  } else {
                      $priceGocField.val('');
                  }
                  $priceField.prop('disabled', true);
                  $priceGocField.prop('disabled', true);
                  $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').prop('checked', true);
                  
                  // Ẩn text label hiển thị số tiền bằng tiếng Việt
                  $('#{{ $uuid1 }}_PRICE_TEXT_LABEL').hide();
              } else {
                  // Khi tắt giá liên hệ: enable input giá cả
                  $priceField.prop('disabled', false);
                  $priceGocField.prop('disabled', false);
                  $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').prop('checked', false);
              }
          } else if (result.isDismissed === true) {
              // Xử lý trả lại dữ liệu switch trước đó
              $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').prop('checked', !isChecked);
          } else if (result.isDenied === true) {
              // Xử lý trả lại dữ liệu switch trước đó
              $('#{{ $uuid1 }}_EDIT_GIA_LIEN_HE').prop('checked', !isChecked);
          }
      }, 'Bạn có muốn thay đổi <span style="display: inline-block;">trạng thái giá liên hệ?</span>');
  });

});
</script>