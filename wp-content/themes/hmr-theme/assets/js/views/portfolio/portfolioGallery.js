var HMR = HMR || {};

/*
* Sample HTML
* <div data-dir="next"></div> or <div data-dir="prev"></div>
*
*/


;(function() {

  HMR.portfolioGallery = function() {

    // Declare variables
    var $thumbs = $('.slide_thumb'),
        $bigArrows = $('.big_arrow'),
        $leftArrow = $('.left_arrow'),
        $rightArrow = $('.right_arrow'),
        $nav = $('.thumb_nav'),
        current = 0, // This will track our curretly active thumb
        len = $thumbs.length, // This is the # of thubmnails total
        changeBackgroundImage,
        timer, startTimer, initialize;
    
    
    // This will swap out the backgrund image
    changeBackgroundImage = function (img) {
        $.backstretch([img],{ fade: 750});
    };
    
    
    // Fire up our timer
    startTimer = function() {
        timer = window.setInterval(function() {
            current++;
            if(current > len-1) current = 0;
            
            $($thumbs).eq(current).data('img');
        }, 1000);
    };

    
    
    // Start this view!
    initialize = function () {

        //hmr.nav.shrink();

        $leftArrow.on('click', function() {
            $nav.transition({x: '-72px'}, 750, 'ease');
        }); 

        $rightArrow.on('click', function() {
            $nav.transition({x: '72px'}, 750, 'ease');
        }); 

        // Run the first time
        changeBackgroundImage( $thumbs.eq(0).data('img') );
        
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
    
            changeBackgroundImage($(this).data('img'));
        });    
        
        
        // Handle click of big arrows
        $bigArrows.on('click', function() {
           
           var endLen = len-1;
           
           if($(this).data('dir') === 'next') {
               current++;
           } else {
               current--;
           }
           
            if(current > endLen) current = 0;
            if(current < 0) current = endLen;
            
            changeBackgroundImage( $thumbs.eq(current).data('img') );
           
        });    
    };
    
    
    // Call the start up!
    initialize();

  };

})();