//HMR.Home = "xxx";
 
var HMR = HMR || {};

;(function() {

  HMR.homepageImageViewer = function() {


    // Image path
    var img = '/assets/CORPORATE-8.jpg';
    
    
    // Setup backstretch
    $.backstretch([img]);

    if($(window).width > 767) {
        // Do the text anims
        $('.forward').transition({delay:1000, opacity: 1, marginLeft: '100px'}, 750, 'ease');
        $('.thinkers').transition({ delay:1250, opacity: 1, marginLeft: '150px'}, 1000, 'easeOutCirc');    
    } else {
        if(Modernizr.touch) $('.thinkers, .forward').remove();
    }
 

  };

})();