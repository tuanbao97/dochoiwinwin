<?php
	$uuid1 = 'section' . Str::random(6);
	$uuid4 = 'section' . Str::random(6);
?>

@extends('UI-BACKEND.admin.common.layout.master')

@section('plugin-css-for-this-page')
@stop 

@section('custom-css')
<style>
	@media (min-width: 1500px) {
		.section.col-lg-4-custom {
			flex: 0 0 auto;
			width: 350px;
		}
		.section.col-lg-8-custom {
			flex: 0 0 auto;
			width: calc(100% - 350px);
		}
	}
	@media (min-width: 1250px) and (max-width: 1500px) {
		.section.col-lg-4-custom {
			flex: 0 0 auto;
			width: 300px;
		}
		.section.col-lg-8-custom {
			flex: 0 0 auto;
			width: calc(100% - 300px);
		}
	}
 
</style>
@stop 

@section('nav-item')
<li class="nav-item">
	<div class="d-flex align-items-baseline">
		<p class="mb-0">Admin</p>
		<i class="typcn typcn-chevron-right"></i>
		<p class="mb-0">Danh sách cài đặt</p>
	</div>
</li>
@stop

@section('content')
<div class="row section-main" id="SECTION_MAIN">

	<div class="col-lg-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-8 col-sm-12">
						<h4 class="card-title">
							DANH SÁCH <span class="one-line">LUỢT XEM TRANG WEB. </span>
							<span class="one-line" style="padding-top: 5px; color: #dc3545;">TỔNG LƯỢT XEM: <span id="TONG_SO_LUOT_XEM"></span></span>
						</h4>
					</div>
					<div class="col-md-4 col-sm-12 float-right text-align-right">
						<!-- <a href="{{ url('/admin/danh-muc-san-pham/chi-tiet') }}">
							<button type="button"
								class="btn btn-action btn-success btn-icon-text">
								<i class="fa fa-plus btn-icon-prepend"></i>Thêm mới
							</button>
						</a> -->
					</div>
				</div>

				<div class="table-responsive data-tables" style="margin-top: 10px">
					<table id="tableSettingCountView"
						class="table table-striped table-bordered" width="100%">

						<thead>
                            <tr>
                                <th>STT</th>
								<th>Tên</th>
                                <th>Số lượng</th>
                                <th>Đơn vị</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                		<tfoot>
                            <tr>
                                <th class="header-footer">STT</th>
								<th class="header-footer">Tên</th>
                                <th class="header-footer">Số lượng</th>
                                <th class="header-footer">Đơn vị</th>
                                <th class="header-footer">Hành động</th>
                            </tr>
                        </tfoot>
					</table>
				</div>

				<div class="col-12 d-flex justify-content-end">
					<div class="action-web">
						<button type="button" class="btn btn-action btn-light btn-icon-text" name="BTN_GO_BACK">
							<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
						</button>
					</div>
					
					<div class="action-mobile">
						<button type="button" class="btn btn-action btn-light btn-icon-text" name="BTN_GO_BACK">
							<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
						</button>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="col-lg-12 grid-margin stretch-card" id="SECTION_SETTING_WEB">
		<div class="card">
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-8 col-sm-12">
						<h4 class="card-title">
							CÀI ĐẶT <span class="one-line">THÔNG TIN TRANG WEB</span>
						</h4>
					</div>
					<div class="col-md-4 col-sm-12 float-right text-align-right">
						<!-- <a href="{{ url('/admin/danh-muc-san-pham/chi-tiet') }}">
							<button type="button"
								class="btn btn-action btn-success btn-icon-text">
								<i class="fa fa-plus btn-icon-prepend"></i>Thêm mới
							</button>
						</a> -->
					</div>
				</div>

				<div class="row">
					<div class="section col-lg-12 col-md-12">
						<div class="row">
							<div class="form-group col-md-6 col-sm-12">
								<label for="EDIT_SETTING_TEN_CUA_HANG">Tên cửa hàng<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_TEN_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_TEN_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-6 col-sm-12">
								<label for="EDIT_SETTING_EMAIL">Email cửa hàng<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_EMAIL" type="email" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_EMAIL"></span>
							</div>

							<div class="form-group col-md-6 col-sm-12">
								<label for="EDIT_SETTING_MA_SO_THUE">Mã số thuế</label>
								<input class="form-control" id="EDIT_SETTING_MA_SO_THUE" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_MA_SO_THUE"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_THOI_GIAN_LAM_VIEC">Thời gian làm việc<code>*</code></label>
								<textarea class="form-control" id="EDIT_SETTING_THOI_GIAN_LAM_VIEC" placeholder="" rows="2"></textarea>
								<span class="error-message" id="MSG_EDIT_SETTING_THOI_GIAN_LAM_VIEC"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_MO_TA_CUA_HANG">Mô tả cửa hàng<code>*</code></label>
								<textarea class="form-control" id="EDIT_SETTING_MO_TA_CUA_HANG" rows="5"></textarea>
								<span class="error-message" id="MSG_EDIT_SETTING_MO_TA_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_DIA_CHI_CUA_HANG">Địa chỉ cửa hàng<code>*</code></label>
								<textarea class="form-control" id="EDIT_SETTING_DIA_CHI_CUA_HANG" rows="2"></textarea>
								<span class="error-message" id="MSG_EDIT_SETTING_DIA_CHI_CUA_HANG"></span>
							</div>

							<div class="col-md-12" id="SETTING_HOTLINE_INPUT_ROWS">
							</div>

							<div  class="d-none" id="TEMPLATE_SETTING_HOTLINE_INPUT">
								<div class="row" id="TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_{-{INDEX}-}" data="{-{INDEX}-}">
									<div class="form-group col-md-3 col-sm-6">
										<label for="EDIT_SETTING_LOAI_{-{INDEX}-}">Chọn loại<code>*</code></label>
										<select id="EDIT_SETTING_LOAI_{-{INDEX}-}"
											class="form-control form-select form-control-default border-radius-2px border-top-left-radius-0px border-bottom-left-radius-0px">
											<option disabled selected value>Vui lòng chọn</option>
											<option value="HOTLINE">Hotline</option>
											<option value="KHIEU_NAI_GOP_Y">Khiếu nại, Góp ý</option>
											<option value="TU_VAN">Tư vấn</option>
										</select>

										<span class="error-message" id="MSG_EDIT_SETTING_LOAI_{-{INDEX}-}"></span>
									</div>
									<div class="form-group col-md-3 col-sm-6">
										<label for="EDIT_SETTING_SDT_{-{INDEX}-}">Số điện thoại<code>*</code></label>
										<input class="form-control" id="EDIT_SETTING_SDT_{-{INDEX}-}" placeholder="">
										<span class="error-message" id="MSG_EDIT_SETTING_SDT_{-{INDEX}-}"></span>
									</div>
									<div class="form-group col-md-6 col-sm-6">
										<label for="EDIT_SETTING_TEN_CHU_SDT_{-{INDEX}-}">Tên chủ sđt<code>*</code></label>
										<input class="form-control" id="EDIT_SETTING_TEN_CHU_SDT_{-{INDEX}-}" placeholder="">
										<span class="error-message" id="MSG_EDIT_SETTING_TEN_CHU_SDT_{-{INDEX}-}"></span>
									</div>

									<div class="col" style="justify-content: flex-end; display: flex; align-items: center;">
										<div class="dropdown">
											<button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" id="DROPDOWN_TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_{-{INDEX}-}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
												style="height: 49px;"> Hành động </button>
											<div class="dropdown-menu" id="DROPDOWN_MENU_TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_{-{INDEX}-}" aria-labelledby="DROPDOWN_TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_{-{INDEX}-}" style="">
												<!-- <h6 class="dropdown-header">Settings</h6> -->
												<a class="dropdown-item" id="BTN_DELETE_TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_{-{INDEX}-}" data="{-{INDEX}-}" href="" onclick="return false;">Xóa</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item" id="BTN_MOVE_UP_TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_{-{INDEX}-}" data="{-{INDEX}-}" href="" onclick="return false;">Đi lên</a>
												<a class="dropdown-item" id="BTN_MOVE_DOWN_TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_{-{INDEX}-}" data="{-{INDEX}-}" href="" onclick="return false;">Đi xuống</a>
											</div>
										</div>
									</div>
									
								</div>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<button type="button" data-value="addRow" id="BTN_SETTING_HOTLINE_ADD_ROW" class="btn btn-outline-info btn-add-line-param">
									<i class="fa fa-plus"></i> Thêm SĐT
								</button>
								<div class="col-md-12 mt-2">
									<code id="MSG_SETTING_HOTLINE"></code>
								</div>
							</div>


							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_GG_MAP_CUA_HANG">Đường dẫn nhúng Google Maps<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_GG_MAP_CUA_HANG" placeholder="https://www.google.com/maps?...&output=embed">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_GG_MAP_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG">Link chia sẻ Google Maps<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG" placeholder="https://maps.app.goo.gl/...">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-6 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG">Đường dẫn Trang Zalo cửa hàng<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-6 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_SO_ZALO_CUA_HANG">Đường dẫn Số Zalo cửa hàng<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_SO_ZALO_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_SO_ZALO_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-6 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG">Đường dẫn Trang Facebook cửa hàng<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-6 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG">Đường dẫn Facebook Messenger cửa hàng<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG">Đường dẫn Trang website<code>*</code></label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_TIKTOK_CUA_HANG">Đường dẫn Tiktok cửa hàng</label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_TIKTOK_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_TIKTOK_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="EDIT_SETTING_DUONG_DAN_YOUTUBE_CUA_HANG">Đường dẫn Youtube cửa hàng</label>
								<input class="form-control" id="EDIT_SETTING_DUONG_DAN_YOUTUBE_CUA_HANG" placeholder="">
								<span class="error-message" id="MSG_EDIT_SETTING_DUONG_DAN_YOUTUBE_CUA_HANG"></span>
							</div>


							<div class="form-group col-md-12 col-sm-12">
								<label for="{{ 'section_' . $uuid1 }}_EDIT_SETTING_GIOI_THIEU_CUA_HANG">Giới thiệu cửa hàng<code>*</code></label>
								<textarea rows="5" class="form-control" id="{{ 'section_' . $uuid1 }}_EDIT_SETTING_GIOI_THIEU_CUA_HANG" placeholder=""></textarea>
								<span class="error-message" id="MSG_{{ 'section_' . $uuid1 }}_EDIT_SETTING_GIOI_THIEU_CUA_HANG"></span>
							</div>

							<div class="form-group col-md-12 col-sm-12">
								<label for="{{ 'section_' . $uuid4 }}_EDIT_SETTING_CAM_KET_BAN_HANG">Cam kết bán hàng<code>*</code></label>
								<textarea rows="5" class="form-control" id="{{ 'section_' . $uuid4 }}_EDIT_SETTING_CAM_KET_BAN_HANG" placeholder=""></textarea>
								<span class="error-message" id="MSG_{{ 'section_' . $uuid4 }}_EDIT_SETTING_CAM_KET_BAN_HANG"></span>
							</div>
						</div>
					</div>

					<div class="col-12 d-flex justify-content-end">
						<div class="action-web">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-info btn-icon-text" name="BTN_SAVE_SETTING_WEBSITE">
								<i class="fa fa-save btn-icon-prepend"></i>Lưu
							</button>
						</div>
						
						<div class="action-mobile">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-info btn-icon-text" name="BTN_SAVE_SETTING_WEBSITE">
								<i class="fa fa-save btn-icon-prepend"></i>Lưu
							</button>
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
							IMPORT <span class="one-line">THÔNG TIN THÀNH PHỐ</span>
						</h4>
					</div>
					<div class="col-md-4 col-sm-12 float-right text-align-right">
						<!-- <a href="{{ url('/admin/danh-muc-san-pham/chi-tiet') }}">
							<button type="button"
								class="btn btn-action btn-success btn-icon-text">
								<i class="fa fa-plus btn-icon-prepend"></i>Thêm mới
							</button>
						</a> -->
					</div>
				</div>

				<div class="row">
					<div class="form-group col-md-12 col-sm-12">
						<label for="NAME">Đường dẫn file excel thành phố</label> <br>
						<span class="card-description">Mặc định: không chọn sẽ lấy file từ hệ thống</span>
						<input type="file" class="form-control" id="EDIT_IMPORT_EXCEL_THANH_PHO" placeholder="">
						<span class="error-message" id="MSG_EDIT_IMPORT_EXCEL_THANH_PHO"></span>
					</div>

					<div class="col-12 d-flex justify-content-end">
						<div class="action-web">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-info btn-icon-text" name="BTN_IMPORT_THANH_PHO">
								<i class="fa fa-save btn-icon-prepend"></i>Import
							</button>
						</div>
						
						<div class="action-mobile">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-info btn-icon-text" name="BTN_IMPORT_THANH_PHO">
								<i class="fa fa-save btn-icon-prepend"></i>Import
							</button>
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
							XÓA CACHE <span class="one-line">FRONTEND</span>
						</h4>
					</div>
				</div>

				<div class="row">
					<div class="col-12 d-flex justify-content-end">
						<div class="action-web">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-danger btn-icon-text" name="BTN_XOA_CACHE">
								<i class="fa fa-trash-o btn-icon-prepend"></i>Xóa cache
							</button>
						</div>
						
						<div class="action-mobile">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-danger btn-icon-text" name="BTN_XOA_CACHE">
								<i class="fa fa-trash-o btn-icon-prepend"></i>Xóa cache
							</button>
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
							FETCH SẢN PHẨM <span class="one-line">SAPO</span>
						</h4>
					</div>
				</div>

				<div class="row">
					<div class="col-12 d-flex justify-content-end">
						<div class="action-web">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-warning btn-icon-text me-1" name="BTN_FETCH_SAPO_FULL">
								<i class="fa fa-cloud-download btn-icon-prepend"></i>Fetch full
							</button>

							<button type="button" class="btn btn-action btn-info btn-icon-text" name="BTN_FETCH_SAPO">
								<i class="fa fa-refresh btn-icon-prepend"></i>Fetch Sapo
							</button>
						</div>
						
						<div class="action-mobile">
							<button type="button" class="btn btn-action btn-light btn-icon-text me-1" name="BTN_GO_BACK">
								<i class="fa fa-caret-left btn-icon-prepend"></i>Quay về
							</button>

							<button type="button" class="btn btn-action btn-warning btn-icon-text me-1" name="BTN_FETCH_SAPO_FULL">
								<i class="fa fa-cloud-download btn-icon-prepend"></i>Fetch full
							</button>

							<button type="button" class="btn btn-action btn-info btn-icon-text" name="BTN_FETCH_SAPO">
								<i class="fa fa-refresh btn-icon-prepend"></i>Fetch Sapo
							</button>
						</div>
					</div>

				</div>

				

			</div>
		</div>
	</div>



