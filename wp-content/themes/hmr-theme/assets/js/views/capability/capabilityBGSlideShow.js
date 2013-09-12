//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {


  HMR.capabilityBGSlideShow = function() {

  	if($(window).width() > 767) {

  		HMR.nav.slideUp();

  	}

	$('.slideshow').cycle({
		fx: 'fade',
		delay: 1000,
		speed:3000,
		containerResize: false,
		slideResize: false,
		fit: 1,
		next:   '#next', 
      	prev:   '#prev'
	});

	$('.photo-credit').cycle({
		fx: 'fade',
		delay: 1000,
		speed:3000
	});

	jQuery(".slideshow").touchwipe({
		wipeLeft: function() {
			jQuery(".slideshow").cycle("next");
	    },
	    wipeRight: function() {
	        jQuery(".slideshow").cycle("prev");
	    }
	});

  };

})();