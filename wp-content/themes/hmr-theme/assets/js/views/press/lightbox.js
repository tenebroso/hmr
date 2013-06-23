var HMR = HMR || {};
;(function() {

  HMR.lightbox = function() {

    var $modal = $('.modal');
    var $modalBody = $('.modal-body');
    var $height = $(window).height();

    $modal.modal('hide');

    $modal.on('show', function () {
    	$('.modal-body',this).css({width:'auto',height:'auto', 'max-height':'100%'});
    	$modalBody.css('height', function() { 
		    return $height/1.3;
		});
	});

  };

})();