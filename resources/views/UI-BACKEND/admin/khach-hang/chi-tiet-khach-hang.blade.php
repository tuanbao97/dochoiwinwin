@extends('UI-BACKEND.admin.common.layout.master')

@section('plugin-css-for-this-page')
@stop

@section('custom-css')
<style>
	.ww-customer { color: #0f172a; }
	.ww-customer__card {
		background: #fff;
		border: 1px solid rgba(15, 23, 42, 0.08);
		border-radius: 16px;
		box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
		overflow: hidden;
	}
	.ww-customer__hero {
		display: flex;
		gap: 16px;
		align-items: center;
		padding: 18px;
		background: linear-gradient(180deg, #fff7ed 0%, #fff 72%);
		border-bottom: 1px solid rgba(15, 23, 42, 0.06);
	}
	.ww-customer__avatar {
		width: 64px;
		height: 64px;
		border-radius: 18px;
		display: flex;
		align-items: center;
		justify-content: center;
		background: #ea580c;
		color: #fff;
		font-size: 26px;
		font-weight: 800;
		flex: 0 0 auto;
	}
	.ww-customer__name { margin: 0; font-size: 22px; font-weight: 800; line-height: 1.25; }
	.ww-customer__badge {
		display: inline-block;
		margin-top: 6px;
		padding: 3px 8px;
		border-radius: 999px;
		background: #eff6ff;
		color: #1d4ed8;
		font-size: 12px;
		font-weight: 700;
	}
	.ww-customer__meta {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 10px 16px;
		padding: 16px 18px 18px;
	}
	.ww-customer__meta div { min-width: 0; }
	.ww-customer__meta span {
		display: block;
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 0.04em;
		text-transform: uppercase;
		color: #64748b;
	}
	.ww-customer__meta strong {
		display: block;
		margin-top: 3px;
		font-size: 15px;
		font-weight: 700;
		line-height: 1.4;
		word-break: break-word;
	}
	.ww-customer__meta a { color: #0f172a; text-decoration: none; }
	.ww-customer__stats {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 12px;
		margin-top: 14px;
	}
	.ww-customer__stat {
		padding: 14px 14px 12px;
		border-radius: 14px;
		background: #fff7ed;
		border: 1px solid #fdba74;
	}
	.ww-customer__stat span {
		display: block;
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 0.04em;
		text-transform: uppercase;
		color: #c2410c;
	}
	.ww-customer__stat strong {
		display: block;
		margin-top: 4px;
		font-size: 20px;
		line-height: 1.2;
		color: #9a3412;
		white-space: nowrap;
	}
	.ww-customer__stat small { display: block; margin-top: 2px; color: #9a3412; font-size: 12px; }
	.ww-customer__stat--count { background: #eff6ff; border-color: #93c5fd; }
	.ww-customer__stat--count span { color: #1d4ed8; }
	.ww-customer__stat--count strong, .ww-customer__stat--count small { color: #1e3a8a; }
	.ww-customer__stat--done { background: #f0fdf4; border-color: #86efac; }
	.ww-customer__stat--done span { color: #15803d; }
	.ww-customer__stat--done strong, .ww-customer__stat--done small { color: #14532d; }
	.ww-customer__stat--cancel { background: #fef2f2; border-color: #fca5a5; }
	.ww-customer__stat--cancel span { color: #b91c1c; }
	.ww-customer__stat--cancel strong { color: #7f1d1d; }
	.ww-customer__orders { margin-top: 14px; }
	.ww-customer__orders h4 { margin: 0 0 12px; font-size: 16px; font-weight: 800; }
	.ww-customer__table { width: 100%; border-collapse: collapse; }
	.ww-customer__table th {
		text-align: left;
		font-size: 12px;
		letter-spacing: 0.04em;
		text-transform: uppercase;
		color: #64748b;
		padding: 10px 12px;
		border-bottom: 1px solid rgba(15, 23, 42, 0.08);
	}
	.ww-customer__table td {
		padding: 12px;
		border-bottom: 1px solid rgba(15, 23, 42, 0.06);
		vertical-align: middle;
		font-size: 14px;
	}
	.ww-customer__table tr:last-child td { border-bottom: 0; }
	.ww-customer__table a { font-weight: 800; color: #0f172a; text-decoration: none; }
	.ww-customer__table a:hover { color: #ea580c; }
	.ww-customer__money { font-weight: 800; color: #14532d; white-space: nowrap; }
	.ww-customer__money--off { color: #94a3b8; text-decoration: line-through; }
	.ww-customer__status {
		display: inline-flex;
		padding: 4px 10px;
		border-radius: 999px;
		font-size: 12px;
		font-weight: 700;
		white-space: nowrap;
	}
	.ww-customer__status--pending { color: #b45309; background: #fffbeb; }
	.ww-customer__status--confirmed { color: #1d4ed8; background: #eff6ff; }
	.ww-customer__status--shipping { color: #7c3aed; background: #f5f3ff; }
	.ww-customer__status--completed { color: #15803d; background: #f0fdf4; }
	.ww-customer__status--cancelled { color: #b91c1c; background: #fef2f2; }
	.ww-customer__empty { padding: 28px 12px; text-align: center; color: #64748b; }
	@media (max-width: 991px) {
		.ww-customer__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
	}
	@media (max-width: 575px) {
		.ww-customer__meta, .ww-customer__stats { grid-template-columns: 1fr; }
		.ww-customer__stat strong { white-space: normal; }
	}
</style>
@stop

@section('nav-item')
<li class="nav-item">
	<div class="d-flex align-items-baseline">
		<p class="mb-0">Admin</p>
		<i class="typcn typcn-chevron-right"></i>
		<p class="mb-0">Khách hàng</p>
		<i class="typcn typcn-chevron-right"></i>
		<p class="mb-0">Chi tiết</p>
	</div>
</li>
@stop

@section('content')
<div class="ww-customer">
	<div class="d-flex justify-content-end mb-3">
		<a href="{{ url('/admin/khach-hang/danh-sach') }}" class="btn btn-action btn-light btn-icon-text">
			<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
		</a>
	</div>
	<div id="CUSTOMER_DETAIL"></div>
</div>
@stop

@section('plugin-js-for-this-page')
@stop

@section('custom-js-for-this-page')
<script>
function formatMoneyVnd(v) {
	return Number(v || 0).toLocaleString('vi-VN') + ' ₫';
}
function escHtml(value) {
	return $('<div>').text(value == null ? '' : String(value)).html();
}
function statusClass(status) {
	var map = { PENDING: 'pending', CONFIRMED: 'confirmed', SHIPPING: 'shipping', COMPLETED: 'completed', CANCELLED: 'cancelled' };
	return map[status] || 'pending';
}
function lineOrDash(value) {
	return value ? escHtml(value) : '—';
}

$(document).ready(function () {
	$.ajax({
		type: 'GET',
		url: '{{ url("/api/customer") }}/{{ $customerKey }}',
		cache: false,
		success: function (json) {
			var customer = json && json.DATAS ? json.DATAS.CUSTOMER : null;
			if (!json || json.STATUS !== true || !customer) {
				$('#CUSTOMER_DETAIL').html('<div class="ww-customer__card"><div class="ww-customer__empty">Không tìm thấy khách hàng.</div></div>');
				return;
			}
			renderCustomer(customer);
		},
		error: function (request) {
			var message = 'Không tải được khách hàng.';
			try {
				var body = request.responseJSON || (request.responseText ? JSON.parse(request.responseText) : null);
				if (body && body.STATUS_DETAIL) message = body.STATUS_DETAIL;
			} catch (e) {}
			$('#CUSTOMER_DETAIL').html('<div class="ww-customer__card"><div class="ww-customer__empty">' + escHtml(message) + '</div></div>');
		}
	});
});

function renderCustomer(customer) {
	var initial = (customer.HO_TEN || 'K').trim().charAt(0).toUpperCase();
	var phoneDigits = String(customer.SO_DIEN_THOAI || '').replace(/\D/g, '');
	var phone = customer.SO_DIEN_THOAI
		? '<a href="tel:' + escHtml(phoneDigits) + '">' + escHtml(typeof formatPhoneNumber === 'function' ? formatPhoneNumber(customer.SO_DIEN_THOAI) : customer.SO_DIEN_THOAI) + '</a>'
		: '—';
	var email = customer.EMAIL ? '<a href="mailto:' + escHtml(customer.EMAIL) + '">' + escHtml(customer.EMAIL) + '</a>' : '—';
	var account = customer.CO_TAI_KHOAN ? '<div class="ww-customer__badge">Có tài khoản' + (customer.NGAY_TAO_TAI_KHOAN ? ' · ' + escHtml(customer.NGAY_TAO_TAI_KHOAN) : '') + '</div>' : '';
	var orders = customer.DON_HANG || [];
	var orderRows = orders.length ? orders.map(function (order) {
		return '<tr>'
			+ '<td><a href="{{ url("/admin/don-hang/chi-tiet") }}/dh-' + order.ID + '">' + escHtml(order.MA_DON) + '</a></td>'
			+ '<td><span class="ww-customer__status ww-customer__status--' + statusClass(order.TRANG_THAI) + '">' + escHtml(order.TRANG_THAI_TEXT) + '</span></td>'
			+ '<td>' + Number(order.SO_LUONG || 0).toLocaleString('vi-VN') + '</td>'
			+ '<td class="ww-customer__money' + (order.TRANG_THAI === 'CANCELLED' ? ' ww-customer__money--off' : '') + '">' + formatMoneyVnd(order.TONG_TIEN) + '</td>'
			+ '<td>' + lineOrDash(order.NGAY_TAO) + '</td>'
			+ '</tr>';
	}).join('') : '<tr><td colspan="5"><div class="ww-customer__empty">Khách này chưa có đơn hàng.</div></td></tr>';

	$('#CUSTOMER_DETAIL').html(
		'<div class="ww-customer__card">'
			+ '<div class="ww-customer__hero">'
				+ '<div class="ww-customer__avatar">' + escHtml(initial) + '</div>'
				+ '<div><h3 class="ww-customer__name">' + escHtml(customer.HO_TEN) + '</h3>' + account + '</div>'
			+ '</div>'
			+ '<div class="ww-customer__meta">'
				+ '<div><span>Số điện thoại</span><strong>' + phone + '</strong></div>'
				+ '<div><span>Email</span><strong>' + email + '</strong></div>'
				+ '<div><span>Địa chỉ</span><strong>' + lineOrDash(customer.DIA_CHI) + '</strong></div>'
				+ '<div><span>Mua từ</span><strong>' + lineOrDash(customer.MUA_TU) + '</strong></div>'
			+ '</div>'
		+ '</div>'
		+ '<div class="ww-customer__stats">'
			+ '<div class="ww-customer__stat"><span>Tổng chi tiêu</span><strong>' + formatMoneyVnd(customer.TONG_CHI_TIEU) + '</strong><small>Không tính đơn đã hủy</small></div>'
			+ '<div class="ww-customer__stat ww-customer__stat--count"><span>Số đơn</span><strong>' + Number(customer.SO_DON || 0).toLocaleString('vi-VN') + '</strong><small>Trung bình ' + formatMoneyVnd(customer.DON_TRUNG_BINH) + '</small></div>'
			+ '<div class="ww-customer__stat ww-customer__stat--done"><span>Hoàn thành</span><strong>' + Number(customer.SO_DON_HOAN_THANH || 0).toLocaleString('vi-VN') + '</strong><small>' + formatMoneyVnd(customer.TONG_HOAN_THANH) + '</small></div>'
			+ '<div class="ww-customer__stat ww-customer__stat--cancel"><span>Đã hủy</span><strong>' + Number(customer.SO_DON_DA_HUY || 0).toLocaleString('vi-VN') + '</strong></div>'
		+ '</div>'
		+ '<div class="ww-customer__card ww-customer__orders">'
			+ '<div style="padding:16px 16px 4px;"><h4>Đơn hàng</h4></div>'
			+ '<div style="overflow:auto;">'
				+ '<table class="ww-customer__table"><thead><tr><th>Mã đơn</th><th>Trạng thái</th><th>Số lượng</th><th>Thành tiền</th><th>Ngày tạo</th></tr></thead><tbody>'
				+ orderRows
				+ '</tbody></table>'
			+ '</div>'
		+ '</div>'
	);
}
</script>
@stop
