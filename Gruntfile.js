module.exports = function(grunt) {
		grunt.initConfig({
				pkg: grunt.file.readJSON('package.json'),
				watch: {
						sass: {
								files: ['src/scss/**/*.{scss,sass}'],
								tasks: ['sass:dist']
						},
						livereload: {
								files: ['*.html', '*.php', 'js/**/*.{js,json}', 'css/*.css', 'img/**/*.{png,jpg,jpeg,gif,webp,svg}'],
								options: {
										livereload: true
								}
						},
						uglify: {
								files: ['src/js/**/*.js'],
								tasks: ['uglify']
						}
				},
				sass: {
						options: {
								sourceMap: false,
								outputStyle: 'compressed'
						},
						dist: {
								files: [{
										'css/owl.carousel.css': 'src/scss/owl.carousel.scss'
								}, {
										'css/owl.carousel.admin.css': 'src/scss/owl.carousel.admin.scss'
								}, ]
						}
				},
				uglify: {
						my_target: {
								files: [{
										'js/owl.carousel.admin.min.js': ['js/original/owl.carousel.admin.js']
								}, {
										'js/jquery.gridder.min.js': ['js/original/jquery.gridder.js']
								}, {
										'js/owl.carousel.min.js': ['js/original/owl.carousel.js']
								}, {
										'js/channel.functions.min.js': ['js/original/channel.functions.js']
								}, {
										'js/channel.video.functions.min.js': ['js/original/channel.video.functions.js']
								}, {
										'js/channel.display.functions.min.js': ['js/original/channel.display.functions.js']
								}, ]
						}
				}

		});
		grunt.loadNpmTasks('grunt-sass');
		grunt.loadNpmTasks('grunt-contrib-watch');
		grunt.loadNpmTasks('grunt-contrib-uglify');

		grunt.registerTask('default', ['sass:dist', 'watch']);


};