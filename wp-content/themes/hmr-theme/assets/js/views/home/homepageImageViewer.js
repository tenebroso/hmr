//HMR.Home = "xxx";
 
var HMR = HMR || {};

;(function() {

  HMR.homepageImageViewer = function() {

    /* var items = [
      { img: "/assets/home/homeSlide1.jpg", words: ".slide1"},
      { img: "/assets/home/homeSlide2.jpg", words: ".slide2"},
      { img: "/assets/home/homeSlide3.jpg", words: ".slide3"},
      { img: "/assets/home/homeSlide4.jpg", words: ".slide4"},
      { img: "/assets/home/homeSlide5.jpg", words: ".slide5"},
      { img: "/assets/home/homeSlide6.jpg", words: ".slide6"},
      { img: "/assets/home/homeSlide7.jpg", words: ".slide7"},
      { img: "/assets/home/homeSlide8.jpg", words: ".slide8"}
    ]; */

    var options = {
        fade: 1550,
        duration: 5000
    };

    var images = $.map(items, function(i) { return i.img; }),
        slideshow = $.backstretch(images,options),
        instance = $("body").data("backstretch"),
        //$loading = $('.loading'),
        $bigArrowLeft = $('.big_arrow.left'),
        $bigArrowRight = $('.big_arrow.right');

    $(window).on("backstretch.show", function(e, instance) {
        //var $getCurrent = $.backstretch.index;
        var $newCaption = items[instance.index].words;
        $($newCaption).transition({opacity: 1}, 1550);
        $('.backstretch').transition({ scale: 1.1 }, 10000);
        $('.backstretch').transition({delay:500, scale: 1 }, 10000);
    });

    $(window).on("backstretch.before", function(e, instance) {
        //$('.backstretch img').transition({ scale: 1 }, 10000);
        $('.img').transition({opacity: 0}, 1550);
        //$loading.transition({opacity:0});
    });

    $(window).on("backstretch.after", function(e, instance) {
        
        instance.resize();
    });

    // Handle keyboard - left/right arrow keys
    (function($){
        $(window).keyup(function(e){
            var key = e.which | e.keyCode;
            if(key === 37){ // 37 is left arrow
                slideshow.prev();
            }
            else if(key === 39){ // 39 is right arrow
                slideshow.next();
            }
        });
    })(jQuery);
    
     /*(function($){
        $(window).touchwipe({
            wipeLeft: function() {
                slideshow.prev();
            },
            wipeRight: function() {
                slideshow.next();
            }
        });
    })(jQuery);*/


    /* if($(window).width() > 767) {
        // Do the text anims
        //$('.homeSlide1').transition({delay:1500, opacity: 1}, 2550, 'ease');  
    } else {
        if(Modernizr.touch) $('.img').remove();
    }*/

    $bigArrowLeft.on('click', function() {
       slideshow.prev();
    });

    $bigArrowRight.on('click', function() {
       slideshow.next();
    });
 

  };

})();