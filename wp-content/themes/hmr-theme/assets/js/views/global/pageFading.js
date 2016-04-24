var HMR = HMR || {};

;(function() {

  HMR.pageFading = function() {

    function displayPage() {
      //$("body").css("display", "none");
      $("body").fadeIn(500);
    }

    window.addEventListener("load", displayPage, false);
    window.addEventListener("unload", displayPage, false);

   $("body").show();
      $(".scrollTop a, .post-type-archive-team a, .back a").click(function(event){
        var isMeta = event.which === 115 || event.ctrlKey || event.metaKey || event.which === 19 || $('.active.menu-team');
        if (!isMeta) {
          linkLocation = this.href;
          $("body").fadeOut(500, redirectPage);
          return false;
        }
      });
      function redirectPage() {
        window.location = linkLocation;
      }

  };

})();
