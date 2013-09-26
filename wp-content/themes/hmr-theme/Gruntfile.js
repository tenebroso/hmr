'use strict';
module.exports = function(grunt) {

  grunt.initConfig({
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: [
        'Gruntfile.js',
        'assets/js/*.js',
        'assets/js/plugins/*.js',
        'assets/js/views/**/*.js',
        '!assets/js/scripts.min.js'
      ]
    },
    recess: {
      dist: {
        options: {
          compile: true,
          compress: true
        },
        files: {
          'assets/css/main.min.css': [
            'assets/css/less/bootstrap/bootstrap.less',
            'assets/css/less/bootstrap/responsive.less',
            'assets/css/less/app.less'
          ]
        }
      }
    },
    uglify: {
      options: {
      compress: true,
      beautify: true
      },
      dist: {
        files: {
          'assets/js/scripts.min.js': [
            'assets/js/plugins/bootstrap/bootstrap-transition.js',
            'assets/js/plugins/bootstrap/bootstrap-modal.js',
            'assets/js/plugins/*.js',
            'assets/js/views/**/*.js',
            'assets/js/_*.js'
          ]
        }
      }
    },
    watch: {
      less: {
        files: [
          'assets/css/less/*.less',
          'assets/css/less/bootstrap/*.less'
        ],
        tasks: ['recess', 'version'],
        options: {
			livereload: false,
		}
      },
      js: {
        files: [
          '<%= jshint.all %>'
        ],
        tasks: ['uglify', 'version']
      }
    },
    clean: {
      dist: [
        'assets/css/main.min.css',
        'assets/js/scripts.min.js'
      ]
    }
  });

  // Load tasks
  grunt.loadTasks('tasks');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-recess');

  // Register tasks
  grunt.registerTask('default', [
    'clean',
    'recess',
    'uglify',
    'version'
  ]);
  grunt.registerTask('dev', [
    'watch'
  ]);

};
