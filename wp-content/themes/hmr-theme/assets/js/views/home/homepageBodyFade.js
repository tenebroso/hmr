//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.homepageBodyFade = function() {
  	
    $('html').css('background','none');
	$('a').click(function (e) {
		$('html').css('background','#071222');
	});

  };

})();