</div>

<!-- Modal Chi tiết lượt xem -->
<div class="modal fade" id="modalChiTietLuotXem" tabindex="-1" role="dialog" 
     aria-labelledby="modalChiTietLuotXemLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalChiTietLuotXemLabel">CHI TIẾT LƯỢT XEM - <span id="MODAL_TITLE_NGAY"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive data-tables">
          <table id="tableChiTietLuotXem" class="table table-striped table-bordered" width="100%">
            <thead>
              <tr>
                <th>STT</th>
                <th>Thời gian</th>
                <th>IP Ver</th>
                <th>IP</th>
                <th>Thành phố</th>
                <th>ISP</th>
                <th>Thiết bị</th>
                <th>OS</th>
                <th>Trình duyệt</th>
                <th>URL</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th class="header-footer">STT</th>
                <th class="header-footer">Thời gian</th>
                <th class="header-footer">IP Ver</th>
                <th class="header-footer">IP</th>
                <th class="header-footer">Thành phố</th>
                <th class="header-footer">ISP</th>
                <th class="header-footer">Thiết bị</th>
                <th class="header-footer">OS</th>
                <th class="header-footer">Trình duyệt</th>
                <th class="header-footer">URL</th>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Shared Controls Container -->
        <div class="row datatables-controls mt-3">
          <div class="col-sm-12 col-md-5" id="datatables-modal-info-container"></div>
          <div class="col-sm-12 col-md-7" id="datatables-modal-pagination-container"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

