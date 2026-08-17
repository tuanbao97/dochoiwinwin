@extends('UI-BACKEND.admin.common.layout.master')

@section('plugin-css-for-this-page')
@stop

@section('custom-css')
<style>
	.badge-order-status {
		font-size: 14px;
		font-weight: 600;
		padding: 0.45rem 0.7rem;
		border-radius: 0.35rem;
		white-space: nowrap;
	}
	.badge-order-pending {
		background-color: #ffc107;
		color: #212529;
	}
	.badge-order-confirmed {
		background-color: #17a2b8;
		color: #fff;
	}
	.badge-order-shipping {
		background-color: #007bff;
		color: #fff;
	}
	.badge-order-completed {
		background-color: #28a745;
		color: #fff;
	}
	.badge-order-cancelled {
		background-color: #dc3545;
		color: #fff;
	}
</style>
@stop

@section('nav-item')
<li class="nav-item">
	<div class="d-flex align-items-baseline">
		<p class="mb-0">Admin</p>
		<i class="typcn typcn-chevron-right"></i>
		<p class="mb-0">Quản lý đơn hàng</p>
	</div>
</li>
@stop

@section('content')
<div class="row">
	<div class="col-12">
		<div id="accordion" class="accordion">
			<div class="card border-primary">
				<div class="card-header border-bottom" id="heading-1"
					style="padding: 15px 15px 15px 15px; background-color: #f2f4f6 !important;">
					<h5 class="mb-0">
						<div class="row">
							<div class="col">
								<a aria-expanded="true" data-toggle="collapse" href="#collapse-1">
									<span style="font-weight: 500; color: black; font-size: 17px;">TÌM KIẾM</span>
								</a>
							</div>
						</div>
					</h5>
				</div>
				<div id="collapse-1" class="border-bottom collapse show" aria-labelledby="heading-1">
					<div class="card-body">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label for="SEARCH_TU_KHOA">Từ khóa</label>
									<input type="text" class="form-control" id="SEARCH_TU_KHOA"
										placeholder="Nhập từ khóa...">
								</div>
							</div>
							<div class="col-md-3 col-sm-12">
								<div class="form-group">
									<label for="SEARCH_TRANG_THAI">Trạng thái đơn</label>
									<select id="SEARCH_TRANG_THAI"
										class="form-control form-select form-control-default border-radius-2px">
										<option value="all" selected>Tất cả</option>
										<option value="PENDING">Chờ xác nhận</option>
										<option value="CONFIRMED">Đã xác nhận</option>
										<option value="SHIPPING">Đang giao hàng</option>
										<option value="COMPLETED">Hoàn thành</option>
										<option value="CANCELLED">Đã hủy</option>
									</select>
								</div>
							</div>
							<div class="col-12 d-flex justify-content-end">
								<button type="button" id="btnReset" style="margin-right: 0.5rem !important;"
									class="btn btn-action btn-light btn-icon-text me-1 my-2">
									<i class="fa fa-refresh btn-icon-prepend"></i>Làm mới
								</button>
								<button type="button" id="btnSearch"
									class="btn btn-action btn-info btn-icon-text my-2">
									<i class="fa fa-search btn-icon-prepend"></i>Tìm kiếm
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-8 col-sm-12">
						<h4 class="card-title">
							DANH SÁCH <span class="one-line">ĐƠN HÀNG</span>
						</h4>
					</div>
				</div>
				<div class="table-responsive data-tables" style="margin-top: 10px;">
					<table id="tableDonHang" class="table table-striped table-bordered" width="100%">
						<thead>
							<tr>
								<th>STT</th>
								<th>Thao tác</th>
								<th>Trạng thái</th>
								<th>Mã đơn</th>
								<th>Khách hàng</th>
								<th>SĐT</th>
								<th>Tổng tiền</th>
								<th>Ngày tạo</th>
								<th>Email</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th class="header-footer">STT</th>
								<th class="header-footer">Thao tác</th>
								<th class="header-footer">Trạng thái</th>
								<th class="header-footer">Mã đơn</th>
								<th class="header-footer">Khách hàng</th>
								<th class="header-footer">SĐT</th>
								<th class="header-footer">Tổng tiền</th>
								<th class="header-footer">Ngày tạo</th>
								<th class="header-footer">Email</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('plugin-js-for-this-page')
@stop

@section('custom-js-for-this-page')
<script>
var dataTableDonHang;

