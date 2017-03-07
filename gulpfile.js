/*
 To use gulp-image-resize you need to install GraphicsMagick first
 1. sudo apt-get install graphicsmagick
 2. put the images you need to resize into assets/images/to_resize
 3. run gulp image-resize

 */

// Image sizes(width) for gulp-image-resize
var sizes = [1920, 1400, 1024, 768];


// Global options
var options = {
    imgmin: false,
    svgmin: true,
    fonts: true,
    reload: true,
    svghtmlmin: false,
    bump: true,
    gzip: false,
    js: false,
    jsimport: false,
    jshint: false,
    jscs: false,
    webp: false
};

// Modules
var fs = require('fs');
var path = require('path');
var gulp = require('gulp');
var rename = require('gulp-rename');
var ignore = require('gulp-ignore');
var uglify = require('gulp-uglify');
var scss = require('gulp-sass');
var prefix = require('gulp-autoprefixer');
var cmq = require('gulp-combine-mq');
var cssMin = require('gulp-cssmin');
var csscomb = require('gulp-csscomb');
var rigger = require('gulp-rigger');
var sourcemaps = require('gulp-sourcemaps');
var toJson = require('gulp-to-json');
var concat = require('gulp-concat');
var htmlmin = require('gulp-htmlmin');
var pngcrush = require('imagemin-pngcrush');
var svgmin = require('gulp-svgmin');
var imagemin = require('gulp-imagemin');
var bump = require('gulp-bump');
var jsonmin = require('gulp-jsonmin');
//var jscs = require('gulp-jscs');
//var jshint = require('gulp-jshint');
var webp = require('gulp-webp');
var imageResize = require('gulp-image-resize');


gulp.task('jsonmin', function () {
    gulp.src(['./data/source/*.json'])
        .pipe(jsonmin())
        .pipe(gulp.dest('./data/'));
});


function add_options(param, array) {
    array = array || [];
    param.forEach(function (key) {
        if (options[key]) {
            array.push(key)
        }
    });
    return array;
}

function errorLog(func) {
    func.on('error', function (error) {
        console.log(error);
    });
    return func;

}
//var react = require('gulp-react');

var source = require('vinyl-source-stream');
var babelify = require('babelify');
var babel = require('gulp-babel');
var browserify = require('browserify');
var reactify = require('reactify');

var gutil = require('gulp-util');
var buffer = require('vinyl-buffer');
var to5ify = require('6to5ify');



