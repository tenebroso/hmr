//HMR.Home = "xxx";
 
 var HMR = HMR || {};

;(function() {

  HMR.homepageImageViewer = function() {

    $.backstretch([
      "/assets/CORPORATE-8.jpg"//, 
      //"/assets/ART-AND-GRAPHIC-16.jpg", 
      //"/assets/FABRIC-5.jpg",
      //"/assets/FABRIC-11.jpg",
      //"/assets/INNOVATION-13.jpg",
      //"/assets/SOCIAL-5.jpg",
      //"/assets/SOCIAL-16.jpg",
      //"/assets/WEDDINGS-2.jpg",
      //"/assets/WEDDINGS-22.jpg"
    ], {duration: 5000, fade: 1250});

    $('.forward').transition({ delay:1000, opacity: 100, marginLeft: '100px'}, 750, 'ease');
    $('.thinkers').transition({ delay:1250, opacity: 100, marginLeft: '150px'}, 1000, 'easeOutCirc');
    //$('.forward').transition({ delay:3950, opacity: 0, marginLeft: '350px'}, 500, 'ease');
    //$('.thinkers').transition({ delay:3950, opacity: 0, marginLeft: '350px'}, 500, 'easeOutCirc');


    //$('.forward2').transition({ delay:7500, opacity: 100, marginRight: '100px'}, 500, 'ease');
    //$('.thinkers2').transition({ delay:7500, opacity: 100, marginRight: '150px'}, 750, 'easeOutCirc');
    //$('.forward2').transition({ delay:3250, opacity: 0, marginLeft: '-50px'}, 500, 'ease');
    //$('.thinkers2').transition({ delay:3250, opacity: 0, marginLeft: '-80px'}, 500, 'easeOutCirc');

    //$('.forward3').transition({ delay:14250, opacity: 100, marginRight: '100px'}, 500, 'ease');
    //$('.thinkers3').transition({ delay:14250, opacity: 100, marginRight: '150px'}, 750, 'easeOutCirc');
    //$('.forward3').transition({ delay:3250, opacity: 0, marginLeft: '-50px'}, 500, 'ease');
    //$('.thinkers3').transition({ delay:3250, opacity: 0, marginLeft: '-80px'}, 500, 'easeOutCirc');

  };

})();