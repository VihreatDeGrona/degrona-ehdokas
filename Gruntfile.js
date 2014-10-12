'use strict';

module.exports = function(grunt) {

	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		// setting folder templates
		dirs: {
			css: 'assets/css',
			scss: 'assets/scss',
			js: 'assets/js'
		},

		/**
		 * Project banner
		 * Dynamically appended to CSS/JS files
		 * Inherits text from package.json
		 */
		tag: {
			banner: '/*!\n' +
				' * <%= pkg.title %>\n' +
				' * <%= pkg.url %>\n' +
				' * @author <%= pkg.author %>\n' +
				' * @version <%= pkg.version %>\n' +
				' * <%= pkg.license %> licensed.\n' +
				' */\n'
		},

		// Compile all .scss files.
		sass: { // Task
			dist: { // Target
				options: { // Target options
					style: 'nested',
					sourcemap: 'none'
				},
				files: { // Dictionary of files
					'<%= dirs.css %>/admin.css': '<%= dirs.scss %>/admin.scss', // 'destination': 'source'
					'<%= dirs.css %>/frontend.css': '<%= dirs.scss %>/frontend.scss', // 'destination': 'source'
				}
			}
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: 'some',
				banner: '<%= tag.banner %>'
			},
			jsfiles: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js',
						'!Gruntfile.js',
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			}
		},

		// Watch changes for assets
		watch: {
			css: {
				files: ['<%= dirs.scss %>/*.scss'],
				tasks: ['sass']
			},
			js: {
				files: [
					'<%= dirs.js %>/*js',
					'!<%= dirs.js %>/*.min.js'
				],
				tasks: ['uglify']
			}
		},

	});

	// Register tasks
	grunt.registerTask('default', [
		'watch'
	]);

	grunt.registerTask('build', [
		'sass',
		'uglify'
	]);

};