// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)

var ExampleSite = {
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
      $.backstretch("http://tenebroso.s3.amazonaws.com/CORPORATE-8.jpg");
      $('html').css('background','none');
      $('a').click(function() { 
        $('html').css('background','#071222');
      });
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
    }
  }
};

var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = ExampleSite;
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