gulp.task('browserify', function () {
    browserify('./assets/js/main.js', {debug: true})
        .transform(to5ify)
        .bundle()
        .on('error', gutil.log.bind(gutil, 'Browserify Error'))
        .pipe(source('main.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init({loadMaps: false})) // loads map from browserify file
        .pipe(uglify({
           compress: {
               drop_console: true
           }
        }))
        //.pipe(sourcemaps.write('./')) // writes .map file
        .pipe(gulp.dest('./dist/js/'));
});


/*

 gulp.task('browserify', function () {
 browserify('./assets/js/main.js', {debug: true})
 .transform(to5ify.configure({
 sourceMapRelative: "/Users/paul/Documents/projects/unicorno/dist/js"
 }))
 .bundle()
 .on("error", function (err) {
 console.log("Error : " + err.message);
 })
 .pipe(source('main.js'))
 //.pipe(buffer())
 .pipe(gulp.dest('./dist/js'));
 });
 */

/*

gulp.task('browserify', function () {


    var stream;
    var bundler;


    return browserify({
        entries: ['./assets/js/main.js'],
        debug: true,
        insertGlobals: true,
        packageCache: {},
        fullPaths: true
    })
        .transform("babelify", {presets: ["es2015", "react"]})
        .bundle()
        .on('error', function (err) {
            console.log(err.toString());
            this.emit("end");
        })
        .pipe(source('main.js')) // main source file
        .pipe(gulp.dest('./dist/js'));
});
*/


//gulp.task('browserify', function () {
//    browserify('./assets/js/main.js', {
//
//        browserifyOptions: {
//            debug: true
//        }
//    })
//        .transform(to5ify)
//        .bundle()
//        .on('error', gutil.log.bind(gutil, 'Browserify Error'))
//        .pipe(source('main.js'))
//        .pipe(buffer())
//        .pipe(sourcemaps.init({loadMaps: true}))
//        // Add transformation tasks to the pipeline here.
//        //.pipe(uglify())
//        //.on('error', gutil.log)
//        .pipe(sourcemaps.write('./'))
//        //.pipe(sourcemaps.init({loadMaps: true})) // loads map from browserify file
//        //.pipe(uglify({
//        //    compress: {
//        //        drop_console: true
//        //    }
//        //}))
//        //.pipe(sourcemaps.write('./')) // writes .map file
//        .pipe(gulp.dest('./dist/js/'));
//});


// Just running the two tasks
//gulp.task('default', ['browserify', 'css']);

//
//gulp.task('react', function () {
//    return gulp.src('assets/js/app-react.jsx')
//        .pipe(react())
//        .pipe(gulp.dest('dist/js/'));
//});
//
//function compile(watch) {
//    var bundler = watchify(browserify(['./assets/js/app.js'], {debug: true}).transform(babel));
//
//    function rebundle() {
//        bundler.bundle()
//            .on('error', function (err) {
//                console.error(err);
//                this.emit('end');
//            })
//            .pipe(source('build.js'))
//            .pipe(buffer())
//            .pipe(sourcemaps.init({loadMaps: true}))
//            .pipe(sourcemaps.write('./'))
//            .pipe(gulp.dest('./dist/js'));
//    }
//
//    if (watch) {
//        bundler.on('update', function () {
//            console.log('-> bundling...');
//            rebundle();
//        });
//    }
//
//    rebundle();
//}
//
//function watch() {
//    return compile(true);
//};
//
//gulp.task('build', function () {
//    return compile();
//});
//gulp.task('watchjs', function () {
//    return watch();
//});


// Services
gulp.task('bump', function () {
    gulp.src('./bower.json')
        .pipe(bump())
        .pipe(gulp.dest('./'));
});

gulp.task('tojson', function () {
    gulp.src('[_]*.html')
        .pipe(toJson({
            relative: true,
            filename: 'pages.json',
            strip: /^_|(.html)/g
        }));
});

// live reload
var browserSync;
var reload = function () {
};
var files = {
    js: 'assets/js/*.js',
    css: ['assets/css/global.scss', 'assets/css/pages/*.scss'],
    html: '[_]*.html'
}
if (options.reload) {
    browserSync = require('browser-sync').create();
    reload = browserSync.reload;
}

//HTML include
gulp.task('htmlimport', function () {
    gulp.src(files.html)
        .pipe(rigger())
        .pipe(rename(function (path) {
            var newName = path.basename;
            if (newName.charAt(0) === '_')
                newName = newName.slice(1);
            path.basename = newName;
        }))
        .pipe(gulp.dest(''));
});

// Images, SVG, Fonts
gulp.task('imgmin', function () {
    var formats = ['assets/images/**/*.+(jpeg|jpg|png|gif)', '!assets/images/to_resize/**'];
    if (options.webp) {
        gulp.src(formats)
            .pipe(webp())
            .pipe(gulp.dest('dist/images'));
    }
    var stream = gulp.src(formats);
    if (options.imgmin) {
        stream.pipe(imagemin({
            progressive: true,
            use: [pngcrush()]
        }))
    }
    stream.pipe(gulp.dest('dist/images'));
    return stream;
});

gulp.task('svgmin', function () {
    gulp.src('assets/images/**/*.svg')
        .pipe(svgmin())
        .pipe(gulp.dest('dist/images/'));
});

gulp.task('svghtmlmin', function () {
    return gulp.src('templates/svg/*.html')
        .pipe(htmlmin({collapseWhitespace: true}))
        .pipe(gulp.dest('templates/svg/'))
});

gulp.task('fonts', function () {
    gulp.src('assets/fonts/**')
        .pipe(gulp.dest('dist/fonts'));
});


// Image resize
gulp.task('image-resize', function () {
    sizes.forEach(function (size) {
        gulp.src('assets/images/to_resize/**/*.+(jpeg|jpg|png|gif)')
            .pipe(imageResize({
                width: size
            }))
            .pipe(rename({
                suffix: '-' + size
            }))
            .pipe(gulp.dest('dist/images/'));
    });
});

var argv = require('yargs').argv;
// SCSS
gulp.task('scss', function () {
    //console.log(argv);

    gulp.src('./assets/css/*.scss')
        //.pipe(sourcemaps.init())
        .pipe(scss().on('error', scss.logError))
        .pipe(prefix('last 2 versions', '> 1%', 'ie 10'))
        .pipe(cmq())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('dist/css'))
        .pipe(csscomb())
        .pipe(cssMin())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('dist/css'));
});


// JS
function getFiles(dir) {
    return fs.readdirSync(dir)
        .filter(function (file) {
            return fs.statSync(path.join(dir, file)).isFile();
        });
}

function mapJs(callback) {
    if (typeof callback !== 'function') {
        return;
    }
    getFiles('assets/js')
        .forEach(function (file) {
            file = file.slice(0, -3);
            callback(file);
        });
}


gulp.task('jscs', function () {
    mapJs(function (file) {
        gulp.src(['assets/js/' + file + '.js'])
            .pipe(errorLog(jscs()));
    });
});

