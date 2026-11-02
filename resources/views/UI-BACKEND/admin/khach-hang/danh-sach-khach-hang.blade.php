@extends('UI-BACKEND.admin.common.layout.master')

@section('plugin-css-for-this-page')
@stop

@section('custom-css')
<style>
</style>
@stop

@section('nav-item')
<li class="nav-item">
	<div class="d-flex align-items-baseline">
		<p class="mb-0">Admin</p>
		<i class="typcn typcn-chevron-right"></i>
		<p class="mb-0">Khách hàng</p>
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
									<span style="font-weight: 500; color: black; font-size: 16px;">TÌM KIẾM</span>
								</a>
							</div>
						</div>
					</h5>
				</div>
				<div id="collapse-1" class="border-bottom collapse show" aria-labelledby="heading-1">
					<div class="card-body">
						<div class="row">
							<div class="col-xl-6 col-md-12 col-sm-12">
								<div class="form-group">
									<label for="SEARCH_TU_KHOA">Từ khóa</label>
									<input type="text" class="form-control" id="SEARCH_TU_KHOA" placeholder="Tên, số điện thoại hoặc email">
								</div>
							</div>
							<div class="col-12 d-flex justify-content-end">
								<button type="button" id="btnReset" style="margin-right: 0.5rem !important;" class="btn btn-action btn-light btn-icon-text me-1 my-2">
									<i class="fa fa-refresh btn-icon-prepend"></i>Làm mới
								</button>
								<button type="button" id="btnSearch" class="btn btn-action btn-info btn-icon-text my-2">
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
							DANH SÁCH <span class="one-line">KHÁCH HÀNG</span>
						</h4>
					</div>
				</div>

				<div class="table-responsive data-tables" style="margin-top: 10px;">
					<table id="tableKhachHang" class="table table-striped table-bordered" width="100%">
						<thead>
							<tr>
								<th>STT</th>
								<th>Thao tác</th>
								<th>Khách hàng</th>
								<th>SĐT</th>
								<th>Email</th>
								<th>Số đơn</th>
								<th>Tổng chi tiêu</th>
								<th>Đơn gần nhất</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th class="header-footer">STT</th>
								<th class="header-footer">Thao tác</th>
								<th class="header-footer">Khách hàng</th>
								<th class="header-footer">SĐT</th>
								<th class="header-footer">Email</th>
								<th class="header-footer">Số đơn</th>
								<th class="header-footer">Tổng chi tiêu</th>
								<th class="header-footer">Đơn gần nhất</th>
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
var dataTableKhachHang;

function customerDetailUrl(id) {
	return '{{ url("/admin/khach-hang/chi-tiet") }}/' + encodeURIComponent(id || '');
}

