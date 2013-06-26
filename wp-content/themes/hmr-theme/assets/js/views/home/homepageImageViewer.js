//HMR.Home = "xxx";
 
var HMR = HMR || {};

;(function() {

  HMR.homepageImageViewer = function() {


    // Image path
    var img = '/assets/CORPORATE-8.jpg';
    
    
    // Setup backstretch
    $("body").backstretch([img]);

    var instance = $("body").data("backstretch");
    instance.resize();

    // Use in case we want something to happen only after backstretch runs. Placeholder for now.
    //$(window).on("backstretch.after", function (e, instance, index) {
      // Do something
    //});


    if($(window).width() > 767) {
        // Do the text anims
        $('.forward').transition({delay:1000, opacity: 1, x: '100px'}, 750, 'ease');
        $('.thinkers').transition({ delay:1250, opacity: 1, x: '150px'}, 1000, 'easeOutCirc');    
    } else {
        if(Modernizr.touch) $('.thinkers, .forward').remove();
    }
 

  };

})();