function formatMoneyVnd(v) {
	var n = Number(v || 0);
	return n.toLocaleString('vi-VN') + ' ₫';
}

function renderOrderStatusBadge(status, statusText) {
	var map = {
		PENDING: { cls: 'badge-order-pending', text: 'Chờ xác nhận' },
		CONFIRMED: { cls: 'badge-order-confirmed', text: 'Đã xác nhận' },
		SHIPPING: { cls: 'badge-order-shipping', text: 'Đang giao hàng' },
		COMPLETED: { cls: 'badge-order-completed', text: 'Hoàn thành' },
		CANCELLED: { cls: 'badge-order-cancelled', text: 'Đã hủy' }
	};
	var conf = map[status] || { cls: 'badge-secondary', text: statusText || status || '—' };
	return '<span class="badge badge-order-status ' + conf.cls + '">' + conf.text + '</span>';
}

$(document).ready(function () {
	dataTableDonHang = new DataTable('#tableDonHang', {
		"fixedHeader": {
			"header": false,
			"footer": false
		}
		, "scrollX": false
		, "scrollY": ""
		, "scrollCollapse": false
		, "fixedColumns": {
			"start": 0,
			"end": 0
		}
		, "responsive": false
		, "autoWidth": false
		, "paging": true
		, "pagingType": "numbers"
		, "searching": true
		, "processing": false
		, "serverSide": true
		, "deferLoading": true
		, ajax: {
			type: "GET"
			, url: '{{ url("/api/transaction/list") }}'
			, contentType: "application/json"
			, cache: false
			, traditional: false
			, showLoading: false
			, data: function (dataInput) {
				let inputSearching = !isEmpty(dataInput.search.value) ? dataInput.search.value : null;

				let startPageFrUrl = window.wwAdminListUrl ? window.wwAdminListUrl.pageIndex(10) : 0;
				dataInput.start = startPageFrUrl * Number(dataInput.length);

				let start = Number(dataInput.start);
				let length = Number(dataInput.length);

				dataInput.PAGE = (start / length) + 1;
				dataInput.PER_PAGE = !isEmpty(length) && length > 0 ? length : 2147483647;
				dataInput.DRAW = dataInput.draw;
				dataInput.TU_KHOA = !isEmpty($('#SEARCH_TU_KHOA').val())
					? $('#SEARCH_TU_KHOA').val()
					: (!isEmpty(inputSearching) ? inputSearching : '');

				let st = $('#SEARCH_TRANG_THAI').val();
				dataInput.TRANG_THAI_GIAO_DICH = (!st || st === 'all') ? null : st;

				return dataInput;
			}
			, dataSrc: function (json) {
				json.recordsTotal = 0;
				json.recordsFiltered = 0;

				if (json.STATUS === true) {
					var block = json.DATAS && json.DATAS.TRANSACTION ? json.DATAS.TRANSACTION : null;
					if (!block) {
						return [];
					}
					if (block.DATA && block.DATA.length > 0) {
						json.recordsTotal = block.TOTAL_ITEM;
						json.recordsFiltered = block.TOTAL_ITEM;
					}
					json.draw = block.DRAW;

					return (block.DATA || []).map(function (item, index) {
						return {
							STT: index
							, FULL_DU_LIEU: item
							, ID: !isEmpty(item.ID) ? item.ID : ''
							, MA_DON: !isEmpty(item.MA_DON) ? item.MA_DON : ''
							, HO_TEN: !isEmpty(item.HO_TEN) ? item.HO_TEN : ''
							, SO_DIEN_THOAI: !isEmpty(item.SO_DIEN_THOAI) ? item.SO_DIEN_THOAI : ''
							, EMAIL: !isEmpty(item.EMAIL) ? item.EMAIL : ''
							, TONG_TIEN: item.TONG_TIEN
							, TRANG_THAI_GIAO_DICH: item.TRANG_THAI_GIAO_DICH
							, TRANG_THAI_GIAO_DICH_TEXT: item.TRANG_THAI_GIAO_DICH_TEXT
							, NGAY_TAO: !isEmpty(item.NGAY_TAO) ? item.NGAY_TAO : ''
						};
					});
				}

				showToastFailure('top-right', json ? json.STATUS_DETAIL : 'Internal server');
				return [];
			}
			, error: function (request) {
				try {
					request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				} catch (e) {}
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
			}
		}
		, "columns": [
			{
				"title": "STT"
				, "data": "STT"
				, "visible": true
				, "className": "text-center td-stt"
				, "searchable": true
				, "orderable": false
				, "width": "70px"
				, "render": function (data, type, row) {
					let id = !isEmpty(row.ID) ? row.ID : '';
					return '<span data-id="' + id + '">' + data + '</span>';
				}
			}
			, {
				"title": "Thao tác"
				, "data": "FULL_DU_LIEU"
				, "visible": true
				, "className": "dataTable-td-thao-tac text-center"
				, "searchable": false
				, "orderable": false
				, "width": "auto"
				, "render": function () {
					return `
						<div class="action-web">
							<button type="button" action-type="btn-view" class="action-btn btn btn-sm btn-outline-primary btn-fw me-2" title="Chi tiết">
								<i class="fa fa-eye btn-icon-prepend"></i>
							</button>
						</div>
						<div class="action-mobile">
							<div class="dropdown inline-block">
								<button type="button" class="btn btn-light dropdown-toggle" id="dropdownMenuIconButton4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuIconButton4">
									<button type="button" class="action-btn dropdown-item" action-type="btn-view"><i class="fa fa-eye btn-icon-prepend"></i> Chi tiết</button>
								</div>
							</div>
						</div>`;
				}
			}
			, {
				"title": "Trạng thái"
				, "data": "TRANG_THAI_GIAO_DICH"
				, "visible": true
				, "className": "text-center"
				, "searchable": false
				, "orderable": false
				, "width": "150px"
				, "render": function (data, type, row) {
					return renderOrderStatusBadge(data, row.TRANG_THAI_GIAO_DICH_TEXT);
				}
			}
			, {
				"title": "Mã đơn"
				, "data": "ID"
				, "visible": true
				, "className": "text-center"
				, "searchable": true
				, "orderable": false
				, "width": "100px"
				, "render": function (data, type, row) {
					let pathUrl = '{{ url("/admin/don-hang/chi-tiet") }}/dh-' + data;
					let code = !isEmpty(row.MA_DON) ? row.MA_DON : ('#' + data);
					return '<a class="text-decoration-none fw-bold" href="' + pathUrl + '">' + code + '</a>';
				}
			}
			, {
				"title": "Khách hàng"
				, "data": "HO_TEN"
				, "visible": true
				, "className": "text-left text-wrap-auto"
				, "searchable": true
				, "orderable": false
				, "width": "180px"
				, "render": function (data) {
					if (isEmpty(data)) return '';
					return '<div style="max-height: 4.5em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.5em;" title="' + data + '">' + data + '</div>';
				}
			}
			, {
				"title": "SĐT"
				, "data": "SO_DIEN_THOAI"
				, "visible": true
				, "className": "text-center text-nowrap"
				, "searchable": true
				, "orderable": false
				, "width": "140px"
				, "render": function (data) {
					if (isEmpty(data)) return '';
					var display = formatPhoneNumber(String(data));
					var digits = String(data).replace(/\D/g, '');
					return '<a class="text-decoration-none" href="tel:' + digits + '" title="' + digits + '">' + display + '</a>';
				}
			}
			, {
				"title": "Tổng tiền"
				, "data": "TONG_TIEN"
				, "visible": true
				, "className": "text-right"
				, "searchable": false
				, "orderable": false
				, "width": "140px"
				, "render": function (data) {
					return '<span class="fw-bold text-primary">' + formatMoneyVnd(data) + '</span>';
				}
			}
			, {
				"title": "Ngày tạo"
				, "data": "NGAY_TAO"
				, "visible": true
				, "className": "text-center"
				, "searchable": false
				, "orderable": false
				, "width": "160px"
				, "defaultContent": ""
			}
			, {
				"title": "Email"
				, "data": "EMAIL"
				, "visible": true
				, "className": "text-left text-wrap-auto"
				, "searchable": true
				, "orderable": false
				, "width": "180px"
				, "render": function (data) {
					if (isEmpty(data)) return '';
					return '<div style="max-height: 3em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.5em;" title="' + data + '">' + data + '</div>';
				}
			}
		]
		, "columnDefs": [
			{ "targets": '_all' }
			, { "targets": 0 }
		]
		, "aLengthMenu": [
			[10, 20, 50, -1]
			, [10, 20, 50, "Tất cả"]
		]
		, "iDisplayLength": 10
		, "language": {
			"search": "Tìm kiếm"
			, "searchPlaceholder": 'Nhập từ khóa...'
			, "info": "<p class=\"card-description\">Hiển thị _START_ đến _END_. <span class=\"one-line\">Tổng số <code>_TOTAL_ kết quả</code> </span></p>"
			, "emptyTable": '<h6 class="block text-center"><i class="fa fa-folder-open-o" style="color: #6c7293;"></i> {{ __("Không có dữ liệu") }}</h6>'
			, "zeroRecords": '<h6 class="block text-center"><i class="fa fa-folder-open-o" style="color: #6c7293;"></i> {{ __("Không có dữ liệu") }}</h6>'
			, "infoEmpty": "<p class=\"card-description\"><span class=\"one-line\">Không có dữ liệu</span></p>"
			, "infoFiltered": ""
			, "lengthMenu": "Hiển thị &nbsp; _MENU_"
			, "paginate": {}
		}
		, "dom": 'Bflrtip'
		, "buttons": [
			{
				attr: {
					title: 'Change views',
					id: 'card-toggle'
				}
				, text: 'Hiển thị kiểu <i class="fa fa-id-badge fa-fw fa-lg" aria-hidden="true"></i>'
				, className: 'animated bounce d-none'
				, action: function () {}
			}
		]
		, infoCallback: function (settings, start, end, max, total, pre) {
			return pre;
		}
		, "drawCallback": function (settings) {
			var api = this.api();
			api.rows().every(function (rowIdx) {
				this.cell(rowIdx, 0).data(rowIdx + 1);
			});

			const dropdownsOnTable = $("#tableDonHang .dropdown-toggle");
			dropdownsOnTable.each((index, dropdownToggleEl) => {
				dropdownToggleEl.addEventListener("show.bs.dropdown", function (event) {
					$(event.target).closest("td").addClass("z-index-3");
				});
				dropdownToggleEl.addEventListener("hide.bs.dropdown", function (event) {
					$(event.target).closest("td").removeClass("z-index-3");
				});
			});

			const pageInfo = $('#tableDonHang').DataTable().page.info();
			if (settings.iDraw == 2) {
				let startPageFrUrl = window.wwAdminListUrl ? window.wwAdminListUrl.pageIndex(10) : 0;
				if (Number(pageInfo.pages) >= Number(startPageFrUrl)) {
					settings.IS_CALL_AJAX = false;
					$('#tableDonHang').DataTable().page(startPageFrUrl).draw(false);
				}
			}

			$('#tableDonHang').DataTable().columns.adjust();
		}
		, "initComplete": function () {
			if (window.matchMedia('(max-width: 575px)').matches) {
				$('#tableDonHang').DataTable().columns([0]).visible(false, true);
			}
			$("#tableDonHang").wrap("<div style='overflow:auto; width: 100%; position:relative; max-height: 70vh;'></div>");
		}
	});

	dataTableDonHang.on('page', function () {
		let pageInfo = dataTableDonHang.page.info();
		let page = pageInfo.page + 1;
		let length = pageInfo.length > 0 ? pageInfo.length : 'all';
		if (page > 0 && window.wwAdminListUrl) {
			window.wwAdminListUrl.sync(page, length);
		}
	}).on('length.dt', function (e, settings, len) {
		if (settings.iDraw !== 1) {
			settings.IS_CALL_AJAX = false;
			dataTableDonHang.page('first').draw(false);
			if (window.wwAdminListUrl) {
				window.wwAdminListUrl.sync(1, len);
			}
		}
	}).on('preDraw', function (e, settings) {
		if (settings.IS_CALL_AJAX === false) {
			settings.IS_CALL_AJAX = true;
			return false;
		}
		return true;
	});

	$('#tableDonHang tbody').on('click', '.action-btn', function (e) {
		e.currentTarget;
		var row = $(this).parents('tr');
		var data = dataTableDonHang.row(row).data();
		let actionType = $(e.currentTarget).attr('action-type') || '';
		switch (actionType) {
			case 'btn-view':
				window.location.assign('{{ url("/admin/don-hang/chi-tiet") }}/dh-' + data.ID);
				break;
		}
	});

	$('#btnSearch').on('click', function () {
		dataTableDonHang.ajax.reload();
	});
	$('#btnReset').on('click', function () {
		$('#SEARCH_TU_KHOA').val('');
		$('#SEARCH_TRANG_THAI').val('all');
		dataTableDonHang.ajax.reload();
	});
	$('#SEARCH_TU_KHOA').on('keypress', function (e) {
		if (e.which === 13) {
			dataTableDonHang.ajax.reload();
		}
	});

	dataTableDonHang.ajax.reload();
});
</script>
@stop