$(document).ready(function () {
	dataTableKhachHang = new DataTable('#tableKhachHang', {
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
			, url: '{{ url("/api/customer/list") }}'
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
				return dataInput;
			}
			, dataSrc: function (json) {
				json.recordsTotal = 0;
				json.recordsFiltered = 0;

				if (json.STATUS === true) {
					var block = json.DATAS && json.DATAS.CUSTOMER ? json.DATAS.CUSTOMER : null;
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
							, HO_TEN: !isEmpty(item.HO_TEN) ? item.HO_TEN : ''
							, SO_DIEN_THOAI: !isEmpty(item.SO_DIEN_THOAI) ? item.SO_DIEN_THOAI : ''
							, EMAIL: !isEmpty(item.EMAIL) ? item.EMAIL : ''
							, SO_DON: item.SO_DON || 0
							, TONG_CHI_TIEU: item.TONG_CHI_TIEU || 0
							, DON_GAN_NHAT: !isEmpty(item.DON_GAN_NHAT) ? item.DON_GAN_NHAT : ''
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
				, "orderable": true
				, "orderData": [0]
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
				, "orderData": []
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
				"title": "Khách hàng"
				, "data": "HO_TEN"
				, "visible": true
				, "className": "text-left text-wrap-auto"
				, "searchable": true
				, "orderable": true
				, "orderData": [2]
				, "width": "220px"
				, "render": function (data, type, row) {
					if (isEmpty(data)) return '';
					return '<a class="text-decoration-none" href="' + customerDetailUrl(row.ID) + '"><span title="' + data + '">' + data + '</span></a>';
				}
			}
			, {
				"title": "SĐT"
				, "data": "SO_DIEN_THOAI"
				, "visible": true
				, "className": "text-center text-nowrap"
				, "searchable": true
				, "orderable": false
				, "orderData": []
				, "width": "140px"
				, "render": function (data) {
					if (isEmpty(data)) return '';
					var display = typeof formatPhoneNumber === 'function' ? formatPhoneNumber(String(data)) : data;
					var digits = String(data).replace(/\D/g, '');
					return '<a class="text-decoration-none" href="tel:' + digits + '" title="' + digits + '">' + display + '</a>';
				}
			}
			, {
				"title": "Email"
				, "data": "EMAIL"
				, "visible": true
				, "className": "text-left text-wrap-auto"
				, "searchable": true
				, "orderable": false
				, "orderData": []
				, "width": "220px"
				, "render": function (data) {
					if (isEmpty(data)) return '';
					return '<div style="max-height: 3em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.5em;" title="' + data + '">' + data + '</div>';
				}
			}
			, {
				"title": "Số đơn"
				, "data": "SO_DON"
				, "visible": true
				, "className": "text-center"
				, "searchable": false
				, "orderable": false
				, "orderData": []
				, "width": "90px"
				, "render": function (data) {
					return '<span class="badge badge-secondary" style="font-size: 15px;">' + Number(data || 0).toLocaleString('vi-VN') + '</span>';
				}
			}
			, {
				"title": "Tổng chi tiêu"
				, "data": "TONG_CHI_TIEU"
				, "visible": true
				, "className": "text-left text-wrap-auto"
				, "searchable": false
				, "orderable": false
				, "orderData": []
				, "width": "180px"
				, "render": function (data) {
					let textValue = typeof formatVNDToText === 'function' ? formatVNDToText(data || 0) : (Number(data || 0).toLocaleString('vi-VN') + ' ₫');
					return '<span class="badge badge-info" style="border: 2px solid #17a2b8; background-color: #17a2b8; color: white; font-size: 15px;">' + textValue + '</span>';
				}
			}
			, {
				"title": "Đơn gần nhất"
				, "data": "DON_GAN_NHAT"
				, "visible": true
				, "className": "text-center"
				, "searchable": false
				, "orderable": false
				, "orderData": []
				, "width": "160px"
				, "defaultContent": ""
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

			const dropdownsOnTable = $("#tableKhachHang .dropdown-toggle");
			dropdownsOnTable.each((index, dropdownToggleEl) => {
				dropdownToggleEl.addEventListener("show.bs.dropdown", function (event) {
					$(event.target).closest("td").addClass("z-index-3");
				});
				dropdownToggleEl.addEventListener("hide.bs.dropdown", function (event) {
					$(event.target).closest("td").removeClass("z-index-3");
				});
			});

			const pageInfo = $('#tableKhachHang').DataTable().page.info();
			if (settings.iDraw == 2) {
				let startPageFrUrl = window.wwAdminListUrl ? window.wwAdminListUrl.pageIndex(10) : 0;
				if (Number(pageInfo.pages) >= Number(startPageFrUrl)) {
					settings.IS_CALL_AJAX = false;
					$('#tableKhachHang').DataTable().page(startPageFrUrl).draw(false);
				}
			}

			$('#tableKhachHang').DataTable().columns.adjust();
		}
		, "initComplete": function () {
			if (window.matchMedia('(max-width: 575px)').matches) {
				$('#tableKhachHang').DataTable().columns([0]).visible(false, true);
			}
			$("#tableKhachHang").wrap("<div style='overflow:auto; width: 100%; position:relative; max-height: 70vh;'></div>");
		}
	});

	dataTableKhachHang.on('page', function () {
		let pageInfo = dataTableKhachHang.page.info();
		let page = pageInfo.page + 1;
		let length = pageInfo.length > 0 ? pageInfo.length : 'all';
		if (page > 0 && window.wwAdminListUrl) {
			window.wwAdminListUrl.sync(page, length);
		}
	}).on('length.dt', function (e, settings, len) {
		if (settings.iDraw !== 1) {
			settings.IS_CALL_AJAX = false;
			dataTableKhachHang.page('first').draw(false);
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

	$('#tableKhachHang tbody').on('click', '.action-btn', function (e) {
		var row = $(this).parents('tr');
		var data = dataTableKhachHang.row(row).data();
		let actionType = $(e.currentTarget).attr('action-type') || '';
		if (actionType === 'btn-view' && data && data.ID) {
			window.location.assign(customerDetailUrl(data.ID));
		}
	});

	$('#btnSearch').on('click', function () {
		dataTableKhachHang.ajax.reload();
	});
	$('#btnReset').on('click', function () {
		$('#SEARCH_TU_KHOA').val('');
		dataTableKhachHang.ajax.reload();
	});
	$('#SEARCH_TU_KHOA').on('keypress', function (e) {
		if (e.which === 13) {
			dataTableKhachHang.ajax.reload();
		}
	});

	dataTableKhachHang.ajax.reload();
});
</script>
@stop
