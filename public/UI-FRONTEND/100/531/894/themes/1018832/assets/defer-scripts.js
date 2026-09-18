function getScript(scriptUrl, callback) {
  var script = document.createElement('script');
    var prior = document.getElementsByTagName('script')[0];
    script.async = 1;

    script.onload = script.onreadystatechange = function( _, isAbort ) {
        if(isAbort || !script.readyState || /loaded|complete/.test(script.readyState) ) {
            script.onload = script.onreadystatechange = null;
            script = undefined;

            if(!isAbort && callback) setTimeout(callback, 0);
        }
    };

    script.src = scriptUrl;
    prior.parentNode.insertBefore(script, prior);
}
 function loadCSS(url) {
      var link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = url;
      document.head.appendChild(link);
    }
 
let loaded;
function loadDefer(){
	if(loaded) return;
	loaded = true;
	loadCSS(window.themeConfigs.vendorsCssLink)
	var done = false;
	function markReady(e){
		if(done) return;
		done = true;
		window.__wwFirstInteractionDone = true;
		try {
			publish(window.themeConfigs.firstInteraction, e)
		} catch (err) {}
	}
	getScript(window.themeConfigs.vendorsJSLink, markReady)
	// CDN lỗi / chậm: vẫn đánh dấu firstInteraction để cart UI boot
	setTimeout(function () { markReady(null); }, 2500);
}


document.addEventListener("DOMContentLoaded", (event) => {
     let events = ['mousemove', 'touchstart', 'scroll']
		events.forEach((event)=>{
			window.addEventListener(event, () => {
				   loadDefer()
			  }, { once: true })
		})
		if(window.pageYOffset >= window.innerHeight){
			loadDefer()
		}

  });
 
function mailChimpResponse(form,resp) {
	let alert = 	form.next()
	if (resp.result === 'success') {
		if(resp.msg == 'Thank you for subscribing!'){
			alert.find('.mailchimp-success').html('Cảm ơn bạn đã đăng ký!').fadeIn(900);
		}else{
			alert.find('.mailchimp-success').html('' + resp.msg).fadeIn(900);
		}
		$('.mailchimp-error').fadeOut(100);
	} else if (resp.result === 'error') {
		if(resp.msg == '0 - Please enter a value'){
			alert.find('.mailchimp-error').html('Vui lòng nhập các trường thông tin').fadeIn(900);
		}else if(resp.msg == '0 - An email address must contain a single @'){
			alert.find('.mailchimp-error').html('Địa chỉ email phải chứa ký tự @').fadeIn(900);
		}else if(resp.msg == 'This email cannot be added to this list. Please enter a different email address.'){
			alert.find('.mailchimp-error').html('Email này không thể được thêm vào danh sách này. Vui lòng nhập một địa chỉ email khác.').fadeIn(900);
		}else if(resp.msg.includes('0 - The domain portion of the email address is invalid')){
			alert.find('.mailchimp-error').html('Phần tên miền của địa chỉ email không hợp lệ').fadeIn(900);
		}else if(resp.msg.includes('0 - The username portion of the email address is empty')){
			alert.find('.mailchimp-error').html('Phần tên người dùng của địa chỉ email trống').fadeIn(900);
		}else if(resp.msg.includes('0 - The username portion of the email address is invalid')){
			alert.find('.mailchimp-error').html('Phần tên người dùng của địa chỉ email không hợp lệ').fadeIn(900);
		}else if(resp.msg == 'Thank you for subscribing!'){
			alert.find('.mailchimp-error').html('Cảm ơn bạn đã đăng ký!').fadeIn(900);
		}else{
			alert.find('.mailchimp-error').html('' + resp.msg).fadeIn(900);
		}
	}
}
function horizontalNav () {
	return {
		wrapper: $('.navigation--horizontal .navigation-horizontal-wrapper'),
		navigation: $('.navigation--horizontal .navigation-horizontal'),
		item: $('.navigation--horizontal .navigation-horizontal .menu-item '),
		arrows: $('.navigation-arrows'),
		scrollStep: 0,
		totalStep: 0,
		transform: function(){
			return `translateY(-${this.scrollStep*100}%)` 
		},
		onCalcNavOverView: function(){
			let itemHeight = this.item.eq(0).outerHeight(),
				navHeight = this.navigation.height()
			return Math.ceil(navHeight/itemHeight)
		},
		handleArrowClick: function(e){
			this.totalStep = this.onCalcNavOverView()
			this.scrollStep = $(e.currentTarget).hasClass('prev') ? this.scrollStep - 1 : this.scrollStep + 1
			this.handleScroll()
		},
		handleScroll: function(){
			this.arrows.find('button').removeAttr('disabled')
			if(this.totalStep - 1 <= this.scrollStep ){
				this.arrows.find('.next').attr('disabled',true)
				this.scrollStep = this.totalStep - 1
			}
			if(this.scrollStep <= 0){
				this.arrows.find('.prev').attr('disabled',true)
				this.scrollStep = 0
			}
			this.item.find('.menu-item__link').css('transform', this.transform())
		},
		init:function(){
			this.totalStep = this.onCalcNavOverView()
			if(this.totalStep > 1){
				this.wrapper.addClass('overflow')
			} 
			this.handleScroll()
			this.arrows.find('button').click((e)=>this.handleArrowClick(e))
		}
	}	
}
let lastScrollTop = 0
let stickyHeaderTicking = false
function syncHeaderFixedSpacer(stickyHeader, isActive) {
	const body = document.body;
	if (!body || !body.classList.contains('ega-theme')) return;
	const useFixed = window.matchMedia('(max-width: 1279px)').matches;
	if (isActive && useFixed) {
		const h = stickyHeader && stickyHeader[0] ? stickyHeader[0].offsetHeight : 64;
		document.documentElement.style.setProperty('--ww-header-offset', h + 'px');
		body.classList.add('ww-header-fixed');
	} else {
		body.classList.remove('ww-header-fixed');
		document.documentElement.style.removeProperty('--ww-header-offset');
	}
}
function initStickyHeader(){
		const stickyHeader = $('.header')
		if (!stickyHeader.length) return;
		const isInputFocus = $('.header input:focus').length > 0
		let height =   isInputFocus ?  400 : 250 ;
	  
		let sticky = window.innerHeight / 2 > height ? height : window.innerHeight / 2  ;
	
	    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		const shouldStick = scrollTop > sticky;
		if (shouldStick) {
			stickyHeader.addClass("active");
			syncHeaderFixedSpacer(stickyHeader, true);
		} else {
			stickyHeader.removeClass("active");
			syncHeaderFixedSpacer(stickyHeader, false);
		}
		lastScrollTop = scrollTop;
	}
