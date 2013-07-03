var HMR = HMR || {};

;(function() {

  HMR.subNavFixes = function() {

    //This view basically uses JS to add the missing "active" class to parent nav elements when on sub-pages

      if($('body.history').length) {
         $('.menu-hmr').addClass('active');
      }
      if($('body.clients').length) {
         $('.menu-service').addClass('active');
      }
      if($('body.venues').length) {
         $('.menu-service').addClass('active');
      }
      if($('body.testimonials').length) {
         $('.menu-service').addClass('active');
      }

  };

})();