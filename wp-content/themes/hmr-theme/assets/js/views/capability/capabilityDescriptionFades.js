//HMR.Home = "xxx";

 var HMR = HMR || {};

;(function() {

  HMR.capabilityDescriptionFades = function() {


  	if($(window).width() > 767) {

		$('.single-capability .meta-box').transition({ delay:6000, opacity: 0}, 2000, 'ease');
		$('.gallery-title').transition({ delay:7000, opacity: 100}, 2000, 'ease');
		
		$(".single-capability .meta-box").hover(
		  function () {
		    $(this).transition({ opacity: 100}, 1000, 'ease');
		  },
		  function () {
		     $(this).transition({ opacity: 0}, 1000, 'ease');
		  }
		); 

		$(".gallery-title").hover(
		  function () {
		    $('.meta-box').transition({ opacity: 100}, 1000, 'ease');
		  },
		  function () {
		    $('.meta-box').transition({ opacity: 0}, 1000, 'ease');
		  }
		);

	}


	
  };

})();