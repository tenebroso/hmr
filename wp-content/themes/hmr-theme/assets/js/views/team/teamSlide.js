var HMR = HMR || {};


;(function() {

  HMR.teamSlide = function() {

  	var $btn = $('.btn'),
        $menu = $('.active.menu-team a');
  		  $team = $('#team'),
  		  $hero = $('#team-photo'),
  		  $members = $('#team-members');

  if($(window).width() > 767) {

  	$btn.on('click', function () {
      $hero.transition({y:'-663px'});
      $members.transition({y:'-663px'});
      $team.css('height','auto');
      $team.css('overflow','visible');
    });

    $menu.on('click', function (e) {
      $hero.transition({y:0});
      $members.transition({y:0});
      $team.css('height','663px');
      $team.css('overflow','hidden');
      e.preventDefault();
    });

  } else {
    $team.css('height','auto');
    $team.css('overflow','visible');
  }


  };

})();