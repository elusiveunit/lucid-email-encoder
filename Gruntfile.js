module.exports = function(grunt) {
	'use strict';

	grunt.initConfig({

		// Data from package.json
		pkg: grunt.file.readJSON('package.json'),

		// JSHint
		jshint: {
			options: {
				'bitwise'  : true,
				'browser'  : true,
				'curly  '  : true,
				'eqeqeq'   : true,
				'eqnull'   : true,
				'es3'      : true,
				'forin'    : true,
				'immed'    : true,
				'indent'   : false,
				'jquery'   : true,
				'latedef'  : true,
				'newcap'   : true,
				'noarg'    : true,
				'noempty'  : true,
				'nonew'    : true,
				'node'     : true,
				'smarttabs': true,
				'strict'   : true,
				'trailing' : true,
				'undef'    : true,

				// lucidEmailEncoder is used outside the JS file
				'unused'   : false,

				'globals': {
					'jQuery': true,
					'alert': true
				}
			},
			dist: {
				src: [
					'js/email-decoder.js',
					'js/generate-script.js'
				]
			},
			grunt: {
				src: ['Gruntfile.js']
			}
		},

		// JavaScript concatenation and minification
		uglify: {
			decoder: {
				options: { report: 'min' },
				files: {'js/email-decoder.min.js': ['js/email-decoder.js']}
			},
			generate: {
				options: { report: 'min' },
				files: {'js/generate-script.min.js': ['js/generate-script.js']}
			}
		},

		// Watch project for changes
		watch: {
			js: {
				files: ['<%= jshint.dist.src %>'],
				tasks: ['jshint', 'uglify']
			}
		}

	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Register tasks.
	// Default: just 'grunt'
	grunt.registerTask('default', [
		'jshint:dist',
		'uglify'
	]);

	// Watch: 'grunt w'
	grunt.registerTask('w', [
		'jshint:dist',
		'uglify',
		'watch'
	]);

	// Gruntfile: 'grunt g'
	grunt.registerTask('g', [
		'jshint:grunt'
	]);

};