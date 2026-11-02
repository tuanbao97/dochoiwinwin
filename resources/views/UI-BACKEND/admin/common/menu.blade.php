<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    <li class="nav-item">
      <a class="nav-link" href="{{ url('/') }}">
        <i class="typcn typcn-home menu-icon"></i>
        <span class="menu-title">Trang chủ</span>
      </a>
    </li>

    <li class="nav-item" id="MENU_PARENT_LIEN_QUAN_DON_HANG" style="display: none;">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-thong-tin-don-hang" aria-expanded="true" aria-controls="menu-thong-tin-don-hang">
        <i class="typcn typcn-shopping-cart menu-icon"></i>
        <span class="menu-title">Đơn hàng</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse show" id="menu-thong-tin-don-hang">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_DON_HANG" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/don-hang/danh-sach') }}">Danh sách đơn hàng</a>
          </li>
          <li class="nav-item" id="MENU_KHACH_HANG" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/khach-hang/danh-sach') }}">Khách hàng</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item" style="display: none;">
      <a class="nav-link" href="{{ url('/quan-ly/template/dashboard/dashboard') }}">
        <i class="typcn typcn-device-desktop menu-icon"></i>
        <span class="menu-title">Dashboard</span>
        <div class="badge badge-danger">new</div>
      </a>
    </li>

    <li class="nav-item" style="display: none;">
      <a class="nav-link" href="{{ url('/quan-ly/template/widget/widget') }}">
        <i class="typcn typcn-archive menu-icon"></i>
        <span class="menu-title">Widgets</span>
      </a>
    </li>

    <li class="nav-item" id="MENU_PARENT_LIEN_QUAN_SAN_PHAM" style="display: none;">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-thong-tin-san-pham" aria-expanded="true" aria-controls="menu-thong-tin-san-pham">
        <i class="typcn typcn-shopping-cart menu-icon"></i>
        <span class="menu-title">Sản phẩm</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse show" id="menu-thong-tin-san-pham">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_DANH_MUC_SAN_PHAM" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/danh-muc-san-pham/danh-sach') }}">Danh mục sản phẩm</a>
          </li>
          <li class="nav-item" id="MENU_SAN_PHAM" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/san-pham/danh-sach') }}">Sản phẩm</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item" id="MENU_PARENT_LIEN_QUAN_TIN_TUC" style="display: none;">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-thong-tin-tin-tuc" aria-expanded="true" aria-controls="menu-thong-tin-tin-tuc">
        <i class="typcn typcn-news menu-icon"></i>
        <span class="menu-title">Tin tức</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse show" id="menu-thong-tin-tin-tuc">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_DANH_MUC_TIN_TUC" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/danh-muc-tin-tuc/danh-sach') }}">Danh mục tin tức</a>
          </li>
          <li class="nav-item" id="MENU_TIN_TUC" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/tin-tuc/danh-sach') }}">Tin tức</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item" id="MENU_PARENT_LIEN_QUAN_VIDEO" style="display: none;">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-thong-tin-video" aria-expanded="true" aria-controls="menu-thong-tin-video">
        <i class="typcn typcn-video menu-icon"></i>
        <span class="menu-title">Video</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse show" id="menu-thong-tin-video">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_VIDEO" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/video/danh-sach') }}">Danh sách video</a>
          </li>
        </ul>
      </div>
    </li>

	  <li class="nav-item" id="MENU_PARENT_THONG_TIN_CA_NHAN">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-thong-tin-ca-nhan" aria-expanded="true" aria-controls="menu-thong-tin-ca-nhan">
        <i class="typcn typcn-user menu-icon"></i>
        <span class="menu-title">Cá nhân</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse show" id="menu-thong-tin-ca-nhan">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_THONG_TIN_CA_NHAN" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/thong-tin-ca-nhan') }}">Thông tin cá nhân</a>
          </li>
        </ul>
		  <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_THONG_TIN_CA_NHAN">
            <a class="nav-link" href="{{ url('/admin/doi-mat-khau') }}">Đổi mật khẩu</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item" id="MENU_PARENT_LIEN_QUAN_NGUOI_DUNG" style="display: none;">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-nguoi-dung" aria-expanded="true" aria-controls="menu-nguoi-dung">
        <i class="typcn typcn-group menu-icon"></i>
        <span class="menu-title">Người dùng</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse show" id="menu-nguoi-dung">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_NGUOI_DUNG" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/nguoi-dung/danh-sach') }}">Danh sách người dùng</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item" id="MENU_PARENT_CAI_DAT" style="display: none;">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-cai-dat" aria-expanded="true" aria-controls="menu-cai-dat">
        <i class="typcn typcn-cog menu-icon"></i>
        <span class="menu-title">Cài đặt</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse show" id="menu-cai-dat">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item" id="MENU_CAI_DAT" style="display: none;">
            <a class="nav-link" href="{{ url('/admin/cai-dat') }}">Danh sách cài đặt</a>
          </li>
        </ul>
      </div>
    </li>

  </ul>
</nav>