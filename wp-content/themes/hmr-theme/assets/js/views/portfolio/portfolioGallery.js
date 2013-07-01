var HMR = HMR || {};

/*
* Sample HTML
* <div data-dir="next"></div> or <div data-dir="prev"></div>
*
*/


;(function() {

  HMR.portfolioGallery = function() {

    if($(window).width() > 767) {

        HMR.nav.slideUp();

    }

    // Declare variables
    var $thumbs = $('.slide_thumb'),
        $bigArrows = $('.big_arrow'),
        $nav = $('.thumb_nav'),
        $credit = $('.photographer'),
        current = -1, // This will track our curretly active thumb
        len = $thumbs.length, // This is the # of thubmnails total
        changeBackgroundImage,
        changeFooterCredit,
        timer, startTimer, initialize, changeIt;

    var $slider = $($nav).bxSlider({
        pager:false, 
        minSlides:12, 
        maxSlides:12, 
        moveSlides:1, 
        slideMargin:1,
        slideWidth:70,
        onSlideBefore: function(_$el, _oldIndex, _newIndex) {
            $('.active').removeClass('active');
            _$el.addClass('active');
            changeIt(_$el);
        }//,
        // infiniteLoop: false//,
        // onSlideAfter: function(el, old, _new) {
        //     console.log(el);
        //     console.log(old);
        //     console.log(_new);            
        // }
    });


    // $slid            
    
    
    // This will swap out the backgrund image
    changeBackgroundImage = function (img, duration) {
        $.backstretch([img],{ fade: duration}).resize();
    };

    // This will swap out the photo credit
    changeFooterCredit = function (active) {
        $('.id-' + (active)).transition({opacity:1}, 750, 'ease');
    };

    loadFirstImage = function () {
        current = 0;
        changeBackgroundImage( $thumbs.eq(current).data('img'), 500 );
        $thumbs.eq(current).addClass('active');
    };
    
    
    // Fire up our timer
    startTimer = function() {
        timer = window.setInterval(function() {
            current++;
            if(current > len-1) current = 0;
            changeBackgroundImage( $thumbs.eq(current).data('img'), 500 );
            $thumbs.removeClass('active');
            $thumbs.eq(current).addClass('active');
            $slider.goToNextSlide();
        }, 6000);
    };

    
    
    // Start this view!
    initialize = function () {

        //hmr.nav.shrink();

        // Run the first time
        // changeBackgroundImage( $thumbs.eq(0).data('img') );
        // $thumbs.eq(0).addClass('active');

        loadFirstImage();

        // Start timer
        startTimer();


        changeIt = function (_$el) {
            if(timer) {
                window.clearInterval(timer);
                timer = null;
            }

            $credit.transition({opacity:0}, 250, 'ease');
            changeFooterCredit(_$el.data('id'));
            changeBackgroundImage(_$el.data('img'), 500);            
        }

        // Handle clicks on ('.slide_thumb') elements
        $('.thumb_nav').on('click', '.slide_thumb', function() {
            var _id = $(this).data('id');
        
            // if(timer) {
            //     window.clearInterval(timer);
            //     timer = null;
            // }

            $slider.goToSlide(_id);

            // $credit.transition({opacity:0}, 250, 'ease');
            // changeFooterCredit($(this).data('id'));
            // changeBackgroundImage($(this).data('img'), 500);
        });    
        
        
        // Handle click of big arrows
        $bigArrows.on('click', function() {
           
           var endLen = len-1,
                oldCur = current;

            // if(timer) {
            //     window.clearInterval(timer);
            //     timer = null;
            // }           
           
           if($(this).data('dir') === 'next') {
               current++;
               $slider.goToNextSlide();
           } else {
               current--;
               $slider.goToPrevSlide();
           }
           
           // $('.active').removeClass('active');
           
            // if(current > endLen) current = 0;
            // if(current < 0) current = endLen;

            // $thumbs.eq(current).addClass('active');

            // $credit.transition({opacity:0}, 250, 'ease');
            // changeFooterCredit($($thumbs.eq(current)).data('id'));
            // changeBackgroundImage( $thumbs.eq(current).data('img'), 500 );
            
        });    
    };
    
    
    // Call the start up!
    initialize();

  };

})();