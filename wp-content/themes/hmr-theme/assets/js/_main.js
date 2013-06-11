// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)
// 

var HMR = window.HMR || {};

HMR.Site = {
  // All pages
  common: {
    init: function() {
      $('#s').clearField();
 
      $("body").show();

      $("a").click(function(event){
        event.preventDefault();
        linkLocation = this.href;
        $("body").fadeOut(500, redirectPage);      
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
    }
  },
  // About page
  about: {
    init: function() {
      // JS here
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
