//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.capabilityBGSlideShow = function() {

	$('.slideshow').cycle({
		fx: 'fade',
		delay: 1000,
		speed:3000
	});

	$('.gallery-footer').cycle({
		fx: 'fade',
		delay: 1000,
		speed:3000
	});
	
  };

})();