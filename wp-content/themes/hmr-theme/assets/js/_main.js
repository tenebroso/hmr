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
      HMR.navMobileVersion();
      HMR.pageFading();
      $('#s').clearField();
    }
  },
  // Home page
  home: {
    init: function() {

      // The following call deal with fading in images/copy on the homepage
      HMR.homepageImageViewer();

      // Specific homepage based body fade
      HMR.homepageBodyFade();
    }
  },
  // Connect
  connect: {
    init: function() {

      // Uniform is a plugin to style the dropdown select in the form
      HMR.uniform();

      // Scroll to the form
      HMR.scrollForm();

      // Call the Google Map
      HMR.MapTest();

      //Clear floats on the Connect page, the points of contact section
      $('.pointsOfContact .points:nth-child(4n+5)').addClass('clear');
    }
  },
  single: {
    init: function() {

      HMR.sidebarPadding();
      $(".type-post").fitVids();

      // Call the capability detail slideshow & description fades
      if($('.single-capability').length) {
        HMR.capabilityBGSlideShow();
        HMR.capabilityDescriptionFades();
        $('.menu-capabilities').addClass('active');
        $('.menu-blog').removeClass('active');
      }

      // Add the active class to the nav on a individual bio pages
      if($('.single-team').length) {
        $('.menu li.menu-our-team').addClass('active');
        $('.menu-hmr').addClass('active');
        $('.menu-blog').removeClass('active');
        $(window).bind('hashchange', function () {
          // do some magic
        });
      }

      // White BG for Blog Pages
      if($('.single-post').length) {
        $('html').css('background-color','#fff');
      }
      
    }
  },
  page: {
    init: function() {
      HMR.subNavFixes();
      HMR.conditionals();
      if($('body.web').length) {
        $('.nav li.menu-media').addClass('active');
      }
      if($('body.video').length) {
        $('.nav li.menu-media').addClass('active');
      }
      if($('body.clients').length) {
        $('.nav li.menu-hmr').addClass('active');
      }
      if($('body.venues').length) {
        $('.nav li.menu-hmr').addClass('active');
      }
      if($('body.about').length) {
        $('.nav li.menu-hmr').addClass('active');
      }
      if($('body.social-and-gala').length) {
        $('.nav li.menu-gallery').addClass('active');
      }
      if($('body.celebrations').length) {
        $('.nav li.menu-gallery').addClass('active');
      }
      if($('body.corporate').length) {
        $('.nav li.menu-gallery').addClass('active');
      }
    }
  },
  blog: {
    init: function() {
      HMR.sidebarPadding();
      $(".type-post").fitVids();
      $('html').css('background-color','#fff');
    }
  },
  search: {
    init: function() {
      $('html').css('background-color','#fff');
    }
  },
  author: {
    init: function() {
      $('html').css('background-color','#fff');
    }
  },
  archive: {
    init: function() {
      HMR.conditionals();
      HMR.sidebarPadding();
      $(".type-post").fitVids();

      // White BG for Blog Pages
      if($('.category').length) {
        $('html').css('background-color','#fff');
      }
    }
  },
  about: {
    init: function() {
      $('html').css('background','none');
      var img = '/assets/history-bg.jpg';
      $("body").backstretch([img], { centeredX:false});
      var instance = $("body").data("backstretch");
      instance.resize();
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
