//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {


  HMR.capabilityBGSlideShow = function() {

  	if($(window).width() > 767) {

  		HMR.nav.slideUp();

  	}

	/*$('.slideshow').cycle({
		fx: 'fade',
		delay: 1000,
		speed:3000,
		containerResize: false,
		slideResize: false,
		fit: 1,
		next:   '#next', 
      	prev:   '#prev'
	});*/

	var $slideshow = $('.slideshow'),
		$bigArrowLeft = $('.big_arrow.left'),
		$bigArrowRight = $('.big_arrow.right'),
		$credit = $('.gallery-footer');

	

	/*$(".slideshow").touchwipe({
		wipeLeft: function() {
			$(".slideshow").cycle("next");
	    },
	    wipeRight: function() {
	        $(".slideshow").cycle("prev");
	    }
	});*/

var slideIndex = 0,
	nextIndex = 0,
	prevIndex = 0,
	creditIndex = 0,
	nextCreditIndex = 0,
	prevCreditIndex = 0;

$credit.cycle({
	fx: 'fade',
	delay: 1000,
	speed: 1000,
	after: function(currCreditElement, nextCreditElement, options) {
        creditIndex = options.currSlide;
        nextCreditIndex = creditIndex + 1;
        prevCreditIndex = creditIndex -1;

        if (creditIndex == options.slideCount-1) {
            nextCreditIndex = 0;
        }

        if (creditIndex == 0) {
            prevCreditIndex = options.slideCount-1;
        }
    }
});

$slideshow.cycle({
    fx: 'fade',//fx: 'scrollHorz', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
    delay: 1000,
	speed: 1000,
	containerResize: false,
	slideResize: false,
	fit: 1,
    after: function(currSlideElement, nextSlideElement, options) {
        slideIndex = options.currSlide;
        nextIndex = slideIndex + 1;
        prevIndex = slideIndex -1;

        if (slideIndex == options.slideCount-1) {
            nextIndex = 0;
        }

        if (slideIndex == 0) {
            prevIndex = options.slideCount-1;
        }
    }
});

	(function($){
	    $(window).keyup(function(e){
	        var key = e.which | e.keyCode;
	        if(key === 37){ // 37 is left arrow
	            $slideshow.cycle(prevIndex, "fade");
	            $credit.cycle(prevCreditIndex, "fade");
	            $slideshow.cycle('toggle');
	            $credit.cycle('toggle');
	        }
	        else if(key === 39){ // 39 is right arrow
	            $slideshow.cycle(nextIndex, "fade");
	            $credit.cycle(nextCreditIndex, "fade");
	            $slideshow.cycle('toggle');
	            $credit.cycle('toggle');
	        }
	    });
	})(jQuery);

	// Handle swipe
    (function($){
        $(window).touchwipe({
            wipeLeft: function(e) {
            	e.preventDefault();
                $slideshow.cycle(prevIndex, "fade");
	            $credit.cycle(prevCreditIndex, "fade");
            },
            wipeRight: function(e) {
            	e.preventDefault();
                $slideshow.cycle(nextIndex, "fade");
	            $credit.cycle(nextCreditIndex, "fade");
            },
            preventDefaultEvents: false
        });
    })(jQuery);

    $bigArrowLeft.on('click', function() {
       $slideshow.cycle(prevIndex, "fade");
       $credit.cycle(prevCreditIndex, "fade");
    });

    $bigArrowRight.on('click', function() {
       $slideshow.cycle(nextIndex, "fade");
       $credit.cycle(nextCreditIndex, "fade");
    });

  };

})();