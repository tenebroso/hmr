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
        $('.forward').transition({delay:1500, opacity: 1, x: '200px'}, 2550, 'ease');
        $('.thinkers').transition({ delay:3250, opacity: 1, x: '250px'}, 3550, 'easeOutCirc');    
    } else {
        if(Modernizr.touch) $('.thinkers, .forward').remove();
    }
 

  };

})();