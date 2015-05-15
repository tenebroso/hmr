var HMR = HMR || {};

/*
* Sample HTML
* <div data-dir="next"></div> or <div data-dir="prev"></div>
*
*/


;(function() {

var win = window,
    $win = $(win);

  HMR.portfolioGallery = function() {

    if($win.width() > 767) {

        HMR.nav.slideUp();

    }

    // Declare variables
    var $thumbs = $('.slide_thumb'),
        $bigArrows = $('.big_arrow'),
        $nav = $('.thumb_nav'),
        $venue = $('.venue'),
        $url = $('.photographer a'),
        $credit = $('.photographer'),
        current = -1, // This will track our curretly active thumb
        len = $thumbs.length, // This is the # of thubmnails total
        changeBackgroundImage,
        changeFooterCredit,
        timer, startTimer, initialize, changeIt;

    var $slider = $nav.bxSlider({
        pager:false, 
        minSlides:12, 
        maxSlides:12, 
        moveSlides:1, 
        slideMargin:1,
        slideWidth:70,
        onSlideBefore: function(_$el, _oldIndex, _newIndex) {
            $('.slide_thumb.active').removeClass('active');
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


    var $sliderParent = $('.bx-wrapper');          
    
    
    // This will swap out the backgrund image
    changeBackgroundImage = function (img, duration) {
        $.backstretch([img],{ fade: duration}).resize();
    };

    // This will swap out the photo credit
    changeFooterCredit = function (active) {
        $('.id-' + (active)).transition({opacity:1}, 750, 'ease').addClass('show');
        $('.idVenue-' + (active)).transition({opacity:1}, 750, 'ease');
        $('.url-' + (active)).transition({opacity:1}, 750, 'ease');
    };

    loadFirstImage = function () {
        current = 0;
        changeBackgroundImage( $thumbs.eq(current).data('img'), 500 );
        $thumbs.eq(current).addClass('active');
        $('.id-0').transition({opacity:1}, 750, 'ease').addClass('show');
        $('.url-0').transition({opacity:1}, 750, 'ease');
        $('.idVenue-0').transition({opacity:1}, 750, 'ease');
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
            $sliderParent.transition({ opacity: 0}, 1000, 'ease');
        }, 6000);
    };

    
    
    // Start this view!
    initialize = function () {

        //hmr.nav.shrink();

        // Run the first time
        // changeBackgroundImage( $thumbs.eq(0).data('img') );
        // $thumbs.eq(0).addClass('active');

        $($sliderParent).hover(
            function () {
              $(this).transition({ opacity: 100}, 100, 'ease');
            },
            function () {
              $(this).transition({ opacity: 0}, 500, 'ease');
            }
        );

        loadFirstImage();

        $('.js-start-slideshow').click(function(){
            startTimer();
            $slider.goToNextSlide();
            $('.meta-box').transition({ opacity: 0}, 2000, 'ease');
        })

        // Start timer
        


        changeIt = function (_$el) {
            /*if(timer) {
                window.clearInterval(timer);
                timer = null;
            }*/
            $url.transition({opacity:0}, 250, 'ease');
            $venue.transition({opacity:0}, 250, 'ease');
            $credit.transition({opacity:0}, 250, 'ease').removeClass('show');
            changeFooterCredit(_$el.data('id'));
            changeBackgroundImage(_$el.data('img'), 500);            
        }

        // Handle clicks on ('.slide_thumb') elements
        $('.thumb_nav').on('click', '.slide_thumb', function() {
            var _id = $(this).data('id');
        
            killTimer();

            $slider.goToSlide(_id);

            // $credit.transition({opacity:0}, 250, 'ease');
            // changeFooterCredit($(this).data('id'));
            // changeBackgroundImage($(this).data('img'), 500);
        });

        // Handle keyboard - left/right arrow keys
        $win.keyup(function(e){
            var key = e.which | e.keyCode;
            if(key === 37){ // 37 is left arrow
                killTimer();
                current--;
                $slider.goToPrevSlide();
            }
            else if(key === 39){ // 39 is right arrow
                killTimer();
                current++;
                $slider.goToNextSlide();
            }
        });

        // Handle swipe
        $win.touchwipe({
            wipeLeft: function(e) {
                e.preventDefault();
                killTimer();
                current++;
                $slider.goToNextSlide();
            },
            wipeRight: function(e) {
                e.preventDefault();
                killTimer();
                current--;
                $slider.goToPrevSlide();
            },
            preventDefaultEvents: false
        });
        
        
        // Handle click of big arrows
        $bigArrows.on('click', function() {
           
           var endLen = len-1,
                oldCur = current;

            killTimer();           
           
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

    function killTimer() {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    }
    
    
    // Call the start up!
    initialize();

  };

})();