{{-- Shared quick-view modal shell — dùng chung mọi trang storefront --}}
<quick-view
	class="portal portal--modal"
	id="quick-view-product"
	data-type="modal"
	data-animation=""
	style="--dialog-max-width:920px;--ww-qv-modal-width:min(920px, 100%)"
>
	<dialog class="portal-dialog">
		<div class="flex items-center justify-center w-full h-full">
			<div class="portal-overlay"></div>
			<div class="portal-inner h-full">
				<button
					type="button"
					id="PortalClose-quick-view-product"
					data-animation="fade-in"
					class="portal-close-button animation rounded-full w-[3.2rem] h-[3.2rem] border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin"
					aria-label="Đóng"
				>
					<i class="icon icon-cross"></i>
				</button>
				{{-- X nằm sibling của .product-wrapper (JS replaceChildren không xóa nút đóng) --}}
				<div class="product-wrapper animation bg-background w-full h-full md:rounded-lg"></div>
				<span class="loading-icon gap-1 hidden items-center justify-center" aria-hidden="true">
					<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
					<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
					<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
				</span>
			</div>
		</div>
	</dialog>
</quick-view>
