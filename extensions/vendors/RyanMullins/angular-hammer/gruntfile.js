module.exports = function (grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    copy: {
      map: {
        src: './angular.hammer.min.js.map',
        dest: './examples/'
      },
      min: {
        src: './angular.hammer.min.js',
        dest: './examples/angular.hammer.demo.js'
      },
      std: {
        src: './angular.hammer.js',
        dest: './examples/angular.hammer.demo.js'
      }
    },
    jsdoc : {
      dist : {
        src: ['./angular.hammer.js'],
        dest: './doc',
        options: {
          configure: 'jsdoc.json'
        }
      }
    },
    nodemon: {
      demo: {
        script:'server.js'
      }
    },
    uglify: {
      options: {
        sourceMap: true,
        sourceMapName: './angular.hammer.min.js.map',
        mangle: true,
        preserveComments: require('uglify-save-license')
      },
      './angular.hammer.min.js': ['./angular.hammer.js']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-jsdoc');
  grunt.loadNpmTasks('grunt-nodemon');

  grunt.registerTask('default', ['uglify', 'jsdoc']);
  grunt.registerTask('demo', ['uglify', 'copy:std', 'nodemon']);
  grunt.registerTask('demo-min', ['uglify', 'copy:min', 'copy:map', 'nodemon']);
}