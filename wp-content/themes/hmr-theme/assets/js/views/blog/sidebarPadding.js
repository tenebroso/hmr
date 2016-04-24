var HMR = HMR || {};

;(function() {

  HMR.sidebarPadding = function() {

    var $firstTitleHeight = $('.post:first-of-type header'),
    	$sidebarWidth = $('.sidebar').width(),
    	$subscribeButton = $('#subscribe-submit input[type="submit"]'),
    	$sidebar = $('.sidebar');

    $sidebar.css({marginTop: '-24px'});

    $subscribeButton.addClass('btn btn-read-more');


    //http://andrewhenderson.me/tutorial/jquery-sticky-sidebar/

    if (!!$('.sticky').offset()) { // make sure ".sticky" element exists
 
	    var stickyTop = $('.sticky').offset().top; // returns number 
	 
	    $(window).scroll(function(){ // scroll event
	 
	      var windowTop = $(window).scrollTop(); // returns number 
	 
	      if (stickyTop < windowTop){
	        $('.sticky').css({ position: 'fixed', top: 0, paddingTop:'2em', width: $sidebarWidth });
	      }
	      else {
	        $('.sticky').css({position:'static',paddingTop:'0', width: ''});
	      }
	 
	    });
	 
	  }



  };

})();
