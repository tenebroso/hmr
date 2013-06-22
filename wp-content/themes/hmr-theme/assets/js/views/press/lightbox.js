var HMR = HMR || {};
;(function() {

  HMR.lightbox = function() {

    var $modal = $('.modal');

    $modal.modal('hide');
    $modal.on('show', function () {
    	$('.modal-body',this).css({width:'auto',height:'auto', 'max-height':'100%'});
    	var width = $modal.width();
    	//$modal.css('marginLeft', function() { 
		    //return $modal.width()/2*-1;
		//});
	});

  };

})();