function initStickyHeaderOnScroll() {
	if (stickyHeaderTicking) return;
	stickyHeaderTicking = true;
	requestAnimationFrame(function () {
		initStickyHeader();
		stickyHeaderTicking = false;
	});
}
// Bind sớm: không đợi firstInteraction (mobile hay mất sticky header)
$(window).on('scroll.wwStickyHeader', initStickyHeaderOnScroll);
$(window).on('resize.wwStickyHeader', function () {
	const stickyHeader = $('.header');
	syncHeaderFixedSpacer(stickyHeader, stickyHeader.hasClass('active'));
});
function lazyloadSrc(){
	const elements = document.querySelectorAll('[data-lazyload]');
	const observer = new IntersectionObserver((entries) => {
	  entries.forEach(entry => {
		if (entry.isIntersecting) {
		  const element = entry.target;
		  element.src = element.dataset.src;
		  observer.unobserve(element);
		}
	  });
	}, { root: null, threshold: 0.5 });

	elements.forEach(element => observer.observe(element));


}
initStickyHeader()
if(window.matchMedia('(min-width: 992px)').matches){
		horizontalNav().init()
	}
subscribe(window.themeConfigs.firstInteraction, (e)=>{
	
	$('#mc-form').ajaxChimp({
			language: 'en',
			callback: (resp) => mailChimpResponse($('#mc-form'),resp),
			url: window.themeConfigs.newsletterFormAction
		});
	 function awe_backtotop() { 
		if ($('.back-to-top').length) {
			var scrollTrigger = 100, // px
				backToTopTicking = false,
				docHeightMinus700 = 0,
				backToTop = function () {
					var scrollTop = $(window).scrollTop();
					if (scrollTop > scrollTrigger) {
						$('.back-to-top').addClass('show');
					} else {
						$('.back-to-top').removeClass('show');
					}

					if (!docHeightMinus700 || scrollTop > docHeightMinus700 - 400) {
						docHeightMinus700 = $(document).height() - 700;
					}
					if (scrollTop > docHeightMinus700 ) {
						$('.back-to-top').addClass('end');
					} else {
						$('.back-to-top').removeClass('end');
					}
				};
			backToTop();
			$(window).on('scroll', function () {
				if (backToTopTicking) return;
				backToTopTicking = true;
				requestAnimationFrame(function () {
					backToTop();
					backToTopTicking = false;
				});
			});
			$('.back-to-top').on('click', function (e) {
				e.preventDefault();
				$('html,body').animate({
					scrollTop: 0
				}, 700);
			});
		}
	} window.awe_backtotop=awe_backtotop;
	awe_backtotop();
	
	function initNavigation(){

	// sticky header scroll đã bind sớm ở ngoài (scroll.wwStickyHeader)
	$('.floating_banner .btn').click((e)=>{
		$('.floating_banner').remove()

	})
		

}
	 
	initNavigation()
	 document.querySelectorAll('[data-lazyload]').forEach(el =>{
	 	if(el.dataset.src){
			el.src = el.dataset.src;
			el.removeAttribute('data-lazyload')
		}
	 
	 } )
	 initFooter()
	 
	  if ($('#live-template').length && $('.modal-live-iframe').length) {
	    $('.modal-live-iframe').html($('#live-template').html())
	    $('.btn-live').click((e)=>{
	      if(window.innerWidth > 767){
		    $('.modal-live').toggleClass('opened')
	      }else{
		    window.open(e.currentTarget.dataset.mobileLink, '_blank')
	      }
	    })
	    $('.btn-close--live').click(()=> { $('.modal-live').removeClass('opened') })
	  }
	 
	 
})

function initFooter(){
	const media = window.themeConfigs.mbBreakpoint;
	
	const toggleDetails = (media)=>{
		if(media.matches){
			$('.footer-details').removeAttr('open')
		}else{
		   $('.footer-details').add('open',true)

		}
	}
	toggleDetails(media)
	media.addEventListener("change", toggleDetails);
	
	
	$('.footer-details summary').click((e)=>{
		if(!media.matches){
			e.preventDefault()
		}
	})
	
}