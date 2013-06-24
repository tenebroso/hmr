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
					title: 'President'
				},
				{
					el: '.heff',
					name: 'Bill Heffernan',
					title: 'Creative Director'
				},
				{
					el: '.ruben',
					name: 'Burt Rubenstein',
					title: 'Senior Event Designer'
				},
				{
					el: '.ahr',
					name: 'Brittanie Ahrens',
					title: 'Event Designer'
				},
				{
					el: '.patel',
					name: 'Rishi Patel',
					title: 'Vice President of Sales and Design'
				},
				{
					el: '.crum',
					name: 'Amy Crum',
					title: 'Senior Event Designer'
				},
				{
					el: '.schr',
					name: 'Larissa Schroeder',
					title: 'Weekly Floral Design Manager'
				},
				{
					el: '.hens',
					name: 'John Hensel',
					title: 'Senior Event Designer'
				},
				{
					el: '.grif',
					name: 'Jessica Griffin',
					title: 'Event Designer'
				},
				{
					el: '.eps',
					name: 'David Epstein',
					title: 'Senior Event Designer'
				},
				{
					el: '.mil',
					name: 'Carli Milstein',
					title: 'Event Designer'
				}
			],
			settings: {
				slideTime: 167,
				alignTo: 'element',
				offset: [0,1]
			}
		},
		templateString = '<h4 class="caps">{{name}}</h4><p><em>{{title}}</em></p>',
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
						$el.slideDown(config.settings.slideTime);
					}
				});
			});
		};


		// Call initialize method when ready to begin
		initialize();

  };

})();