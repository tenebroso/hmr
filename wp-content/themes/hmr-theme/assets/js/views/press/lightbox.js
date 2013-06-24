var HMR = HMR || {};
;(function() {

  HMR.lightbox = function() {

    var $modal = $('.modal');
    var $modalBody = $('.modal-body');
    var $height = $(window).height();
    var screenImage = $(".img");

    // Create new offscreen image to test
    var theImage = new Image();
    theImage.src = screenImage.attr("src");

    // Get accurate measurements from that.
    var imageWidth = theImage.width;
    var imageHeight = theImage.height;

    $modal.modal('hide');

    $modal.on('show', function () {
    	$('.modal-body',this).css({width:'auto',height:'auto', 'max-height':'100%'});
    	$modalBody.css('height', function() { 
		      return $height/1.3;
		    });
	});

  };

})();