@extends('UI-BACKEND.admin.common.layout.master')

@section('plugin-css-for-this-page')
@stop

@section('custom-css')
<style>
.ww-admin-order { color: #0f172a; }
.ww-admin-order__card {
	background: #fff;
	border: 1px solid rgba(15, 23, 42, 0.08);
	border-radius: 16px;
	overflow: hidden;
	box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}
.ww-admin-order__head {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	gap: 12px;
	padding: 16px 18px;
	background: linear-gradient(180deg, #fff7ed 0%, #fff 70%);
	border-bottom: 1px solid rgba(15, 23, 42, 0.06);
}
.ww-admin-order__code { margin: 0; font-size: 18px; font-weight: 700; }
.ww-admin-order__date { display: block; margin-top: 4px; color: #64748b; font-size: 13px; }
.ww-admin-order__status {
	display: inline-flex;
	align-items: center;
	padding: 6px 12px;
	border-radius: 999px;
	font-size: 13px;
	font-weight: 700;
	white-space: nowrap;
}
.ww-admin-order__status--pending { color: #b45309; background: #fffbeb; }
.ww-admin-order__status--confirmed { color: #1d4ed8; background: #eff6ff; }
.ww-admin-order__status--shipping { color: #7c3aed; background: #f5f3ff; }
.ww-admin-order__status--completed { color: #15803d; background: #f0fdf4; }
.ww-admin-order__status--cancelled { color: #b91c1c; background: #fef2f2; }
.ww-admin-order__cancel {
	display: flex;
	gap: 10px;
	align-items: flex-start;
	margin: 14px 18px 0;
	padding: 12px 14px;
	border-radius: 12px;
	background: #fef2f2;
	color: #991b1b;
}
.ww-admin-order__cancel small { display: block; font-size: 12px; }
.ww-admin-order__products { padding: 6px 8px; }
.ww-admin-order__product {
	display: grid;
	grid-template-columns: 72px 1fr auto;
	gap: 12px;
	align-items: center;
	padding: 12px 10px;
	border-bottom: 1px solid rgba(15, 23, 42, 0.06);
}
.ww-admin-order__product:last-child { border-bottom: 0; }
.ww-admin-order__image {
	width: 72px;
	height: 72px;
	border-radius: 12px;
	overflow: hidden;
	background: #f8fafc;
	border: 1px solid rgba(15, 23, 42, 0.06);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 28px;
}
.ww-admin-order__image img { width: 100%; height: 100%; object-fit: cover; display: block; }
.ww-admin-order__name { font-weight: 700; color: #0f172a; text-decoration: none; }
.ww-admin-order__name:hover { color: #ea580c; }
.ww-admin-order__facts { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
.ww-admin-order__fact {
	display: flex;
	flex-direction: column;
	min-width: 92px;
	padding: 6px 12px 7px;
	border-radius: 10px;
	background: #fff7ed;
	border: 1px solid #fdba74;
}
.ww-admin-order__fact span {
	font-size: 11px;
	font-weight: 700;
	letter-spacing: 0.04em;
	text-transform: uppercase;
	color: #c2410c;
}
.ww-admin-order__fact strong { font-size: 20px; line-height: 1.15; color: #9a3412; }
.ww-admin-order__fact--price { background: #eff6ff; border-color: #93c5fd; }
.ww-admin-order__fact--price span { color: #1d4ed8; }
.ww-admin-order__fact--price strong { color: #1e3a8a; font-size: 18px; }
.ww-admin-order__line { text-align: right; white-space: nowrap; }
.ww-admin-order__line strong { display: block; font-size: 15px; color: #0f172a; }
.ww-admin-order__line span { color: #64748b; font-size: 12px; }
.ww-admin-order__delivery {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 12px;
	padding: 14px 18px;
	background: #f8fafc;
}
.ww-admin-order__delivery div { min-width: 0; }
.ww-admin-order__delivery span, .ww-admin-order__track span { display: block; color: #64748b; font-size: 12px; }
.ww-admin-order__delivery strong, .ww-admin-order__track strong { display: block; margin-top: 2px; overflow-wrap: anywhere; }
.ww-admin-order__delivery .is-wide { grid-column: 1 / -1; }
.ww-admin-order__track {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 12px;
	padding: 14px 18px;
}
.ww-admin-order__track article {
	padding: 12px 14px;
	border: 1px solid rgba(15, 23, 42, 0.08);
	border-radius: 12px;
	background: #f8fafc;
}
.ww-admin-order__pay { padding: 8px 18px 16px; background: #fffdfb; border-top: 1px solid rgba(15, 23, 42, 0.06); }
.ww-admin-order__pay h5 { margin: 10px 0 6px; font-size: 15px; font-weight: 700; }
.ww-admin-order__row {
	display: flex;
	justify-content: space-between;
	gap: 16px;
	padding: 7px 0;
	color: #475569;
}
.ww-admin-order__row b { color: #0f172a; white-space: nowrap; }
.ww-admin-order__row--save b { color: #15803d; }
.ww-admin-order__row--total {
	margin-top: 6px;
	padding-top: 12px;
	border-top: 1px dashed rgba(15, 23, 42, 0.16);
	font-size: 16px;
	font-weight: 700;
	color: #0f172a;
}
.ww-admin-order__voucher {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 2px 8px;
	border-radius: 999px;
	background: #fff7ed;
	color: #c2410c;
	font-weight: 700;
	letter-spacing: 0.03em;
}
.ww-admin-order__side .card { border-radius: 16px; }
@media (max-width: 767px) {
	.ww-admin-order__product { grid-template-columns: 64px 1fr; }
	.ww-admin-order__line { grid-column: 2; text-align: left; }
	.ww-admin-order__delivery, .ww-admin-order__track { grid-template-columns: 1fr; }
}
</style>
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
<div class="row ww-admin-order">
	<div class="col-lg-8">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h4 class="card-title mb-0">Chi tiết đơn hàng</h4>
			<a href="{{ url('/admin/don-hang/danh-sach') }}" class="btn btn-light btn-icon-text">
				<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
			</a>
		</div>
		<div class="ww-admin-order__card" id="ORDER_CARD">
			<div class="p-4 text-muted">Đang tải đơn hàng…</div>
		</div>
	</div>
	<div class="col-lg-4 ww-admin-order__side">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">Cập nhật trạng thái</h5>
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
				<hr>
				<p class="mb-1"><strong>Tổng số lượng:</strong> <span id="TXT_TONG_SL">—</span></p>
				<p class="mb-0"><strong>Khách phải trả:</strong> <span id="TXT_TONG_TIEN">—</span></p>
			</div>
		</div>
	</div>
</div>
@stop

@section('custom-js-for-this-page')
<script>
$(document).ready(function () {
	const transactionId = @json(isset($transactionId) ? (int) $transactionId : 0);
	const appUrl = @json(rtrim(url('/'), '/'));

	function escapeHtml(value) {
		return String(value == null ? '' : value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function formatMoney(value) {
		return Number(value || 0).toLocaleString('vi-VN') + ' ₫';
	}

	function dateTime(value) {
		if (!value) return '';
		const date = new Date(value);
		return isNaN(date.getTime()) ? String(value) : date.toLocaleString('vi-VN');
	}

	function imageUrl(value) {
		if (!value) return '';
		value = String(value);
		if (/^(https?:)?\/\//i.test(value) || value.indexOf('data:') === 0) return value;
		return appUrl + '/' + value.replace(/^\/+/, '');
	}

	function productUrl(handle, productId) {
		const id = Number(productId) || 0;
		let slug = String(handle || '').replace(/^\/+/, '');
		if (slug === '') return id > 0 ? appUrl + '/san-pham/chi-tiet/sp-' + id : '#';
		if (id > 0 && !/-\d+$/.test(slug)) slug += '-' + id;
		return appUrl + '/san-pham/chi-tiet/' + slug;
	}

	function statusClass(status) {
		const allowed = ['PENDING', 'CONFIRMED', 'SHIPPING', 'COMPLETED', 'CANCELLED'];
		return allowed.indexOf(status) >= 0 ? status.toLowerCase() : 'pending';
	}

	function shippingText(data) {
		const fee = Number(data.SHIPPING_FEE || 0);
		if (fee > 0) return formatMoney(fee);
		const note = String(data.GHI_CHU || '') + ' ' + String(data.DIA_CHI || '');
		if (/nhận tại cửa hàng|nhan tai cua hang/i.test(note)) return 'Miễn phí';
		return 'Chi phí thỏa thuận';
	}

	function renderProduct(line) {
		const href = productUrl(line.HANDLE, line.PRODUCT_ID);
		const image = imageUrl(line.HINH_ANH);
		const name = line.TEN_SAN_PHAM || ('Sản phẩm #' + (line.PRODUCT_ID || ''));
		const thumb = image
			? '<a class="ww-admin-order__image" href="' + escapeHtml(href) + '" target="_blank" rel="noopener"><img src="' + escapeHtml(image) + '" alt="' + escapeHtml(name) + '"></a>'
			: '<div class="ww-admin-order__image">🧸</div>';
		return '<div class="ww-admin-order__product">'
			+ thumb
			+ '<div><a class="ww-admin-order__name" href="' + escapeHtml(href) + '" target="_blank" rel="noopener">' + escapeHtml(name) + '</a>'
			+ '<div class="ww-admin-order__facts">'
			+ '<div class="ww-admin-order__fact"><span>Số lượng</span><strong>' + escapeHtml(line.SO_LUONG || 0) + '</strong></div>'
			+ '<div class="ww-admin-order__fact ww-admin-order__fact--price"><span>Đơn giá</span><strong>' + formatMoney(line.DON_GIA) + '</strong></div>'
			+ '</div></div>'
			+ '<div class="ww-admin-order__line"><span>Thành tiền</span><strong>' + formatMoney(line.THANH_TIEN) + '</strong></div>'
			+ '</div>';
	}

	function payRow(label, value, modifier) {
		return '<div class="ww-admin-order__row' + (modifier ? ' ' + modifier : '') + '"><span>' + label + '</span><b>' + value + '</b></div>';
	}

	function renderOrder(data) {
		const lines = data.DANH_SACH_SAN_PHAM || [];
		let vouchers = Array.isArray(data.VOUCHERS) ? data.VOUCHERS : [];
		if (!vouchers.length && Number(data.DISCOUNT_AMOUNT || 0) > 0) {
			vouchers = [{ CODE: data.DISCOUNT_CODE, AMOUNT: data.DISCOUNT_AMOUNT }];
		}
		const cancel = data.TRANG_THAI_GIAO_DICH === 'CANCELLED'
			? '<div class="ww-admin-order__cancel"><span>!</span><span><small>Lý do hủy đơn</small><strong>'
				+ escapeHtml(data.LY_DO_HUY || 'Chưa có lý do hủy') + '</strong></span></div>'
			: '';
		const note = data.GHI_CHU
			? '<div class="is-wide"><span>Ghi chú</span><strong>' + escapeHtml(data.GHI_CHU) + '</strong></div>'
			: '';
		const voucherRows = vouchers.map(function (voucher) {
			const code = escapeHtml(String(voucher.CODE || '').toUpperCase() || 'Mã giảm giá');
			return payRow('Voucher <span class="ww-admin-order__voucher">' + code + '</span>', '-' + formatMoney(voucher.AMOUNT), 'ww-admin-order__row--save');
		}).join('');

		return '<div class="ww-admin-order__head">'
			+ '<div><h5 class="ww-admin-order__code">Đơn hàng ' + escapeHtml(data.MA_DON || ('#' + data.ID)) + '</h5>'
			+ '<span class="ww-admin-order__date">' + escapeHtml(dateTime(data.NGAY_TAO)) + '</span></div>'
			+ '<span class="ww-admin-order__status ww-admin-order__status--' + statusClass(data.TRANG_THAI_GIAO_DICH) + '">'
			+ escapeHtml(data.TRANG_THAI_GIAO_DICH_TEXT || data.TRANG_THAI_GIAO_DICH || '') + '</span></div>'
			+ cancel
			+ '<div class="ww-admin-order__products">' + (lines.map(renderProduct).join('') || '<div class="p-3 text-muted">Không có sản phẩm</div>') + '</div>'
			+ '<div class="ww-admin-order__delivery">'
			+ '<div><span>Người nhận</span><strong>' + escapeHtml(data.HO_TEN || '—') + '</strong></div>'
			+ '<div><span>Điện thoại</span><strong>' + escapeHtml(data.SO_DIEN_THOAI || '—') + '</strong></div>'
			+ '<div><span>Email</span><strong>' + escapeHtml(data.EMAIL || '—') + '</strong></div>'
			+ '<div class="is-wide"><span>Địa chỉ</span><strong>' + escapeHtml(data.DIA_CHI || '—') + '</strong></div>'
			+ note
			+ '</div>'
			+ '<div class="ww-admin-order__track">'
			+ '<article><span>Nhân viên phụ trách</span><strong>' + escapeHtml(data.NHAN_VIEN || 'Chưa phân công') + '</strong></article>'
			+ '<article><span>Ngày hẹn giao</span><strong>' + escapeHtml(dateTime(data.NGAY_HEN_GIAO) || 'Chưa có lịch hẹn') + '</strong></article>'
			+ '</div>'
			+ '<div class="ww-admin-order__pay"><h5>Chi tiết thanh toán</h5>'
			+ payRow('Tổng tiền hàng', formatMoney(data.SUBTOTAL))
			+ payRow('Phí vận chuyển', shippingText(data))
			+ voucherRows
			+ payRow('Thành tiền', formatMoney(data.TONG_TIEN), 'ww-admin-order__row--total')
			+ payRow('Phương thức thanh toán', escapeHtml(data.PHUONG_THUC_THANH_TOAN || 'Thanh toán khi nhận hàng (COD)'))
			+ '</div>';
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
				$('#ORDER_CARD').html(renderOrder(data));
				$('#TXT_TONG_SL').text(data.TONG_SO_LUONG || 0);
				$('#TXT_TONG_TIEN').text(formatMoney(data.TONG_TIEN));
				$('#EDIT_TRANG_THAI').val(data.TRANG_THAI_GIAO_DICH || 'PENDING');
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
