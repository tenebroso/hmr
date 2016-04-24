var HMR = HMR || {};

;(function() {

  HMR.conditionals = function() {

   //In order to tidy up the _main.js file, I added the random Page-based conditional scripts to this function

      // Call the portfolio gallery on the Portfolio Pages
      if($('.page-template-template-portfolio-php').length) {
        HMR.portfolioGallery();
        HMR.capabilityDescriptionFades();
      }

      // Add lightbox functionality to the Press Release and Testimonials Page
      if($('.page-template-template-press-php, page-template-template-video-php, page-template-template-web-php').length) {
        HMR.lightbox();
      }

      // Clear floats on the Capabilities page
      if($('.page-template-template-capabilities-php').length) {
        $('.first-row .span4:nth-child(4n+4)').addClass('clear');
         $('.first-row .span4:nth-child(3n+3)').addClass('no-right');
      }
      
      // Clear floats on Press Release & Testimonials Pages
      $('.page-template-template-press-php .content .row-fluid article:nth-of-type(4n+5)').addClass('clear');
      //$('.page-template-template-testimonials-php .content .row-fluid .span3:nth-child(4n+9)').addClass('clear');

      // Call the Team Hero face rollover function and clear floats. Also, remove the active state that WP adds to the blog
      if($('.post-type-archive-team').length) {
        //HMR.teamArchiveFaceMap();
        //HMR.teamSlide();
        $('article.span2:nth-child(6n+7)').addClass('clear');
        $('.menu-blog').removeClass('active');
      }

  };

})();
