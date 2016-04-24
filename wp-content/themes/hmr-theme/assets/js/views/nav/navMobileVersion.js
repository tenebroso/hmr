var HMR = HMR || {};

;(function() {

  HMR.navMobileVersion = function() {

    var navigation = responsiveNav("#navbar-mobile", {
        animate: true,
        openPos: "relative",
        transition:200,
        open: function () { 
          $('#nav-toggle').addClass('opened');
        },
        close: function() { 
          $('#nav-toggle').removeClass('opened');
        }
    });

  };

})();