@stop

@section('plugin-js-for-this-page')
@stop

@section('custom-js-for-this-page')

<!-- Include setup tinyMCE -->
@include(
	'UI-BACKEND.admin.common.component.editor.editor-noi-dung'
	, [
		'sectionId' => 'section_' . $uuid1
		, 'elementTinyMceId' => 'section_' . $uuid1 . '_' . 'EDIT_SETTING_GIOI_THIEU_CUA_HANG'
	]
)


@include(
	'UI-BACKEND.admin.common.component.editor.editor-noi-dung'
	, [
		'sectionId' => 'section_' . $uuid4
		, 'elementTinyMceId' => 'section_' . $uuid4 . '_' . 'EDIT_SETTING_CAM_KET_BAN_HANG'
	]
)

<script>
$(document).ready(function () {
	/* Xử lý event click btn go back danh sách */
    $('button[name="BTN_GO_BACK"]').on('click', function() {
    	window.location = '{{ url('/admin/san-pham') }}';
	});

	handleTemplateSetting = function(templateId, containerId, btnAddNewRowId, msgId) {

		// New row template input
		newRowTemplateSettingInput = function(index) {
			// Get template input
			let templateSettingInput = $(`#${templateId}`).html();

			// Create new row
			let newRow = templateSettingInput.replaceAll('{-{INDEX}-}', index);

			// Append after div
			$(`#${containerId}`).append(newRow);
		}

		// Get max current index row template input
		function getMaxCurrIndexRowTemplate() {
			var templateSettingInputRow = $(`[id^="${templateId}_ROWS_"]`);
			if (templateSettingInputRow.length == 0) {
				return 1;
			}
			var dataMax = 1;
			for (let i = 0; i < templateSettingInputRow.length; i++) {
				let data = templateSettingInputRow[i].getAttribute('data');

				// Skip row template
				if (data === '{-{INDEX}-}')
					continue;

				data = Number(data); // Ép kiểu về Số
				if (dataMax < data)
					dataMax = data;
			}
			return Number(dataMax);
		}

		// Handle event add new row
		$(`#${btnAddNewRowId}`).click(function() {
			let index = getMaxCurrIndexRowTemplate() + 1;
			newRowTemplateSettingInput(index);

			// Scroll element
			let currentDiv = document.getElementById(`${templateId}_ROWS_` + index);
			scrollElement(index, currentDiv, null);
		});

		// Handle event click button in divParameter (for btnDelete, btnMoveUp, btnMoveDown)
		// Cai nay dung cho phan tu sau khi append
		$(`#${containerId}`).on('click', 'a', function(e){
			var btnId = e.currentTarget.id || '';
			if (!btnId) return;

			// Btn delete
			if (btnId.toLowerCase().includes(`BTN_DELETE_${templateId}`.toLowerCase())) {
				handleDeleteInput($(e.currentTarget));
			}

			// Btn move down
			if (btnId.toLowerCase().includes(`BTN_MOVE_DOWN_${templateId}`.toLowerCase())) {
				handleMoveDownInput($(e.currentTarget));
			}

			// Btn move up
			if (btnId.toLowerCase().includes(`BTN_MOVE_UP_${templateId}`.toLowerCase())) {
				handleMoveUpInput($(e.currentTarget));
			}
		});

		// Scroll element
		function scrollElement(index, currentDiv, parentDiv) {
			scrollSmothIntoElement(currentDiv.id, parentDiv);

			// Focus element border
			$("#EDIT_SETTING_LOAI_" + index).attr('style', 'border-color: red !important');
			$("#EDIT_SETTING_SDT_" + index).attr('style', 'border-color: red !important');
			$("#EDIT_SETTING_TEN_CHU_SDT_" + index).attr('style', 'border-color: red !important');

			// Remove focus element after 0.7s
			setTimeout(function() {
				$("#EDIT_SETTING_LOAI_" + index).css('border-color', '');
				$("#EDIT_SETTING_SDT_" + index).css('border-color', '');
				$("#EDIT_SETTING_TEN_CHU_SDT_" + index).css('border-color', '');
			}, 700);
		}

		// Scroll smooth into element
		function scrollSmothIntoElement(elementId, parentDiv) {
			if(!elementId) {
				return;
			}
			let id = elementId;
			let yOffset = -120; 
			let element = document.getElementById(id);
			let y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
			
			if (parentDiv) {
				parentDiv.scrollTo({top: (element.offsetTop), behavior: 'smooth'});
			} else {
				window.scrollTo({top: y, behavior: 'smooth'});
			}
		}

		
		// Handle move down hotline input
		function handleMoveUpInput(elementBtnMoveUp) {
			let data = elementBtnMoveUp.attr('data');
			let currentDiv = document.getElementById(`${templateId}_ROWS_` + data);
			let previousDiv = currentDiv.previousElementSibling;

			if (!previousDiv) {
				showToastWarning('top-right', 'Hiện đã là vị trí đầu tiên');
				return;
			}

			// Lưu value của input, textarea, select
			const getValues = (div) => {
				let map = {};
				div.querySelectorAll('input, textarea, select').forEach(el => {
					map[el.id] = el.value;
				});
				return map;
			};

			const setValues = (div, values) => {
				div.querySelectorAll('input, textarea, select').forEach(el => {
					if (values.hasOwnProperty(el.id)) {
						el.value = values[el.id];
					}
				});
			};

			const currentValues = getValues(currentDiv);
			const previousValues = getValues(previousDiv);

			// Đảo vị trí trong DOM
			const parent = currentDiv.parentNode;
			parent.insertBefore(currentDiv, previousDiv); // current lên trên previous

			// Lấy lại DOM mới sau khi hoán đổi
			const updatedPreviousDiv = document.getElementById(`${templateId}_ROWS_` + data);
			const updatedCurrentDiv = updatedPreviousDiv.nextElementSibling;

			// Set lại dữ liệu
			setValues(updatedPreviousDiv, previousValues);
			setValues(updatedCurrentDiv, currentValues);

			// Ẩn dropdown nếu có
			$(`#DROPDOWN_${templateId}_ROWS_` + data).removeClass('show');
			$(`#DROPDOWN_${templateId}_ROWS_` + data).attr('aria-expanded', 'false');
			$(`#DROPDOWN_MENU_${templateId}_ROWS_` + data).removeClass('show');

			// Scroll
			scrollElement(data, updatedPreviousDiv, null);
		}

		
		function handleMoveDownInput(elementBtnMoveDown) {
			let data = elementBtnMoveDown.attr('data');
			let currentDiv = document.getElementById(`${templateId}_ROWS_` + data);
			let nextDiv = currentDiv.nextElementSibling;

			if (!nextDiv) {
				showToastWarning('top-right', 'Hiện đã là vị trí cuối cùng');
				return;
			}

			// Lưu value của input, textarea, select
			const getValues = (div) => {
				let map = {};
				div.querySelectorAll('input, textarea, select').forEach(el => {
					map[el.id] = el.value;
				});
				return map;
			};

			const setValues = (div, values) => {
				if (!div) return;
				div.querySelectorAll('input, textarea, select').forEach(el => {
					if (values.hasOwnProperty(el.id)) {
						el.value = values[el.id];
					}
				});
			};

			const currentValues = getValues(currentDiv);
			const nextValues = getValues(nextDiv);

			// Swap vị trí: insert current sau next
			const parent = currentDiv.parentNode;
			parent.insertBefore(nextDiv, currentDiv); // next nằm trước current => xem như swap

			// Gán lại giá trị
			setValues(nextDiv, currentValues);
			setValues(currentDiv, nextValues);

			// Ẩn dropdown nếu có
			$(`#DROPDOWN_${templateId}_ROWS_` + data).removeClass('show');
			$(`#DROPDOWN_${templateId}_ROWS_` + data).attr('aria-expanded', 'false');
			$(`#DROPDOWN_MENU_${templateId}_ROWS_` + data).removeClass('show');

			// Cuộn
			scrollElement(data, currentDiv, null);
		}




		// Handle delete hotline input
		function handleDeleteInput(elementBtnDel) {
			let data = elementBtnDel.attr('data');
			
			showSwalWarningPopup(function callback(result) {
				if (result.isConfirmed === true) {
					$(`#${templateId}_ROWS_` + data).remove();
				} else if (result.isDismissed === true) {
				} else if (result.isDenied === true) {

				}
			}, 'Bạn có muốn xóa <span style="display: inline-block;">dòng này không?</span>');

		}

		validateHotlineInput = function() {
			// Empty all msg
			$('[id^=MSG_EDIT_SETTING_]').text('');
			$('[id^=MSG_EDIT_SETTING_]').text('');
			$(`#${msgId}`).text('');


			// Get array element by regex begin id
			let templateHotlineInputRow = $(`[id^="${templateId}_ROWS_"]`);
			if (templateHotlineInputRow.length === 1) {
				isValid = false;
				$(`#${msgId}`).text('Số điện thoại là bắt buộc.');
			}
			for (let i = 0; i < templateHotlineInputRow.length; i++) {
				let data = templateHotlineInputRow[i].getAttribute('data');

				// Skip row template
				if (data === '{-{INDEX}-}') continue;
				let settingLoaiInputValue = !isEmpty($('#EDIT_SETTING_LOAI_' + data).val()) ? $('#EDIT_SETTING_LOAI_' + data).val(): '';
				let settingHotlineInputValue = !isEmpty($('#EDIT_SETTING_SDT_' + data).val()) ? $('#EDIT_SETTING_SDT_' + data).val(): '';
				let settingTenChuSdtInputValue = !isEmpty($('#EDIT_SETTING_TEN_CHU_SDT_' + data).val()) ? $('#EDIT_SETTING_TEN_CHU_SDT_' + data).val() : '';

				// Validate
				if (settingLoaiInputValue === '') {
					isValid = false;
					$('#MSG_EDIT_SETTING_LOAI_' + data).text('Dữ liệu này không được để trống.');
				}
				if (settingHotlineInputValue === '') {
					isValid = false;
					$('#MSG_EDIT_SETTING_SDT_' + data).text('Dữ liệu này không được để trống.');
				}
				if (settingTenChuSdtInputValue === '') {
					isValid = false;
					$('#MSG_EDIT_SETTING_TEN_CHU_SDT_' + data).text('Dữ liệu này không được để trống.');
				}
			}
		}
	}
	handleTemplateSetting('TEMPLATE_SETTING_HOTLINE_INPUT', 'SETTING_HOTLINE_INPUT_ROWS', 'BTN_SETTING_HOTLINE_ADD_ROW', 'MSG_SETTING_HOTLINE');


	/* Xử lý set input data để lưu */
	setInputDataSettingWeb = function() {
    	// Create object data
    	var data = {
			SETTING_TEN_CUA_HANG: !isEmpty($('#EDIT_SETTING_TEN_CUA_HANG').val()) ? $('#EDIT_SETTING_TEN_CUA_HANG').val() : null,
			SETTING_EMAIL: !isEmpty($('#EDIT_SETTING_EMAIL').val()) ? $('#EDIT_SETTING_EMAIL').val() : null,
			SETTING_MA_SO_THUE: !isEmpty($('#EDIT_SETTING_MA_SO_THUE').val()) ? $('#EDIT_SETTING_MA_SO_THUE').val() : null,
			SETTING_THOI_GIAN_LAM_VIEC: !isEmpty($('#EDIT_SETTING_THOI_GIAN_LAM_VIEC').val()) ? $('#EDIT_SETTING_THOI_GIAN_LAM_VIEC').val() : null,
			SETTING_MO_TA_CUA_HANG: !isEmpty($('#EDIT_SETTING_MO_TA_CUA_HANG').val()) ? $('#EDIT_SETTING_MO_TA_CUA_HANG').val() : null,
			SETTING_DIA_CHI_CUA_HANG: !isEmpty($('#EDIT_SETTING_DIA_CHI_CUA_HANG').val()) ? $('#EDIT_SETTING_DIA_CHI_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_GG_MAP_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_GG_MAP_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_GG_MAP_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_SO_ZALO_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_SO_ZALO_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_SO_ZALO_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_TIKTOK_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_TIKTOK_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_TIKTOK_CUA_HANG').val() : null,
			SETTING_DUONG_DAN_YOUTUBE_CUA_HANG: !isEmpty($('#EDIT_SETTING_DUONG_DAN_YOUTUBE_CUA_HANG').val()) ? $('#EDIT_SETTING_DUONG_DAN_YOUTUBE_CUA_HANG').val() : null,
			SETTING_GIOI_THIEU_CUA_HANG: getEditorContent('{{ 'section_' . $uuid1 }}_EDIT_SETTING_GIOI_THIEU_CUA_HANG'),
			SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT: getEditorContentOnlyText('{{ 'section_' . $uuid1 }}_EDIT_SETTING_GIOI_THIEU_CUA_HANG'),
			SETTING_CAM_KET_BAN_HANG: getEditorContent('{{ 'section_' . $uuid4 }}_EDIT_SETTING_CAM_KET_BAN_HANG'),
			SETTING_CAM_KET_BAN_HANG_ONLY_TEXT: getEditorContentOnlyText('{{ 'section_' . $uuid4 }}_EDIT_SETTING_CAM_KET_BAN_HANG'),
		};


		// Get array element by regex begin id
		let danhSachHotline = [];
		let templateHotlineInputRow = $('[id^="TEMPLATE_SETTING_HOTLINE_INPUT_ROWS_"]');
		for (let i = 0; i < templateHotlineInputRow.length; i++) {
			let data = templateHotlineInputRow[i].getAttribute('data');

			// Skip row template
			if (data === '{-{INDEX}-}') continue;
			let settingLoaiIndex = !isEmpty($('#EDIT_SETTING_LOAI_' + data).val()) ? $('#EDIT_SETTING_LOAI_' + data).val() : null;
			let settingSdtIndex =  !isEmpty($('#EDIT_SETTING_SDT_' + data).val()) ? $('#EDIT_SETTING_SDT_' + data).val() : null;
			let settingTenChuSdtIndex = !isEmpty($('#EDIT_SETTING_TEN_CHU_SDT_' + data).val()) ? $('#EDIT_SETTING_TEN_CHU_SDT_' + data).val() : null;

			danhSachHotline.push({
				'LOAI' : settingLoaiIndex,
				'SDT' : settingSdtIndex,
				'TEN_CHU_SDT': settingTenChuSdtIndex
			});
		}
		data['SETTING_DANH_SACH_HOTLINE'] = danhSachHotline;
  
		return data;
    }

	/* Xử lý event lưu */
	$('button[name="BTN_SAVE_SETTING_WEBSITE"]').on('click', function() {
		// Reset all error msg
    	resetAllErrorMsg('SECTION_SETTING_WEB');

		let data = setInputDataSettingWeb();
		$.ajax({
    		type: "POST", 
    		url: '{{ url("/api/setting/web/save") }}', 
    		contentType: "application/json",
			showLoading: true,
    		data: JSON.stringify(data), 
    		success: function(data, textStatus, request) {
    			// Ajax call completed successfully 
    			showToastSuccess('top-right', data.STATUS_DETAIL);

    		}, 
    		error: function(request, textStatus, errorThrown) {
				if (request.status === 401 || request.status === 403) {
					return;
				}
				try {
					request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
					showToastFailure('top-right', request.responseJSON && !isEmpty(request.responseJSON.STATUS_DETAIL) ? request.responseJSON.STATUS_DETAIL : 'Internal server');

					// Set error msg
					setErrorMsg(request, 'EDIT');
					
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
								case 'SETTING_GIOI_THIEU_CUA_HANG':
									$("#MSG_{{ 'section_' . $uuid1 }}_EDIT_SETTING_GIOI_THIEU_CUA_HANG").text(errorMsg);
									break;
								case 'SETTING_CAM_KET_BAN_HANG':
									$("#MSG_{{ 'section_' . $uuid4 }}_EDIT_SETTING_CAM_KET_BAN_HANG").text(errorMsg);
									break;
								case 'SETTING_DANH_SACH_HOTLINE':
									$('#MSG_SETTING_HOTLINE').text('Không được để trống trong phần này.');
									break;
								default:
									// Regex match với định dạng: SETTING_DANH_SACH_HOTLINE.{index}.{field}
									const hotlineMatch = key.match(/^SETTING_DANH_SACH_HOTLINE\.(\d+)\.(\w+)$/);
									if (hotlineMatch) {
										$('#MSG_SETTING_HOTLINE').text('Không được để trống trong phần này.');
									}

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
					// Scroll đến msg lỗi
					scrollMsgInSection($('#SECTION_SETTING_WEB'));
				}
    		},
    		complete: function() {
    		}
    	});

	});

	dataTableSettingCountView = new DataTable('#tableSettingCountView', {
		"fixedHeader": { // Fix header cố định, khi scroll thì nó sẽ kéo thả theo cố định 1 vị trí
			"header": false,
			"footer": false
		}

		, "scrollX": false // Cho phép scroll ngang hay không
		, "scrollY": "" // Chiều cao height bao nhiêu pixel
		, "scrollCollapse": false // Khi define scrollY 1 chiều cao cố định thì scrollCollapse sẽ tự động fit theo dữ liệu dataset hiện có của datatables. Mà không làm có đoạn margin hard code height scrollY
		, "fixedColumns": {
			"start": 0, // Default: 0
			"end": 0 // Default: 0
		}
		
		, "responsive": false
		, "autoWidth": false

		, "paging": true // Có phân trang hay không
		, "pagingType": "numbers" // Kiểu phân trang: numbers - simple_numbers -  full_numbers 
		, "searching": true // Có cho phép searching hay không

		, "processing": false // Hiển thị box loading khi retrieve api ajax hay không
		, "serverSide": false // Xử lý render ở server-side. Khi retrieve API thì mặc định đã có chữ loading bên trong table rồi
		, "deferLoading": false // Tắt tự động tải dữ liệu. Ngăn DataTables gọi API lần đầu tiên.

		, "columns": [
				{
					"title": "STT"
					, "data": "STT" // Dữ liệu lấy từ thuộc tính này
					
					, "visible": true // Có cho phép hiển thị column này hay không
					, "className": "text-center td-stt" // Class name cho column này
					, "searchable": true // Có cho phép search column này hay không
					, "orderable": true // Có cho phép sort column này hay không
					, "orderData": [0] // Sắp xếp theo dữ liệu cột index nào
					, "width": "70px"

					, "render": function (data, type, row, meta) {
						let categoryPId = !isEmpty(row.ID) ? row.ID : '';
						let html = '<span data-id="' + categoryPId + '" >' + data + '</span>';
						return html;
					}
				}
				, { 
					"title": "Tên"
					, "data": "TEN" // Dữ liệu lấy từ thuộc tính này
					
					, "visible": true // Có cho phép hiển thị column này hay không
					, "className": "text-left text-wrap-auto" // Class name cho column này
					, "searchable": true // Có cho phép search column này hay không
					, "orderable": true // Có cho phép sort column này hay không
					, "orderData": [1] // Sắp xếp theo dữ liệu cột index nào
					, "width": "250px"

					, "render": function (data, type, row, meta) {
						return isEmpty(data) ? null : data;
					}
				}
				, { 
					"title": "Số lượng"
					, "data": "GIA_TRI" // Dữ liệu lấy từ thuộc tính này
					
					, "visible": true // Có cho phép hiển thị column này hay không
					, "className": "text-left text-wrap-auto" // Class name cho column này
					, "searchable": true // Có cho phép search column này hay không
					, "orderable": true // Có cho phép sort column này hay không
					, "orderData": [2] // Sắp xếp theo dữ liệu cột index nào
					, "width": "150px"

					, "render": function (data, type, row, meta) {
						return isEmpty(data) ? null : data;
					}
				}
				, { 
					"title": "Đơn vị"
					, "data": "DON_VI" // Dữ liệu lấy từ thuộc tính này
					
					, "visible": true // Có cho phép hiển thị column này hay không
					, "className": "text-left text-wrap-auto" // Class name cho column này
					, "searchable": true // Có cho phép search column này hay không
					, "orderable": true // Có cho phép sort column này hay không
					, "orderData": [3] // Sắp xếp theo dữ liệu cột index nào
					, "width": "150px"

					, "render": function (data, type, row, meta) {
						return isEmpty(data) ? null : data;
					}
				}
				, { 
					"title": "Hành động"
					, "data": "CODE" // Dữ liệu lấy từ thuộc tính này
					
					, "visible": true // Có cho phép hiển thị column này hay không
					, "className": "text-center" // Class name cho column này
					, "searchable": false // Có cho phép search column này hay không
					, "orderable": false // Có cho phép sort column này hay không
					, "width": "160px"

					, "render": function (data, type, row, meta) {
						let code = !isEmpty(row.CODE) ? row.CODE : '';
						let name = !isEmpty(row.TEN) ? row.TEN : '';
						
						let html = `
							<button type="button" 
								class="btn btn-sm btn-info btn-view-detail" 
								data-code="${code}"
								data-name="${name}"
								title="Xem chi tiết">
								<i class="fa fa-eye"></i> Xem chi tiết
							</button>
						`;
						return html;
					}
				}
				
		]
		, "columnDefs": [ // Column defines
			// Cho tất cả các column
			{ 
				"targets": '_all'
				// , ...
			}
			, {
				"targets": 0, // Column index bắt đầu từ 0
				// , ...
			}
		]
		/* Set số lượng hiển thị */
		, "aLengthMenu": [
			  [10, 20, 50, -1]
			, [10, 20, 50, "Tất cả"]
		]
		/* Set số lượng hiển thị mặc định */
		, "iDisplayLength": 10
		, "language": {
			/* Đổi label search */
			"search": "Tìm kiếm"
			/* Set placeholder textbox search */
			, "searchPlaceholder": 'Nhập từ khóa...'
			/* Đổi label hiển thị kết quả */
			, "info" : "<p class=\"card-description\">Hiển thị _START_ đến _END_. <span class=\"one-line\">Tổng số <code>_TOTAL_ kết quả</code> </span></p>"// text you want show for info section
			, "emptyTable": '<h6 class="block text-center"><i class="fa fa-folder-open-o" style="color: #6c7293;"></i> {{ __("Không có dữ liệu") }}</h6>'
			/* Đổi label khi tìm kiếm không có record nào */
			, "zeroRecords": '<h6 class="block text-center"><i class="fa fa-folder-open-o" style="color: #6c7293;"></i> {{ __("Không có dữ liệu") }}</h6>'
			/* Đổi label khi filter không có kết quả nào */
			, "infoEmpty": "<p class=\"card-description\"><span class=\"one-line\">Không có dữ liệu</span></p>"
			, "infoFiltered": ""
			/* Đổi label hiển thị số dòng */
			, "lengthMenu": "Hiển thị &nbsp; _MENU_"
			/* Đổi label phân trang */
			, "paginate": {
				/* "first":      "Đầu tiên"
				, "last":       "Cuối cùng"
				, "next":       "Sau"
				, "previous":   "Trước" */
			},
			
		}
		/* Dom set phần hiển thị chọn số lượng, ô tìm kiếm và phân trang */
		, "dom": 'Bflrtip'
		, "buttons": [
			// Button hiển thị danh sách theo CARD hoặc LIST
			{
				attr:  {
					title: 'Change views',
					id: 'card-toggle'
				},
				text: 'Hiển thị kiểu <i class="fa fa-id-badge fa-fw fa-lg" aria-hidden="true"></i>',
				className: 'animated bounce d-none',
				action: function () {
					alert('Submit');
				}
			}
		]
		/* [Quan trọng] đây là hàm draw callback của datatables */
		, "drawCallback": function(settings) {
			var api = this.api();

			// Cập nhật số thứ tự
			api.rows().every(function (rowIdx, tableLoop, rowLoop) {
				// Cập nhật số thứ tự
				let indexRowSTT = 0;
				this.cell(rowIdx, indexRowSTT).data(rowIdx + 1);
			});
			
			// START Xử lý z-index cho các button dropdown trên datatables
			const dropdownsOnTable = $("#tableSettingCountView .dropdown-toggle");
			dropdownsOnTable.each((index, dropdownToggleEl) => {

				// Listen Xử lý sự kiện vào trình kích hoạt thả xuống
				dropdownToggleEl.addEventListener("show.bs.dropdown", function (event) {
					$(event.target).closest("td").addClass("z-index-3");
				});
				dropdownToggleEl.addEventListener("hide.bs.dropdown", function (event) {
					$(event.target).closest("td").removeClass("z-index-3");
				});
			});
			// END Xử lý z-index cho các button dropdown trên datatables

			// Xử lý hiển thị default ảnh thumnail khi bị lỗi 404 không tìm thấy
			const images = document.querySelectorAll("#tableSettingCountView .td-img-thumnail > img");
			images.forEach((image) => {
				$(image).on("error", function(e) {
					image.src = "{{asset('image/UI-BACKEND/default-no-image.jpg') }}";
				});
			});

			// Điều chỉnh lại kích thước cột
			$('#tableSettingCountView').DataTable().columns.adjust();
			
		}
		/* Sau khi hoàn thành khởi tạo Datatables */
		,"initComplete": function (settings, json) {
			if (window.matchMedia('(max-width: 575px)').matches) { // Ẩn column nào khi là kích thước Mobile
				$('#tableSettingCountView').DataTable().columns([0]).visible(false, true);
			}
			
			// Khởi tạo scroll ngang và dọc cho datatables
			$("#tableSettingCountView").wrap("<div style='overflow:auto; width: 100%; position:relative; max-height: 60vh;'></div>");
		}
	});

	// Listen event Datatables 
	dataTableSettingCountView.on('page', function (e, settings) { // Event thay đổi page
		// Get thông tin page info
		let pageInfo = dataTableSettingCountView.page.info();

		// Đồng bộ page/length lên URL dạng path
		let page = pageInfo.page + 1; // DataTables sử dụng index 0 cho trang, +1 để phù hợp với hiển thị của người dùng
		let length =  pageInfo.length > 0 ? pageInfo.length : 'all';
		if (page > 0 && window.wwAdminListUrl) {
			window.wwAdminListUrl.sync(page, length);
		}
	}).on('length.dt', function(e, settings, len) { // Event thay đổi 
		if (settings.iDraw !== 1) {
			if (window.wwAdminListUrl) {
				window.wwAdminListUrl.sync(1, len);
			}
		}
	}).on('preDraw', function(e, settings) { // Trước khi draw bảng. return TRUE -> tiếp tục. FALSE -> dừng không vẽ
		return true;

	}).on('draw', function(e, settings) { // Event draw bảng
	});

	fnSearchSettingCountView = function() {
    	// Create object data to check
		$.ajax({
			type : "GET"
			, url : '{{ url("/api/setting/list") }}'
			, contentType : "application/json"
			, cache: false // Đảm bảo không cache dữ liệu
			, traditional: false // Bắt buộc đặt là false
			, showLoading: false
			, data : (function() { // IIFE 
				let dataInput = {};

				/* 
				dataInput.PAGE =  1 || null;
				dataInput.PER_PAGE = 2147483647 || null; 
				*/
				dataInput.IS_GET_ALL_ELEMENTS = true;
				dataInput.TYPE = 'SETTING_COUNT_VIEW_DAY';
			
				return dataInput; // Trả về object input data
			})()
			, success : function(data, textStatus, request) {
				/* Start render table */
				var dataSet = [];
				var result = data.DATAS.SETTING.DATA;
				var resultData = !isEmpty(result) ? result : [];
				// Looping
				for (let i = 0; i < resultData.length; i++) {
					let objTmp = {};
					objTmp.STT = i;
					objTmp.FULL_DU_LIEU = resultData[i];
					objTmp.CODE = !isEmpty(resultData[i]['CODE']) ? resultData[i]['CODE'] : null;
					objTmp.TEN = !isEmpty(resultData[i]['NAME']) ? resultData[i]['NAME'] : '';
					objTmp.TYPE = !isEmpty(resultData[i]['TYPE']) ? resultData[i]['TYPE'] : '';
					objTmp.GIA_TRI = !isEmpty(resultData[i]['VALUE']) ? resultData[i]['VALUE'] : '';
					objTmp.DON_VI = !isEmpty(resultData[i]['UNIT']) ? resultData[i]['UNIT'] : '';
					objTmp.PARENT_CODE = !isEmpty(resultData[i]['PARENT_CODE']) ? resultData[i]['PARENT_CODE'] : null;
					objTmp.DESCRIPTION = resultData[i]['DESCRIPTION'];

					// Common
					objTmp.CRT_DT = !isEmpty(resultData[i]['CRT_DT']) ? resultData[i]['CRT_DT'] : null;
					objTmp.UPD_DT = !isEmpty(resultData[i]['UPD_DT']) ? resultData[i]['UPD_DT'] : null;
					objTmp.CRT_ID = !isEmpty(resultData[i]['CRT_ID']) ? resultData[i]['CRT_ID'] : null;
					objTmp.UPD_ID = !isEmpty(resultData[i]['UPD_ID']) ? resultData[i]['UPD_ID'] : null;
					objTmp.TRANG_THAI = !isEmpty(resultData[i]['TRANG_THAI']) ? resultData[i]['TRANG_THAI'] : null;
					objTmp.TRANG_THAI_HOAT_DONG = !isEmpty(resultData[i]['TRANG_THAI_HOAT_DONG']) ? resultData[i]['TRANG_THAI_HOAT_DONG'] : null;

					// Thêm row object này vào dataset
					dataSet.push(objTmp);
				}

				// Empty data table and destroy every before init
				dataTableSettingCountView.clear();
				// Xóa tất cả sort trên columns
				dataTableSettingCountView.order([]);
				// Add dataset vào datatables
				dataTableSettingCountView.rows.add(dataSet).draw();

				/* End render table */

			}
			, error : function(request, textStatus, errorThrown) {
				request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
			}
			, complete : function() {
			}
		});
    }
	fnSearchSettingCountView();

	// Event handler cho button "Xem chi tiết"
	$(document).on('click', '.btn-view-detail', function() {
		let code = $(this).data('code');
		let name = $(this).data('name');
		
		// Set title modal
		$('#MODAL_TITLE_NGAY').text(name);
		
		// Load dữ liệu chi tiết
		loadChiTietLuotXem(code);
		
		// Hiển thị modal
		$('#modalChiTietLuotXem').modal('show');
	});

	// Khởi tạo datatables cho modal chi tiết
	var dataTableChiTietLuotXem = null;
	
	loadChiTietLuotXem = function(code) {
		$.ajax({
			type: "GET",
			url: '{{ url("/api/setting/detail") }}' + "/" + code,
			contentType: "application/json",
			traditional: true,
			showLoading: true,
			success: function(data, textStatus, request) {
				data = data.DATAS.SETTING;
				
				// Parse ATTR2 từ JSON string thành array
				let ipHistoryVN = [];
				if (!isEmpty(data.ATTR2)) {
					try {
						ipHistoryVN = JSON.parse(data.ATTR2);
						if (!Array.isArray(ipHistoryVN)) {
							ipHistoryVN = [];
						}
					} catch (e) {
						console.error('Error parsing ATTR2:', e);
						ipHistoryVN = [];
					}
				}
				
				// Prepare dataset for datatables
				let dataSet = [];
				for (let i = 0; i < ipHistoryVN.length; i++) {
					let item = ipHistoryVN[i];
					dataSet.push({
						STT: i + 1,
						CURRENT_TIME: item.currentTime || '',
						IP: item.ip || '',
						IP_VERSION: item.ipVersion || '',
						COUNTRY: item.country || '',
						CITY: item.city || '',
						ISP: item.isp || '',
						DEVICE_TYPE: item.deviceType || '',
						OS: item.os || '',
						BROWSER: item.browser || '',
						BOT_TYPE: item.botType || '',
						URL: item.url || ''
					});
				}
				
				// Destroy existing datatables if exists
				if (dataTableChiTietLuotXem !== null) {
					dataTableChiTietLuotXem.destroy();
					$('#tableChiTietLuotXem').empty();
				}
				
				// Initialize datatables (giống tableSettingCountView)
				dataTableChiTietLuotXem = new DataTable('#tableChiTietLuotXem', {
					"data": dataSet,
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
					, "serverSide": false
					, "deferLoading": false
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
							, "render": function (data, type, row, meta) {
								let html = '<span data-id="' + row.STT + '" >' + data + '</span>';
								return html;
							}
						}
						, { 
							"title": "Thời gian"
							, "data": "CURRENT_TIME"
							, "visible": true
							, "className": "text-center text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [1]
							, "width": "180px"
							, "render": function (data, type, row, meta) {
								return isEmpty(data) ? null : data;
							}
						}
						, { 
							"title": "IP Ver"
							, "data": "IP_VERSION"
							, "visible": true
							, "className": "text-center text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [2]
							, "width": "100px"
							, "render": function (data, type, row, meta) {
								return isEmpty(data) ? null : data;
							}
						}
						, { 
							"title": "IP"
							, "data": "IP"
							, "visible": true
							, "className": "text-left text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [3]
							, "width": "200px"
							, "render": function (data, type, row, meta) {
								if (isEmpty(data)) return '';
								// Hiển thị tối đa 4 dòng với ellipsis
								return '<div style="max-height: 5.6em; overflow: hidden; line-height: 1.4em; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; text-overflow: ellipsis; word-break: break-all;" title="' + data + '">' + data + '</div>';
							}
						}
						, { 
							"title": "Thành phố"
							, "data": "CITY"
							, "visible": true
							, "className": "text-left text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [4]
							, "width": "150px"
							, "render": function (data, type, row, meta) {
								return isEmpty(data) ? null : data;
							}
						}
						, { 
							"title": "ISP"
							, "data": "ISP"
							, "visible": true
							, "className": "text-left text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [5]
							, "width": "200px"
							, "render": function (data, type, row, meta) {
								if (isEmpty(data)) return '';
								// Hiển thị tối đa 4 dòng với ellipsis
								return '<div style="max-height: 5.6em; overflow: hidden; line-height: 1.4em; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; text-overflow: ellipsis;" title="' + data + '">' + data + '</div>';
							}
						}
						, { 
							"title": "Thiết bị"
							, "data": "DEVICE_TYPE"
							, "visible": true
							, "className": "text-center text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [6]
							, "width": "100px"
							, "render": function (data, type, row, meta) {
								return isEmpty(data) ? null : data;
							}
						}
						, { 
							"title": "OS"
							, "data": "OS"
							, "visible": true
							, "className": "text-left text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [7]
							, "width": "120px"
							, "render": function (data, type, row, meta) {
								return isEmpty(data) ? null : data;
							}
						}
						, { 
							"title": "Trình duyệt"
							, "data": "BROWSER"
							, "visible": true
							, "className": "text-left text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [8]
							, "width": "130px"
							, "render": function (data, type, row, meta) {
								return isEmpty(data) ? null : data;
							}
						}
						, { 
							"title": "URL"
							, "data": "URL"
							, "visible": true
							, "className": "text-left text-wrap-auto"
							, "searchable": true
							, "orderable": true
							, "orderData": [9]
							, "width": "350px"
							, "render": function (data, type, row, meta) {
								if (isEmpty(data)) return '';
								// Hiển thị tối đa 4 dòng với ellipsis
								return '<div style="max-height: 5.6em; overflow: hidden; line-height: 1.4em; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; text-overflow: ellipsis; word-break: break-all;" title="' + data + '">' + data + '</div>';
							}
						}
					]
					, "columnDefs": [
						{
							"targets": '_all'
						}
						, {
							"targets": 0
						}
					]
					, "aLengthMenu": [
						[10, 20, 50, -1],
						[10, 20, 50, "Tất cả"]
					]
					, "iDisplayLength": 10
					, "language": {
						"search": "Tìm kiếm"
						, "searchPlaceholder": 'Nhập từ khóa...'
						, "info" : "<p class=\"card-description\">Hiển thị _START_ đến _END_. <span class=\"one-line\">Tổng số <code>_TOTAL_ kết quả</code> </span></p>"
						, "emptyTable": '<h6 class="block text-center"><i class="fa fa-folder-open-o" style="color: #6c7293;"></i> {{ __("Không có dữ liệu") }}</h6>'
						, "zeroRecords": '<h6 class="block text-center"><i class="fa fa-folder-open-o" style="color: #6c7293;"></i> {{ __("Không có dữ liệu") }}</h6>'
						, "infoEmpty": "<p class=\"card-description\"><span class=\"one-line\">Không có dữ liệu</span></p>"
						, "infoFiltered": ""
						, "lengthMenu": "Hiển thị &nbsp; _MENU_"
						, "paginate": {
							/* "first":      "Đầu tiên"
							, "last":       "Cuối cùng"
							, "next":       "Sau"
							, "previous":   "Trước" */
						}
					}
					, "dom": 'Bflrtip'
					, "buttons": [
						{
							attr:  {
								title: 'Change views',
								id: 'card-toggle'
							},
							text: 'Hiển thị kiểu <i class="fa fa-id-badge fa-fw fa-lg" aria-hidden="true"></i>',
							className: 'animated bounce d-none',
							action: function () {
								alert('Submit');
							}
						}
					]
					, "drawCallback": function(settings) {
						var api = this.api();
						
						// Cập nhật số thứ tự
						api.rows().every(function (rowIdx, tableLoop, rowLoop) {
							let indexRowSTT = 0;
							this.cell(rowIdx, indexRowSTT).data(rowIdx + 1);
						});
						
						// START Xử lý z-index cho các button dropdown trên datatables
						const dropdownsOnTable = $("#tableChiTietLuotXem .dropdown-toggle");
						dropdownsOnTable.each((index, dropdownToggleEl) => {
							dropdownToggleEl.addEventListener("show.bs.dropdown", function (event) {
								$(event.target).closest("td").addClass("z-index-3");
							});
							dropdownToggleEl.addEventListener("hide.bs.dropdown", function (event) {
								$(event.target).closest("td").removeClass("z-index-3");
							});
						});
						// END Xử lý z-index cho các button dropdown trên datatables
						
						// Xử lý hiển thị default ảnh thumnail khi bị lỗi 404 không tìm thấy
						const images = document.querySelectorAll("#tableChiTietLuotXem .td-img-thumnail > img");
						images.forEach((image) => {
							$(image).on("error", function(e) {
								image.src = "{{asset('image/UI-BACKEND/default-no-image.jpg') }}";
							});
						});
						
						// Điều chỉnh lại kích thước cột
						$('#tableChiTietLuotXem').DataTable().columns.adjust();
					}
					, "initComplete": function (settings, json) {
						if (window.matchMedia('(max-width: 575px)').matches) {
							$('#tableChiTietLuotXem').DataTable().columns([0]).visible(false, true);
						}
						
						// Khởi tạo scroll ngang và dọc cho datatables
						$("#tableChiTietLuotXem").wrap("<div style='overflow:auto; width: 100%; position:relative; max-height: 60vh;'></div>");
					}
				});
				
			},
			error: function(request, textStatus, errorThrown) {
				request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
			},
			complete: function() {
				
			}
		});
	};

	loadChiTietCountTotalView = function(code, $el) {
		/* Xử lý get chi tiết product */
		// Create object data to check
		var inputData = {
		};

		$.ajax({
			type : "GET",
			url: '{{ url("/api/setting/detail") }}' + "/" + code, 
			contentType : "application/json",
			traditional: true,
			showLoading: false,
			data : inputData,
			success : function(data, textStatus, request) {
				data = data.DATAS.SETTING;
				if (isEmpty(data) || isEmpty(data.GIA_TRI)) return;
				$el.text(data.GIA_TRI)
			},
			error : function(request, textStatus, errorThrown) {
				request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');

				// Show section error 404
				// redirectErrorPage(404);
			},
			complete : function() {
			}
		});
	}
	loadChiTietCountTotalView('SETTING_COUNT_TOTAL_VIEW', $('#TONG_SO_LUOT_XEM'));


	// Callback tinyEditor
	{{ 'section_' . $uuid1 }}_{{ 'section_' . $uuid1 }}_EDIT_SETTING_GIOI_THIEU_CUA_HANG_callBackTinyMCEAfterInit = function(editor) {
		
	}
	{{ 'section_' . $uuid4 }}_{{ 'section_' . $uuid4 }}_EDIT_SETTING_CAM_KET_BAN_HANG_callBackTinyMCEAfterInit = function(editor) {
		
	}
	

	fnLoadListSettingWeb = function() {
		var valueGioiThieuCuaHang = null;
		var valueCamKetBanHang = null;
    	// Create object data to check
		$.ajax({
			type : "GET"
			, url : '{{ url("/api/setting/list") }}'
			, contentType : "application/json"
			, cache: false // Đảm bảo không cache dữ liệu
			, traditional: false // Bắt buộc đặt là false
			, showLoading: false
			, data : (function() { // IIFE 
				let dataInput = {};

				/* 
				dataInput.PAGE =  1 || null;
				dataInput.PER_PAGE = 2147483647 || null; 
				*/
				dataInput.IS_GET_ALL_ELEMENTS = true;
				dataInput.TYPE = 'SETTING_WEB';
			
				return dataInput; // Trả về object input data
			})()
			, success : function(data, textStatus, request) {

				/* Start render table */
				var result = data.DATAS.SETTING.DATA;
				var resultData = !isEmpty(result) ? result : [];

				// Looping
				var idxHotline = 1;
				for (let i = 0; i < resultData.length; i++) {
					let key = resultData[i].CODE.toUpperCase();
					let value = resultData[i].VALUE;
					let order = resultData[i].ORDER;
					$('#EDIT_' + key).val(value);

					if (key.includes('SETTING_HOTLINE_TYPE_')) {
						// Append new row
						newRowTemplateSettingInput(idxHotline);

						// Set data
						let arrData = resultData[i].VALUE?.split('|');
						if (arrData.length != 3) continue;

						let loai = arrData[0];
						let sdt = arrData[1];
						let tenChuSdt = arrData[2];

						$('#EDIT_SETTING_SDT_' + idxHotline).val(sdt);
						$('#EDIT_SETTING_TEN_CHU_SDT_' + idxHotline).val(tenChuSdt);
						$('#EDIT_SETTING_LOAI_' + idxHotline).val(loai).trigger('change');

						// Tăng lên 1 đơn vị
						idxHotline++;

					} else if (key === 'SETTING_GIOI_THIEU_CUA_HANG') {
						valueGioiThieuCuaHang = value;
					} else if (key === 'SETTING_CAM_KET_BAN_HANG') {
						valueCamKetBanHang = value;
					} else {
						$('#EDIT_' + key).val(value);
					}
				}


			}
			, error : function(request, textStatus, errorThrown) {
				request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
				showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
			}
			, complete : function() {
				{{ 'section_' . $uuid1 }}_initTinyMce(valueGioiThieuCuaHang);
				{{ 'section_' . $uuid4 }}_initTinyMce(valueCamKetBanHang);
			}
		});
    }
	fnLoadListSettingWeb();




	/* Xử lý event click btn import thành phố */
    $('button[name="BTN_IMPORT_THANH_PHO"]').on('click', function() {
    	importThanhPho();
	});

	importThanhPho = function() {
		// Get form
		var formData = new FormData();

		var fileInput = $('#EDIT_IMPORT_EXCEL_THANH_PHO')[0]; // Để lấy DOM element gốc
		var file = fileInput.files[0];
		if (file != null) {
			// Add file to form data
			formData.append('FILE', file);
		}
	
		// Call api import thành phố
		$.ajax({
			type: "POST",
			enctype: 'multipart/form-data',
			url: '{{ url("/api/cities/import-data") }}',
			data: formData,
			showLoading: true,
	
			// prevent jQuery from automatically transforming the data into a query string
			processData: false, // Không xử lý data thành query string
			contentType: false, // Không đặt content-type, để trình duyệt tự xử lý multipart/form-data
			cache: false,
			timeout: 1000000,
			success: function(data, textStatus, jqXHR) {
				// Ajax call completed successfully 
				showToastSuccess('top-right', data.STATUS_DETAIL);
			},
			error: function(request, textStatus, errorThrown) {
				// Some error in ajax call 
				if (request && request.responseJSON && request.responseJSON.STATUS_DETAIL)
					showToastFailure('top-right', request.responseJSON ? request.responseJSON.STATUS_DETAIL : 'Internal server');
			},
			complete : function() {

			}
		});
	
	}

	/* Xử lý event click btn xóa cache */
	$('button[name="BTN_XOA_CACHE"]').on('click', function() {
		showSwalWarningPopup(function callback(result) {
			if (result.isConfirmed === true) {
				xoaCacheFrontend();
			}
		}, 'Bạn có chắc chắn muốn <span style="display: inline-block;">xóa cache frontend?</span>');
	});

	xoaCacheFrontend = function() {
		$.ajax({
			type: "POST",
			url: '{{ url("/api/cache/evict") }}',
			contentType: "application/json",
			showLoading: true,
			data: JSON.stringify({}),
			success: function(data, textStatus, request) {
				// Ajax call completed successfully
				showToastSuccess('top-right', data.STATUS_DETAIL || 'Xóa cache thành công!');
			},
			error: function(request, textStatus, errorThrown) {
				// Some error in ajax call
				try {
					request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
					showToastFailure('top-right', request.responseJSON && !isEmpty(request.responseJSON.STATUS_DETAIL) ? request.responseJSON.STATUS_DETAIL : 'Internal server');
				} catch(err) {
					showToastFailure('top-right', 'Internal server');
				}
			},
			complete: function() {
			}
		});
	}

	/* Xử lý event click btn fetch Sapo */
	$('button[name="BTN_FETCH_SAPO"]').on('click', function() {
		showSwalWarningPopup(function callback(result) {
			if (result.isConfirmed === true) {
				fetchSapoCatalog(false);
			}
		}, 'Bạn có chắc chắn muốn <span style="display: inline-block;">fetch sản phẩm Sapo?</span>');
	});

	/* Fetch full: GET lại toàn bộ sản phẩm Sapo, bỏ qua mốc last_fetch */
	$('button[name="BTN_FETCH_SAPO_FULL"]').on('click', function() {
		showSwalWarningPopup(function callback(result) {
			if (result.isConfirmed === true) {
				fetchSapoCatalog(true);
			}
		}, 'Fetch FULL sẽ tải lại toàn bộ sản phẩm Sapo <span style="display: inline-block;">(có thể mất vài phút). Tiếp tục?</span>');
	});

	fetchSapoCatalog = function(full) {
		$.ajax({
			type: "POST",
			url: '{{ url("/api/sapo/sync") }}',
			contentType: "application/json",
			showLoading: true,
			data: JSON.stringify({ FULL: full === true }),
			success: function(data, textStatus, request) {
				if (data.STATUS === false) {
					showToastFailure('top-right', data.STATUS_DETAIL || 'Fetch Sapo thất bại!');
					return;
				}
				showToastSuccess('top-right', data.STATUS_DETAIL || 'Fetch Sapo thành công!');
			},
			error: function(request, textStatus, errorThrown) {
				try {
					request.responseJSON = request.responseText ? JSON.parse(request.responseText) : null;
					showToastFailure('top-right', request.responseJSON && !isEmpty(request.responseJSON.STATUS_DETAIL) ? request.responseJSON.STATUS_DETAIL : 'Internal server');
				} catch(err) {
					showToastFailure('top-right', 'Internal server');
				}
			},
			complete: function() {
			}
		});
	}

})
</script>
@stop