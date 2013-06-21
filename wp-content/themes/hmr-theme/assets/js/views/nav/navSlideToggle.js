var HMR = HMR || {};

;(function() {

  HMR.navSlideToggle = function() {

    var sliderHeight = "30px";
 
        $(".scrollTop").each(function () 
            {
                var current = $(this);
                current.attr("box_h", current.height());
            }
        );
        $(".slideUp").click(function() { closeSlider() });
        $(".slideDown").click(function() { openSlider() });
     
     
    function openSlider()
     
    {
        var open_height = $(".scrollTop").attr("box_h") + "px";
        $(".scrollTop").animate({"height": open_height}, {duration:250 });
        $(".banner").slideDown(250);
        $(".sub-nav").transition({ y:0}, 250, "ease");
        $(".slideDown").transition({ y:0}, 250, "ease");
        $(".slideToggle").removeClass("slideUp");
        $(".slideToggle").addClass("slideDown");
    }
     
    function closeSlider()
     
    {
        $(".scrollTop").animate({"height": sliderHeight}, {duration: 250 });
        $(".banner").slideUp(250);
        $(".sub-nav").transition({ y:-9}, 250, "ease");
        $(".slideUp").transition({ y:-14}, 250, "ease");
        $(".slideToggle").removeClass("slideDown");
        $(".slideToggle").addClass("slideUp");
    }

  };

})();