//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.homepageImageViewer = function() {

    //
    // Write all code for homepage image viewer here
    //
    $.backstretch("http://tenebroso.s3.amazonaws.com/CORPORATE-8.jpg");

    $('html').css('background','none');

    $('a').click(function () {
      $('html').css('background','#071222');
    });

  };

})();