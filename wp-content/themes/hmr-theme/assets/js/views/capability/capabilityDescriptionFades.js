//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.capabilityDescriptionFades = function() {

	$('.meta-box').transition({ delay:7000, opacity: 0}, 2000, 'ease');
	$('.gallery-title').transition({ delay:7000, opacity: 100}, 2000, 'ease');
	
	$(".meta-box").hover(
	  function () {
	    $(this).transition({ opacity: 100}, 1000, 'ease').transition({ delay:4000, opacity: 0}, 1000, 'ease');
	    $('.gallery-title').transition({ opacity: 0}, 1000, 'ease').transition({ delay:4000, opacity: 100}, 1000, 'ease');
	  },
	  function () {
	    
	  }
	);

	$(".gallery-title").hover(
	  function () {
	    $('.meta-box').transition({ opacity: 100}, 1000, 'ease').transition({ delay:4000, opacity: 0}, 1000, 'ease');
	    $(this).transition({ opacity: 0}, 1000, 'ease').transition({ delay:4000, opacity: 100}, 1000, 'ease');
	  },
	  function () {
	    
	  }
	);


	
  };

})();