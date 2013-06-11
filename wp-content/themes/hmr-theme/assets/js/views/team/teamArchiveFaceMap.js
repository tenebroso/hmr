//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.teamArchiveFaceMap = function() {

    $('.mertz').tooltipsy({
	  alignTo: 'element',
	  offset: [0, 1],
	  content: '<h4 class="caps">Bob Mertzlufft</h4><p><em>President</em></p>',
	  show: function (e, $el) {
	      $el.slideDown(500);
	  }
	});

	$('.heff').tooltipsy({
	  alignTo: 'element',
	  offset: [0, 1],
	  content: '<h4 class="caps">Bill Heffernan</h4><p><em>Creative Director</em></p>',
	  show: function (e, $el) {
	      $el.slideDown(500);
	  }
	});

  };

})();