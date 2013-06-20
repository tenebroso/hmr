//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.capabilityBGSlideShow = function() {

	$('.slideshow').cycle({
		fx: 'fade',
		delay: 2000,
		speed:3000
	});

	$('.footer').cycle({
		fx: 'fade',
		delay: 2000,
		speed:3000
	});

	$('.meta-box').transition({ delay:7000, opacity: 0}, 2000, 'ease');

  };

})();