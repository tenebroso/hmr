var HMR = HMR || {};

;(function() {

  HMR.sidebarPadding = function() {

    var $firstTitleHeight = $('.post:first-of-type header');

    $('.sidebar').css({paddingTop: $firstTitleHeight.height()});

  };

})();