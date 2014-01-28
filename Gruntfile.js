module.exports = function(grunt) {
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.initConfig({
		watch: {
			sass: {
				files: ['xmd/style/**/*.scss', 'actions/**/css/*.scss', 'inc/widgets/**/css/*.scss', 'modules/**/css/*.scss', '!**/_*.scss'],
				tasks: ['sass:dev'],
				options: {
      				spawn: false
    			}
			}
		},
		sass: {
			dist: {
				files: [{
					expand: true,
					files: ['xmd/style/**/*.scss', 'actions/**/css/*.scss', 'inc/widgets/**/css/*.scss', 'modules/**/css/*.scss'],
					ext: '.css'
				}]
			},
			dev: {
				files: {}
			}
		}
	});
	grunt.registerTask('default', [
    	'watch'
  	]);
  	grunt.event.on('watch', function(action, filepath, target) {
		var filedest = filepath.slice(0, filepath.lastIndexOf("."));
		filedest += '.css'
		var files = {};
		files[filedest] = filepath;
		grunt.config.set('sass.dev.files', files);
	});
};