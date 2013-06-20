//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.capabilityBGSlideShow = function() {

	$('.slideshow').cycle({
		fx: 'fade',
		delay: 3000,
		speed:100
	});

	$('.footer').cycle({
		fx: 'fade',
		delay: 3000,
		speed:100
	});

	$('.meta-box').transition({ delay:7000, opacity: 0}, 600, 'ease');

  };

})();