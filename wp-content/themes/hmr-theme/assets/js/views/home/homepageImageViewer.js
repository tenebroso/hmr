//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.homepageImageViewer = function() {

    $.backstretch([
      "http://tenebroso.s3.amazonaws.com/CORPORATE-8.jpg", 
      "http://tenebroso.s3.amazonaws.com/ART-AND-GRAPHIC-16.jpg", 
      "http://tenebroso.s3.amazonaws.com/FABRIC-5.jpg",
      "http://tenebroso.s3.amazonaws.com/FABRIC-11.jpg",
      "http://tenebroso.s3.amazonaws.com/INNOVATION-13.jpg",
      "http://tenebroso.s3.amazonaws.com/SOCIAL-5.jpg",
      "http://tenebroso.s3.amazonaws.com/SOCIAL-16.jpg",
      "http://tenebroso.s3.amazonaws.com/WEDDINGS-2.jpg",
      "http://tenebroso.s3.amazonaws.com/WEDDINGS-22.jpg"
    ], {duration: 4000, fade: 1250});

    $('.forward').transition({ delay:750, opacity: 100, marginLeft: '100px'}, 500, 'ease');
    $('.thinkers').transition({ delay:1000, opacity: 100, marginLeft: '150px'}, 750, 'easeOutCirc');
    $('.forward').transition({ delay:3950, opacity: 0, marginLeft: '350px'}, 500, 'ease');
    $('.thinkers').transition({ delay:3950, opacity: 0, marginLeft: '350px'}, 500, 'easeOutCirc');


    $('.forward').transition({ delay:3750, opacity: 100, marginLeft: '100px'}, 500, 'ease');
    $('.thinkers').transition({ delay:3750, opacity: 100, marginLeft: '150px'}, 750, 'easeOutCirc');
    $('.forward').transition({ delay:7650, opacity: 0, marginLeft: '-50px'}, 500, 'ease');
    $('.thinkers').transition({ delay:7650, opacity: 0, marginLeft: '-80px'}, 500, 'easeOutCirc');

  };

})();