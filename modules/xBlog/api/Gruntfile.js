module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-contrib-coffee');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-riot');
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-sass');

    var config = {
        basePath: '/',
    };
    //
    grunt.initConfig({
        coffee: {
            compile: {
                options: {
                    bare: true,
                },
                files: {
                    '.temp/coffee.js': [
                        // ficheros coffee en el orden que queramos
                        //ContentEdit
                        'lib/ContentEdit/src/scripts/namespace.coffee',
                        'lib/ContentEdit/src/scripts/tag-names.coffee',
                        'lib/ContentEdit/src/scripts/bases.coffee',
                        'lib/ContentEdit/src/scripts/regions.coffee',
                        'lib/ContentEdit/src/scripts/root.coffee',
                        'lib/ContentEdit/src/scripts/static.coffee',
                        'lib/ContentEdit/src/scripts/text.coffee',
                        'lib/ContentEdit/src/scripts/images.coffee',
                        'lib/ContentEdit/src/scripts/videos.coffee',
                        'lib/ContentEdit/src/scripts/lists.coffee',
                        'lib/ContentEdit/src/scripts/tables.coffee',

                        //ContentTools
                        'lib/ContentTools/src/scripts/namespace.coffee',

                        // UI
                        'lib/ContentTools/src/scripts/ui/ui.coffee',
                        'lib/ContentTools/src/scripts/ui/flashes.coffee',
                        'lib/ContentTools/src/scripts/ui/ignition.coffee',
                        'lib/ContentTools/src/scripts/ui/inspector.coffee',
                        'lib/ContentTools/src/scripts/ui/modal.coffee',
                        'lib/ContentTools/src/scripts/ui/toolbox.coffee',

                        // UI - Dialogs
                        'lib/ContentTools/src/scripts/ui/dialogs/dialogs.coffee',
                        'lib/ContentTools/src/scripts/ui/dialogs/image.coffee',
                        'lib/ContentTools/src/scripts/ui/dialogs/link.coffee',
                        'lib/ContentTools/src/scripts/ui/dialogs/properties.coffee',
                        'lib/ContentTools/src/scripts/ui/dialogs/table.coffee',
                        'lib/ContentTools/src/scripts/ui/dialogs/video.coffee',

                        // Other
                        'lib/ContentTools/src/scripts/editor.coffee',
                        'lib/ContentTools/src/scripts/history.coffee',
                        'lib/ContentTools/src/scripts/styles.coffee',
                        'lib/ContentTools/src/scripts/tools.coffee',

                        // public main
                        'coffee/imagePickerDialog.coffee',
                        'coffee/confirmPublishDialog.coffee',
                        'coffee/date.coffee',
                        'coffee/cropdialog.coffee',
                        'coffee/intro.coffee',
                        'coffee/imageUploader.coffee',
                        'coffee/main.coffee'
                    ]
                }
            },
        },
        // concatenar los ficheros js
        concat: {
            options: {
                separator: ";",
            },
            app: {
                src: [
                    //'bower_components/underscore/underscore.js',
                    'bower_components/jquery/jquery.min.js',
                    'bower_components/moment/min/moment-with-locales.js',
                    'bower_components/rome/dist/rome.standalone.min.js',
                    'bower_components/riot/riot.min.js',
                    'lib/ContentEdit/external/html-string.js',
                    'lib/ContentEdit/external/content-select.js',
                    'bower_components/cropper/dist/cropper.js',
                    'bower_components/masonry/dist/masonry.pkgd.js',
                    'bower_components/image-picker/image-picker/image-picker.js',
                    '.temp/tags.js',
                    '.temp/coffee.js'
                ],
                dest: 'public/js/main.js',
            },
        },
        sass: {
            options: {
                sourceMap: true
            },
            dist: {
                files: {
                    '.temp/main.css': 'style/main.scss'
                }
            }
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'public/style/main.min.css': [
                        //'bower_components/bootstrap/dist/css/bootstrap.min.css',
                        'bower_components/image-picker/image-picker/image-picker.css',
                        'lib/ContentTools/build/content-tools.min.css',
                        'lib/ContentEdit/build/content-edit.min.css',
                        'bower_components/rome/dist/rome.min.css',
                        'bower_components/cropper/dist/cropper.min.css',
                        '.temp/main.css'
                    ]
                }
            }
        },
        uglify: {
            default: {
                files: {
                    'public/js/main.min.js': ['public/js/main.js']
                }
            }
        },
        // ficheros a observar que lanzarÃ¡n la funciÃ³n default (varias)
        watch: {
            configFiles: {
                files: [ 'Gruntfile.js' ],
                options: {
                  reload: true
                }
              },
            scripts: {
                files: [
                    'lib/ContentTools/**/*.coffee',
                    'lib/ContentEdit/**/*.coffee',
                    'lib/ContentTools/**/*.css',
                    'coffee/**/*.coffee',
                    'tags/**/*.tag',
                    'style/**/*.scss'
                ],
                tasks: ['compiledev'],
                options: {
                    interrupt: true,
                    livereload: true
                },
            },
        },

        notify_hooks: {
            options: {
              enabled: true,
              max_jshint_notifications: 5, // maximum number of notifications from jshint output
              title: "Editor", // defaults to the name in package.json, or will use project directory's name
              success: true, // whether successful grunt executions should be notified automatically
              duration: 0.5 // the duration of notification in seconds, for `notify-send only
            }
          },
        riot: {
          options: {
              concat : true
          },
          dist: {
            src: 'tags/*.tag',
            dest: '.temp/tags.js'
          }
        },
    });
    grunt.task.run('notify_hooks');
    grunt.registerTask('compiledev', ["coffee", "riot", "concat", "sass", "cssmin"]); // grunt dist
    grunt.registerTask('compile',   ["coffee", "riot", "concat", 'uglify', "sass", "cssmin"]); // grunt dist
    grunt.registerTask('default',   ['compile']);
};