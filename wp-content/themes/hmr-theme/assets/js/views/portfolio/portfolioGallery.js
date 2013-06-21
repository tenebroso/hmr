var HMR = HMR || {};

;(function() {

  HMR.portfolioGallery = function() {

    // Declare variables
    var $thumbs = $('.slide_thumb'),
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

        // Run the first time
        changeBackgroundImage( $($thumbs).eq(0).data('img') );
        
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
    };
    
    
    // Call the start up!
    initialize();

  };

})();