//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.homepageImageViewer = function() {

    //
    // Write all code for homepage image viewer here
    //
    $.backstretch("http://tenebroso.s3.amazonaws.com/CORPORATE-8.jpg");

    $('.forward').transition({ delay:750, opacity: 100, marginLeft: '100px'}, 500, 'ease');
    $('.thinkers').transition({ delay:1000, opacity: 100, marginLeft: '150px'}, 750, 'easeOutCirc');

  };

})();