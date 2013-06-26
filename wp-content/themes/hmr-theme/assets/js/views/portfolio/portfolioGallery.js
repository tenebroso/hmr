var HMR = HMR || {};

/*
* Sample HTML
* <div data-dir="next"></div> or <div data-dir="prev"></div>
*
*/


;(function() {

  HMR.portfolioGallery = function() {

    //HMR.nav.slideUp();

    // Declare variables
    var $thumbs = $('.slide_thumb'),
        $bigArrows = $('.big_arrow'),
        $nav = $('.thumb_nav'),
        $credit = $('.photographer'),
        current = -1, // This will track our curretly active thumb
        len = $thumbs.length, // This is the # of thubmnails total
        changeBackgroundImage,
        changeFooterCredit,
        timer, startTimer, initialize;

    $($nav).bxSlider({
        pager:false, 
        minSlides:12, 
        maxSlides:12, 
        moveSlides:1, 
        slideMargin:1,
        slideWidth:70
    });
    
    
    // This will swap out the backgrund image
    changeBackgroundImage = function (img, duration) {
        $.backstretch([img],{ fade: duration}).resize();
    };

    // This will swap out the photo credit
    changeFooterCredit = function (active) {
        $('.id-' + (active)).transition({opacity:1}, 750, 'ease');
    };
    
    
    // Fire up our timer
    startTimer = function() {
        timer = window.setInterval(function() {
            current++;
            if(current > len-1) current = 0;
            changeBackgroundImage( $thumbs.eq(current).data('img'), 1000 ).resize();
            $thumbs.removeClass('active');
            $thumbs.eq(current).addClass('active');            
        }, 6000);
    };

    
    
    // Start this view!
    initialize = function () {

        //hmr.nav.shrink();

        // Run the first time
        // changeBackgroundImage( $thumbs.eq(0).data('img') );
        // $thumbs.eq(0).addClass('active');

        // Start timer
        startTimer();

        // Handle clicks on ('.slide_thumb') elements
        $thumbs.on('click', function() {

            $thumbs.removeClass('active');
            $(this).addClass('active');
        
            if(timer) {
                window.clearInterval(timer);
                timer = null;
            }
            $credit.transition({opacity:0}, 250, 'ease');
            changeFooterCredit($(this).data('id'));
            changeBackgroundImage($(this).data('img'), 1000).resize();
        });    
        
        
        // Handle click of big arrows
        $bigArrows.on('click', function() {
           
           var endLen = len-1;

            if(timer) {
                window.clearInterval(timer);
                timer = null;
            }           
           
           if($(this).data('dir') === 'next') {
               current++;
           } else {
               current--;
           }
           
            if(current > endLen) current = 0;
            if(current < 0) current = endLen;
            
            changeBackgroundImage( $thumbs.eq(current).data('img'), 1000 ).resize();
            $thumbs.removeClass('active');
            $thumbs.eq(current).addClass('active');
           
        });    
    };
    
    
    // Call the start up!
    initialize();

  };

})();