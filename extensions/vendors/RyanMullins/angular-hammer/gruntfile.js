module.exports = function (grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    browserify: {
      demo: {
        files: {
          './examples/browserify/example.js': ['./examples/browserify/index.js']
        },
        options: {
          browserifyOptions: {
            debug: true
          }
        }
      }
    },
    concurrent: {
      tasks: ['watch:raw','nodemon'],
      options: {
        logConcurrentOutput: true
      }
    },
    copy: {
      raw: {
        expand: true,
        flatten: true,
        src: [
          './angular.hammer.js',
          './node_modules/hammerjs/hammer.js',
          './node_modules/angular/angular.js'
        ],
        dest: './examples/raw/'
      },
      rawmin: {
        expand: true,
        flatten: true,
        src: [
          './angular.hammer.min.js',
          './angular.hammer.min.js.map',
          './node_modules/angular/angular.min.js',
          './node_modules/angular/angular.min.js.map',
          './node_modules/hammerjs/hammer.min.js',
          './node_modules/hammerjs/hammer.min.js.map'
        ],
        dest: './examples/raw/'
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
        script:'server.js',
        options: {
          watch: ['./examples']
        }
      }
    },
    requirejs: {
    },
    uglify: {
      dist: {
        options: {
          sourceMap: true,
          sourceMapName: './angular.hammer.min.js.map',
          mangle: true,
          preserveComments: require('uglify-save-license')
        },
        files: {
          './angular.hammer.min.js': './angular.hammer.js'
        }
      },
      browserify: {
        options: {
          sourceMap: true,
          sourceMapName: './examples/browserify/example.min.js.map',
          mangle: true
        },
        files: {
          './examples/browserify/example.js': ['./examples/browserify/example.js']
        }
      }
    },
    watch: {
      js:  {
        files: ['./angular.hammer.js'],
        tasks: ['copy']
      },
      raw: {
        files: [
          './angular.hammer.js',
          './angular.hammer.min.js'
        ],
        tasks: ['copy']
      }
    },
    webpack: {
    }
  });

  grunt.loadNpmTasks('grunt-browserify');
  grunt.loadNpmTasks('grunt-concurrent');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-requirejs');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-jsdoc');
  grunt.loadNpmTasks('grunt-nodemon');
  grunt.loadNpmTasks('grunt-webpack');

  grunt.registerTask('build', ['uglify:dist', 'jsdoc:dist']);
  grunt.registerTask('default', ['copy', 'concurrent']);
  grunt.registerTask('demo-browserify', ['browserify', 'nodemon']);
  grunt.registerTask('demo-browserify-min', ['browserify', 'uglify:browserify', 'nodemon']);
  grunt.registerTask('demo-raw', ['copy:raw', 'nodemon']);
  grunt.registerTask('demo-raw-min', ['copy:rawmin', 'nodemon']);
}