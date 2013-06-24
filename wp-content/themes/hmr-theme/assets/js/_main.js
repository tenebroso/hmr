// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)
// 

var HMR = window.HMR || {};

HMR.Site = {
  // All pages
  common: {
    init: function() {
      HMR.nav = {};
      HMR.navSlideToggle();
      $('#s').clearField();
      $("body").show();
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
      $(".scrollTop a").click(function(event){
        var isMeta = event.which === 115 || event.ctrlKey || event.metaKey || event.which === 19;
        if (!isMeta) {
          linkLocation = this.href;
          $("body").fadeOut(500, redirectPage);
          event.preventDefault();
          return false;
        }
      });
      function redirectPage() {
        window.location = linkLocation;
      }
      },
      finalize: function() { }
  },
  // Home page
  home: {
    init: function() {
      HMR.homepageImageViewer();
      HMR.homepageBodyFade();
    }
  },
  // Connect
  connect: {
    init: function() {
      HMR.uniform();
      HMR.MapTest();
      $('.pointsOfContact .points:nth-child(4n+5)').addClass('clear');
    }
  },
  //Calling these functions under "Single" since I can't call single-capability or single-team even though they will then be called on single blog post and single team member pages...
  single: {
    init: function() {
      HMR.capabilityBGSlideShow();
      HMR.capabilityDescriptionFades();
      $('.single-team .menu li.menu-our-team').addClass('active');
    }
  },
  //Calling these functions under "Page" since I can't call page-template-template-portfolio-php even though they will then be called on ALL pages...
  page: {
    init: function() {
      HMR.portfolioGallery();
      HMR.lightbox();
      $('.page-template-template-press-php .content .row-fluid .span3:nth-child(4n+9)').addClass('clear');
      $('.page-template-template-testimonials-php .content .row-fluid .span3:nth-child(4n+9)').addClass('clear');
    }
  },
  // Team archive
  archive: {
    init: function() {
      $('article.span2:nth-child(6n+7)').addClass('clear');
      HMR.teamArchiveFaceMap();
    }
  }
};

var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = HMR.Site;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {

    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });

    UTIL.fire('common', 'finalize');
  }
};

$(document).ready(UTIL.loadEvents);
