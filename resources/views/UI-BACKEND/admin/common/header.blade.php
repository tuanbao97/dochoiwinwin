<style>
.navbar .navbar-brand-wrapper .navbar-brand-inner-wrapper .navbar-brand img {
	max-height: 48px;
	width: auto;
	object-fit: contain;
}
.navbar-brand.brand-logo {
	margin: auto !important;
}
.navbar .navbar-brand-wrapper {
    /* background: linear-gradient(315.42deg, #0866ff 3.91%, #2196F3 96.72%); */
	background: white;
}
.navbar {
    border-bottom: 4px solid #639af3;
}
.header-img-account {
	width: 100%;
    height: 37px;
    border: 1px solid #e4e4f4;
    box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 2px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
    border-radius: 0.25rem;
}
@media ( max-width : 480px) {
	.navbar .navbar-brand-wrapper {
		width: 75px;
	}
	.navbar .navbar-brand-wrapper .brand-logo-mini {
		padding-top: 0px;
		width: 80px;
	}
}
@media ( max-width : 767px) {
	.section-my-info-dropdown {
		display: block !important;
		margin-left: 0.531rem;
	}
}
@media (max-width: 991px) {
    .navbar .navbar-brand-wrapper {
        width: 100px;
    }
    .navbar .navbar-brand-wrapper .navbar-brand-inner-wrapper .navbar-brand.brand-logo-mini {
		width: 90px;
        /* margin-left: 10px; */
    }
    .navbar .navbar-menu-wrapper {
        width: calc(100% - 100px);
        padding-left: 15px;
        padding-right: 15px;
    }
}
</style>
<!-- partial:partials/_navbar.html -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
	<div class="navbar-brand-wrapper d-flex justify-content-center">
		<div
			class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
			<a class="navbar-brand brand-logo" href="{{ url('/admin/san-pham/danh-sach') }}"><img
				src="{{ storefrontLogoWideUrl() }}" alt="Đồ Chơi Win Win" style="width: auto; max-width: 100%; height: 42px;" /></a>
			<a class="navbar-brand brand-logo-mini" href="{{ url('/admin/san-pham/danh-sach') }}"><img
				src="{{ storefrontLogoWideUrl() }}" alt="Đồ Chơi Win Win" style="width: auto; max-width: 100%; height: 42px;" /></a>
			<button class="navbar-toggler navbar-toggler align-self-center"
				type="button" data-toggle="minimize">
				<span class="typcn typcn-th-menu"></span>
			</button>
		</div>
	</div>
	<div
		class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
		<ul class="navbar-nav mr-lg-2">
			<li class="nav-item nav-profile dropdown">
				<a class="nav-link"
				href="#" data-toggle="dropdown" id="profileDropdown"> 

					<img alt="profile" name="IMG_AVATAR"
						onerror="this.src='{{ asset('image/UI-BACKEND/profile-male.png') }}';this.onerror='';">

					<span class="nav-profile-name" name="USER_FULL_NAME"></span>
				</a>
				<div class="dropdown-menu dropdown-menu-right navbar-dropdown"
					aria-labelledby="profileDropdown">
					<a class="dropdown-item" name="BTN_THAY_DOI_THONG_TIN">
						<i class="typcn typcn-cog-outline text-primary"></i> Đổi thông tin cá nhân
					</a>
					<a class="dropdown-item" name="BTN_THAY_DOI_MAT_KHAU">
						<i class="typcn typcn-cog-outline text-primary"></i> Đổi mật khẩu
					</a>
					<a class="dropdown-item" name="BTN_DANG_XUAT"> 
						<i class="typcn typcn-eject text-primary"></i> Đăng xuất
					</a>
				</div>
			</li>
			<!-- <li class="nav-item nav-user-status dropdown">
				<p class="mb-0">Lần đăng nhập cuối cùng là 23 giờ trước.</p>
			</li> -->
		</ul>
		<ul class="navbar-nav navbar-nav-right">
			
			<li class="nav-item dropdown" style="display: none !important;">
				<a
					class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center"
					id="messageDropdown" href="#" data-toggle="dropdown"> <i
						class="typcn typcn-cog-outline mx-0"></i> <span class="count"></span>
				</a>
				<div
					class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
					aria-labelledby="messageDropdown">
					<p class="mb-0 font-weight-normal float-left dropdown-header">Tin nhắn</p>
					<a class="dropdown-item preview-item">
						<div class="preview-thumbnail">
							<img src="{{ asset('image/UI-BACKEND/faces/face4.jpg') }}"
								alt="image" class="profile-pic">
						</div>
						<div class="preview-item-content flex-grow">
							<h6 class="preview-subject ellipsis font-weight-normal">Lê Minh Thắng</h6>
							<p class="font-weight-light small-text text-muted mb-0">The
								meeting is cancelled</p>
						</div>
					</a> <a class="dropdown-item preview-item">
						<div class="preview-thumbnail">
							<img src="{{ asset('image/UI-BACKEND/faces/face2.jpg') }}"
								alt="image" class="profile-pic">
						</div>
						<div class="preview-item-content flex-grow">
							<h6 class="preview-subject ellipsis font-weight-normal">Đỗ Thành Trí</h6>
							<p class="font-weight-light small-text text-muted mb-0">New
								product launch</p>
						</div>
					</a> <a class="dropdown-item preview-item">
						<div class="preview-thumbnail">
							<img src="{{ asset('image/UI-BACKEND/faces/face3.jpg') }}"
								alt="image" class="profile-pic">
						</div>
						<div class="preview-item-content flex-grow">
							<h6 class="preview-subject ellipsis font-weight-normal">Đặng Hoàng Trung</h6>
							<p class="font-weight-light small-text text-muted mb-0">Upcoming
								board meeting</p>
						</div>
					</a>
				</div>
			</li>

			<li class="nav-item dropdown mr-0" style="display: none !important;">
				<a
				class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center"
				id="notificationDropdown" href="#" data-toggle="dropdown"> <i
					class="typcn typcn-bell mx-0"></i> <span class="count"></span>
				</a>
				<div
					class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
					aria-labelledby="notificationDropdown">
					<p class="mb-0 font-weight-normal float-left dropdown-header">Thông báo</p>
					<a class="dropdown-item preview-item">
						<div class="preview-thumbnail">
							<div class="preview-icon bg-success">
								<i class="typcn typcn-info mx-0"></i>
							</div>
						</div>
						<div class="preview-item-content">
							<h6 class="preview-subject font-weight-normal">Application Error</h6>
							<p class="font-weight-light small-text mb-0 text-muted">Just now</p>
						</div>
					</a> <a class="dropdown-item preview-item">
						<div class="preview-thumbnail">
							<div class="preview-icon bg-warning">
								<i class="typcn typcn-cog-outline mx-0"></i>
							</div>
						</div>
						<div class="preview-item-content">
							<h6 class="preview-subject font-weight-normal">Settings</h6>
							<p class="font-weight-light small-text mb-0 text-muted">Private
								message</p>
						</div>
					</a> <a class="dropdown-item preview-item">
						<div class="preview-thumbnail">
							<div class="preview-icon bg-info">
								<i class="typcn typcn-user mx-0"></i>
							</div>
						</div>
						<div class="preview-item-content">
							<h6 class="preview-subject font-weight-normal">New user
								registration</h6>
							<p class="font-weight-light small-text mb-0 text-muted">2 days
								ago</p>
						</div>
					</a>
				</div>
			</li>

			<li class="nav-item dropdown mr-0" style="margin-left: unset; margin-right: unset;">
				<div class="section-my-info-dropdown" style="display: none;">
					<a
						id="myInfoDropdown" href="#" data-toggle="dropdown">
						<img class="header-img-account" name="IMG_AVATAR"
							alt="profile"
							onerror="this.src='{{ asset('image/UI-BACKEND/profile-male.png') }}';this.onerror='';">
					</a>
					
					<div
						class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
						aria-labelledby="myInfoDropdown">
						<p class="mb-0 font-weight-normal float-left dropdown-header">Xin chào, <span style="font-weight: 600; text-wrap: auto; margin-top: 5px;" name="USER_FULL_NAME"></span></p>

						<a class="dropdown-item preview-item" name="BTN_THAY_DOI_THONG_TIN">
							<div class="preview-thumbnail">
								<div class="preview-icon bg-info">
									<i class="typcn typcn-cog-outline mx-0"></i>
								</div>
							</div>
							<div class="preview-item-content">
								<h6 class="preview-subject font-weight-normal" style="margin-bottom: unset;">Đổi thông tin cá nhân</h6>
								<!-- <p class="font-weight-light small-text mb-0 text-muted">Private
									message</p> -->
							</div>
						</a>

						<a class="dropdown-item preview-item" name="BTN_THAY_DOI_MAT_KHAU">
							<div class="preview-thumbnail">
								<div class="preview-icon bg-info">
									<i class="typcn typcn-cog-outline mx-0"></i>
								</div>
							</div>
							<div class="preview-item-content">
								<h6 class="preview-subject font-weight-normal" style="margin-bottom: unset;">Đổi mật khẩu</h6>
								<!-- <p class="font-weight-light small-text mb-0 text-muted">Private
									message</p> -->
							</div>
						</a>

						<a class="dropdown-item preview-item" name="BTN_DANG_XUAT">
							<div class="preview-thumbnail">
								<div class="preview-icon bg-danger">
									<i class="typcn typcn-eject mx-0"></i>
								</div>
							</div>
							<div class="preview-item-content">
								<h6 class="preview-subject font-weight-normal" style="margin-bottom: unset;">Đăng xuất</h6>
								<!-- <p class="font-weight-light small-text mb-0 text-muted">2 days ago</p> -->
							</div>
						</a>
					</div>
				</div>
			</li>

		</ul>
		<button
			class="navbar-toggler navbar-toggler-right d-lg-none align-self-center"
			type="button" data-toggle="offcanvas">
			<span class="typcn typcn-th-menu"></span>
		</button>
	</div>
</nav>
<!-- partial -->

<nav class="navbar-breadcrumb col-xl-12 col-12 d-flex flex-row p-0">
	<div class="navbar-links-wrapper d-flex align-items-stretch">
		<div class="nav-link">
			<a href="javascript:;"><i class="typcn typcn-calendar-outline"></i></a>
		</div>
		<div class="nav-link">
			<a href="javascript:;"><i class="typcn typcn-mail"></i></a>
		</div>
		<div class="nav-link">
			<a href="javascript:;"><i class="typcn typcn-folder"></i></a>
		</div>
		<div class="nav-link">
			<a href="javascript:;"><i class="typcn typcn-document-text"></i></a>
		</div>
	</div>
	<div
		class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
		<ul class="navbar-nav mr-lg-2">
			@yield('nav-item')
		</ul>
		<ul class="navbar-nav navbar-nav-right">
			<li class="nav-item nav-search d-none d-md-block mr-0" style="display: none !important;">
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Tìm kiếm menu..."
						aria-label="search" aria-describedby="search">
					<div class="input-group-prepend">
						<span class="input-group-text" id="search"> <i
							class="typcn typcn-zoom"></i>
						</span>
					</div>
				</div>
			</li>
		</ul>
	</div>
</nav>