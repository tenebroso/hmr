var HMR = HMR || {};

;(function() {

  HMR.navSlideToggle = function() {

    var $slideToggle = $('.slideToggle'),
        $subNav = $('.sub-nav'),
        $banner = $('.banner'),
        sliderHeight = "30px",
        navIsDown = true;

    // Listen for click
    $slideToggle.on('click', function () {
      if(navIsDown) {
        slideUp();
      } else {
        slideDown();
      }
    });

    // Make nav slide down
    function slideDown () {
      navIsDown = true;
      $banner.slideDown(167);
      $subNav.removeClass('up').addClass('down');
      // $subNav.transition({y: 0}, 250, "ease");
      // $(".slideDown").transition({ y:0}, 250, "ease");
      $slideToggle.removeClass("slideDown");
      $slideToggle.addClass("slideUp"); 
    }

    function slideUp () {
      navIsDown = false;
      $banner.slideUp(167);
      $subNav.removeClass('down').addClass('up');
      // $subNav.transition({y: -9+'px'}, 250, "ease");
      // $(".slideUp").transition({ y:-14}, 250, "ease");
      $slideToggle.removeClass("slideUp");
      $slideToggle.addClass("slideDown");
    }

    //if($('.page-template-template-portfolio-php, .single-capability').length) {
      //$('.scrollTop').hover(
        //function () {
          //slideDown();
        //},
        //function () {
          //slideUp();
        //}
      //);
    //}
    
    
    HMR.nav.slideUp = slideUp;
    HMR.nav.slideDown = slideDown;

    


  };

})();