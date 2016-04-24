var HMR = HMR || {};


;(function() {

  HMR.teamSlide = function() {

  jQuery.fn.animateAuto = function(prop, speed, callback){
    var elem, height, width;
    return this.each(function(i, el){
        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo("body");
        height = elem.css("height"),
        width = elem.css("width"),
        elem.remove();
        
        if(prop === "height")
            el.animate({"height":height}, speed, callback);
        else if(prop === "width")
            el.animate({"width":width}, speed, callback);  
        else if(prop === "both")
            el.animate({"width":width,"height":height}, speed, callback);
    });  
}

  	var $btn = $('.btn'),
        $menu = $('.active.menu-team a');
  		  $team = $('#team'),
  		  $hero = $('#team-photo'),
  		  $members = $('#team-members'),
        windowSize = $(window).width();

  $(window).resize(function() {
    if(windowSize !== $(window).width()){
    location.reload();
    return;
    }
  });


  if (windowSize <= 767) {
    $team.css('height','auto');
    $team.css('overflow','visible');
  }
  else if (windowSize <= 1000) {
      $btn.on('click', function () {
        $hero.transition({y:'-570px'});
        $members.transition({y:'-570px'});
        //$team.css('height','auto');
        $team.animateAuto("height", 1000);
        $team.css('overflow','visible');
        //$('body').css('overflow','hidden');
      });

      $menu.on('click', function (e) {
        $hero.transition({y:0});
        $members.transition({y:0});
        //$team.css('height','540px');
        $team.animate({height: "570px"}, 100)
        $team.css('overflow','hidden');
        e.preventDefault();
      });

      if(window.location.hash) {
        $hero.transition({y:'-570px'});
        $members.transition({y:'-570px'});
        //$team.css('height','auto');
        $team.animateAuto("height", 1000);
        $team.css('overflow','visible');
        //$('body').css('overflow','hidden');
      }

  }
  else if (windowSize >= 1001) {

      $btn.on('click', function () {
        $hero.transition({y:'-693px'});
        $members.transition({y:'-693px'});
        $team.animate({height: "2475px"}, 100)
        $team.css('overflow','visible');
      });

      $menu.on('click', function (e) {
        $hero.transition({y:0});
        $members.transition({y:0});
        $team.animate({height: "693px"}, 100)
        $team.css('overflow','hidden');
        e.preventDefault();
      });

      if(window.location.hash) {
        $hero.transition({y:'-693px'});
        $members.transition({y:'-693px'});
        $team.animate({height: "2475px"}, 100)
        $team.css('overflow','visible');
      }
  }


  };

})();