gulp.task('jshint', function () {
    //mapJs(function (file) {
    var str = gulp.src(['assets/js/app.js'])
    //if (options.reload) {
    //    str.pipe(reload({stream: true, once: true}));
    //}
    //str
    //    .pipe(errorLog(jshint('.jshintrc')))
    //    .pipe(jshint.reporter('jshint-stylish'));
    //});
});

gulp.task('js', add_options(['jscs', 'jshint']), function () {
    mapJs(function (file) {
        gulp.src(['assets/js/' + file + '/**', 'assets/js/' + file + '.js'])
            .pipe(errorLog(concat(file + '.js')))
            .pipe(gulp.dest('dist/js'))
            .pipe(errorLog(uglify({
                compress: {
                    global_defs: {
                        "DEBUG": false
                    }
                }
            })))
            .pipe(rename({
                suffix: '.min'
            }))
            .pipe(gulp.dest('dist/js'));
    });
});


gulp.task('jsimport', function () {
    //gulp.src(files.js)
    gulp.src('assets/js/app.js')
        .pipe(rigger())
        //.pipe(gulp.dest('dist/js/pages/'))
        .pipe(errorLog(uglify()))
        .pipe(rename({
            name: 'bundle',
            suffix: '.min'
        }))
        .pipe(gulp.dest('dist/js/'));
});



gulp.task('jsmin', function () {
    //gulp.src(files.js)
    gulp.src('./dist/js/main.js')
        .pipe(rigger())
        .pipe(errorLog(uglify()))
        //.pipe(rename({
        //    suffix: '.min'
        //}))
        .pipe(gulp.dest('./dist/js/'));
});


gulp.task('prod', []);

// WATCH
gulp.task('watch', function () {
    //var watcherJS;
    if (options.reload) {
        browserSync.init({
            logPrefix: 'Live reload: ',
            //reloadDebounce: 3000,
            //reloadDelay: 1000,
            //open: "local",
            //, tunnel: "my-private-site",
            //proxy: 'http://unicorno.bigdropinc.net/'
            server: {
                baseDir: "./",
                index: 'index.html',

                routes: {
                    "/signup": "index.html",
                    "/settings": "index.html",
                    "/settings/account": "index.html",
                    "/settings/profile": "index.html",
                    "/settings/password": "index.html",
                    "/settings/delete": "index.html",
                    "/home": "index.html",
                    "/trending": "index.html",
                    "/fresh": "index.html",
                    "/login": "index.html",
                    "/logout": "index.html",
                    "/contact": "index.html",
                    "/privacy": "index.html",
                    "/*": "index.html",
                    "/terms": "index.html",
                    "/404": "index.html",
                    "/user/paul": "index.html",
                    "/user/paul/posts": "index.html",
                    "/user/paul/upvotes": "index.html",
                    "/user/paul/comments": "index.html",
                    "/search": "index.html",
                    "/posts/demo2": "index.html",
                    "/posts/demo": "index.html",
                    "/posts/ulala": "index.html",
                    "/notifications": "index.html",
                    "/forgot": "index.html",
                    "/posts/c/demo?hot": "index.html",
                    "/posts/YLj8MN": "index.html",
                    "/posts/j90d9o": "index.html",
                    "/posts/bLPkV4": "index.html",
                    "/posts/c/demo?fresh": "/data/comments_demo_fresh.json",
                }
            }
        });
    }

    //// JS
    //watcherJS = gulp.watch('assets/js/*.js', [add_options(['js', 'jsimport']), reload]);
    //if (options.jsimport) {
    //    watcherJS.on('change', function (event) {
    //        files.js = event.path;
    //    });
    //}
    gulp.watch(['assets/js/**/*.jsx', 'assets/js/**/*.js'], ['browserify']);


    // SCSS
    gulp.watch(['assets/css/global.scss', 'assets/css/pages/*.scss'], ['scss'/*reload*/])
        .on('change', function (event) {
            files.css = event.path;
        });
    gulp.watch(['assets/css/**/*.scss', '!assets/css/global.scss', '!assets/css/pages/*.scss'], ['scss' /*reload*/])
        .on('change', function (event) {
            files.css = ['assets/css/global.scss', 'assets/css/pages/*.scss'];
        });

    // HTML
    gulp.watch('[_]*.html', ['htmlimport' /*reload*/])
        .on('change', function (event) {
            files.html = event.path;
        })
        .on('error', function (err) {
            console.log(err);
        });
    gulp.watch('templates/**', ['htmlimport'])
        .on('change', function () {
            files.html = '[_]*.html';
        })
        .on('error', function (err) {
            console.log(err);
        });
});

// DEFAULT
default_option = ['tojson', 'scss', 'imgmin', 'watch'/*, 'watchjs'*/];
add_options(['svgmin', /* 'js', 'jsimport',*/ /*'svghtmlmin',*/ 'fonts', 'bump'], default_option);

gulp.task('default', default_option);
