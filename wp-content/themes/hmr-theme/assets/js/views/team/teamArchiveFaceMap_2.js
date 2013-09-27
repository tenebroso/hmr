//HMR.Home = "xxx";
 
 var HMR = HMR || {};
/**
 * teamArchiveFaceMap – SAMPLE REWRITE
 * I went ahead and rewrote this to make it a bit easier to edit/update.
 * Please note the dependencies you will need to install to make this work!!!!
 * 1. Lodash - https://raw.github.com/bestiejs/lodash/v1.3.1/dist/lodash.min.js
 * 2. HandlebarsJS – https://raw.github.com/wycats/handlebars.js/1.0.0/dist/handlebars.js
 */
;(function() {

  HMR.teamArchiveFaceMap = function() {

		var config = {
			people: [
				{
					el: '.mertz',
					name: 'Bob Merzlufft',
					title: 'President',
					url: '/team/bob-mertzlufft/'
				},
				{
					el: '.heff',
					name: 'Bill Heffernan',
					title: 'Creative Director',
					url: '/team/bill-heffernan/'
				},
				{
					el: '.ruben',
					name: 'Burt Rubenstein',
					title: 'Senior Event Designer',
					url: '/team/burt-rubenstein/'
				},
				{
					el: '.ahr',
					name: 'Brittanie Ahrens',
					title: 'Corporate Event Designer',
					url: '/team/brittanie-ahrens/'
				},
				{
					el: '.patel',
					name: 'Rishi Patel',
					title: 'Vice President of Sales and Design',
					url: '/team/rishi-patel/'
				},
				{
					el: '.crum',
					name: 'Amy Crum',
					title: 'Senior Event Designer',
					url: '/team/amy-crum/'
				},
				{
					el: '.schr',
					name: 'Larissa Schroeder',
					title: 'Weekly Floral Design Manager',
					url: '/team/larissa-schroeder/'
				},
				{
					el: '.hens',
					name: 'John Hensel',
					title: 'Senior Event Designer',
					url: '/team/john-hensel/'
				},
				{
					el: '.grif',
					name: 'Jessica Griffin',
					title: 'Event Designer',
					url: '/team/jessica-griffin/'
				},
				{
					el: '.eps',
					name: 'David Epstein',
					title: 'Corporate Event Director',
					url: '/team/david-epstein/'
				},
				{
					el: '.mil',
					name: 'Carli Milstein',
					title: 'Event Designer',
					url: '/team/carli-milstein/'
				},
				{
					el: '.ley',
					name: 'Carolyn Leyba',
					title: 'Event Producer',
					url: '/team/carolyn-leyba/'
				},
				{
					el: '.mills',
					name: 'Chris Mills',
					title: 'Event Producer',
					url: '/team/chris-mills/'
				},
				{
					el: '.silver',
					name: 'Rachel Silverberg',
					title: 'Event Producer',
					url: '/team/rachel-silverberg/'
				},
				{
					el: '.tucker',
					name: 'Christianna Tucker',
					title: 'Event Producer',
					url: '/team/christianna-tucker/'
				}
			],
			settings: {
				slideTime: 167,
				alignTo: 'element',
				offset: [0,1]
			}
		},
		templateString = '<h4 class="caps"><a href="{{url}}">{{name}}</a></h4><p><em>{{title}}</em></p>',
		template = Handlebars.compile(templateString),
		initialize;


		//
		// Initialize operations
		// 
		initialize = function () {
			_.each(config.people, function (person) {
				$(person.el).tooltipsy({
					alignTo: config.settings.element,
					offset: [0, 1],
					content: template(person),
					show: function (e, $el) {
				        $el.css({
				            'top': parseInt($el[0].style.top.replace(/[a-z]/g, '')) - 30 + 'px',
				            'opacity': '0.0',
				            'display': 'block'
				        }).animate({
				            'top': parseInt($el[0].style.top.replace(/[a-z]/g, '')) + 30 + 'px',
				            'opacity': '1.0'
				        }, 300);
				    },
				    hide: function (e, $el) {
				        $el.slideUp(100);
				    }
				});
			});
		};


		// Call initialize method when ready to begin
		initialize();

  };

})();