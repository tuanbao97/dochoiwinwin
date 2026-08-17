@extends('UI-BACKEND.admin.common.layout.master')

@section('plugin-css-for-this-page')
@stop

@section('custom-css')
@stop

@section('nav-item')
<li class="nav-item">
	<div class="d-flex align-items-baseline">
		<p class="mb-0">Admin</p>
		<i class="typcn typcn-chevron-right"></i>
		<p class="mb-0">Chi tiết đơn hàng</p>
	</div>
</li>
@stop

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0">CHI TIẾT ĐƠN HÀNG #<span id="TXT_ID">—</span></h4>
					<a href="{{ url('/admin/don-hang/danh-sach') }}" class="btn btn-light btn-icon-text">
						<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
					</a>
				</div>

				<div class="row mb-4">
					<div class="col-md-6">
						<p><strong>Khách hàng:</strong> <span id="TXT_HO_TEN"></span></p>
						<p><strong>Số điện thoại:</strong> <span id="TXT_SDT"></span></p>
						<p><strong>Email:</strong> <span id="TXT_EMAIL"></span></p>
						<p><strong>Địa chỉ:</strong> <span id="TXT_DIA_CHI"></span></p>
						<p><strong>Ghi chú:</strong> <span id="TXT_GHI_CHU"></span></p>
					</div>
					<div class="col-md-6">
						<p><strong>Ngày tạo:</strong> <span id="TXT_NGAY_TAO"></span></p>
						<p><strong>Tổng số lượng:</strong> <span id="TXT_TONG_SL"></span></p>
						<p><strong>Tổng tiền:</strong> <span id="TXT_TONG_TIEN"></span></p>
						<div class="form-group">
							<label for="EDIT_TRANG_THAI"><strong>Trạng thái đơn</strong></label>
							<select id="EDIT_TRANG_THAI" class="form-control form-select">
								<option value="PENDING">Chờ xác nhận</option>
								<option value="CONFIRMED">Đã xác nhận</option>
								<option value="SHIPPING">Đang giao</option>
								<option value="COMPLETED">Hoàn thành</option>
								<option value="CANCELLED">Đã hủy</option>
							</select>
						</div>
						<button type="button" id="BTN_SAVE_STATUS" class="btn btn-info btn-icon-text">
							<i class="fa fa-save btn-icon-prepend"></i>Cập nhật trạng thái
						</button>
					</div>
				</div>

				<h5 class="mb-3">Sản phẩm trong đơn</h5>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>STT</th>
								<th>Sản phẩm</th>
								<th>Số lượng</th>
								<th>Đơn giá</th>
								<th>Thành tiền</th>
							</tr>
						</thead>
						<tbody id="TBODY_SAN_PHAM"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('custom-js-for-this-page')
<script>
$(document).ready(function () {
	const transactionId = @json(isset($transactionId) ? (int) $transactionId : 0);

	function formatMoney(v) {
		return Number(v || 0).toLocaleString('vi-VN') + ' ₫';
	}

	function loadDetail() {
		if (!transactionId) {
			showToastFailure('top-right', 'Không tìm thấy đơn hàng.');
			return;
		}
		$.ajax({
			type: 'GET',
			url: '{{ url("/api/transaction/detail") }}/' + transactionId,
			contentType: 'application/json',
			traditional: true,
			showLoading: true,
			success: function (res) {
				const data = res && res.DATAS ? res.DATAS.TRANSACTION : null;
				if (!data) {
					showToastFailure('top-right', 'Không tìm thấy đơn hàng.');
					return;
				}
				$('#TXT_ID').text(data.MA_DON || data.ID || '');
				$('#TXT_HO_TEN').text(data.HO_TEN || '');
				$('#TXT_SDT').text(data.SO_DIEN_THOAI || '');
				$('#TXT_EMAIL').text(data.EMAIL || '');
				$('#TXT_DIA_CHI').text(data.DIA_CHI || '');
				$('#TXT_GHI_CHU').text(data.GHI_CHU || '');
				$('#TXT_NGAY_TAO').text(data.NGAY_TAO || '');
				$('#TXT_TONG_SL').text(data.TONG_SO_LUONG || 0);
				$('#TXT_TONG_TIEN').text(formatMoney(data.TONG_TIEN));
				$('#EDIT_TRANG_THAI').val(data.TRANG_THAI_GIAO_DICH || 'PENDING');

				const lines = data.DANH_SACH_SAN_PHAM || [];
				let html = '';
				lines.forEach(function (line, i) {
					html += '<tr>'
						+ '<td>' + (i + 1) + '</td>'
						+ '<td>' + (line.TEN_SAN_PHAM || ('SP #' + (line.PRODUCT_ID || ''))) + '</td>'
						+ '<td>' + (line.SO_LUONG || 0) + '</td>'
						+ '<td>' + formatMoney(line.DON_GIA) + '</td>'
						+ '<td>' + formatMoney(line.THANH_TIEN) + '</td>'
						+ '</tr>';
				});
				$('#TBODY_SAN_PHAM').html(html || '<tr><td colspan="5" class="text-center">Không có sản phẩm</td></tr>');
			},
			error: function (request) {
				try {
					request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				} catch (e) {}
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
			}
		});
	}

	$('#BTN_SAVE_STATUS').on('click', function () {
		$.ajax({
			type: 'PUT',
			url: '{{ url("/api/transaction/status") }}/' + transactionId,
			contentType: 'application/json',
			data: JSON.stringify({ TRANSACTION_STATUS: $('#EDIT_TRANG_THAI').val() }),
			showLoading: true,
			success: function (res) {
				showToastSuccess('top-right', res.STATUS_DETAIL || 'Cập nhật thành công.');
				loadDetail();
			},
			error: function (request) {
				try {
					request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				} catch (e) {}
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
			}
		});
	});

	loadDetail();
});
</script>
@stop
