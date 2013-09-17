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

	var $slideshow = $('.slideshow');

	$('.photo-credit').cycle({
		fx: 'fade',
		delay: 1000,
		speed:3000
	});

	/*$(".slideshow").touchwipe({
		wipeLeft: function() {
			$(".slideshow").cycle("next");
	    },
	    wipeRight: function() {
	        $(".slideshow").cycle("prev");
	    }
	});*/

var slideIndex = 0;
var nextIndex = 0;
var prevIndex = 0;

$slideshow.cycle({
    fx: 'fade',//fx: 'scrollHorz', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
    delay: 1000,
	speed:1000,
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
	            $slideshow.cycle(nextIndex, "fade");
	        }
	        else if(key === 39){ // 39 is right arrow
	            $slideshow.cycle(prevIndex, "fade");
	        }
	    });
	})(jQuery);

	// Handle swipe
    (function($){
        $(window).touchwipe({
            wipeLeft: function() {
                $slideshow.cycle(nextIndex, "fade");
            },
            wipeRight: function() {
                $slideshow.cycle(prevIndex, "fade");
            }
        });
    })(jQuery);


  };

})();