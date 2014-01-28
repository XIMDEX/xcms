module.exports = function(grunt) {
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.initConfig({
		watch: {
			sass: {
				files: ['xmd/style/**/*.scss', 'actions/**/css/*.scss', 'inc/widgets/**/css/*.scss', 'modules/**/css/*.scss'],
				tasks: ['sass']
			}
		},
		sass: {
			dist: {
				files: [{
					expand: true,
					src: ['xmd/style/**/*.scss', 'actions/**/css/*.scss', 'inc/widgets/**/css/*.scss', 'modules/**/css/*.scss'],
					ext: '.css'
				}]
			}
		}
  //     	autoprefixer: {
		// 	options: {
		// 		browsers: ['last 1 version']
		// 	},
		// 	dist: {
		// 		files: [{
		// 			expand: true,
		// 			cwd: '.tmp/styles/',
		// 			src: '{,*/}*.css',
		// 			dest: '.tmp/styles/'
		// 		}]
		// 	}
		// }
	});
	grunt.registerTask('default', [
    	// 'autoprefixer',
    	'watch'
  	]);
};