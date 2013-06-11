//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.teamArchiveFaceMap = function() {

    $('.hastip').tooltipsy({
	  alignTo: 'element',
	  offset: [0, 1],
	  content: '<h4 class="caps">Bob Mertzlufft</h4><p><em>President</em></p>',
	  show: function (e, $el) {
	      $el.slideDown(300);
	  }
	});

  };

})();