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
		fit: 1
	});

	$('.gallery-footer').cycle({
		fx: 'fade',
		delay: 1000,
		speed:3000
	});
  };

})();