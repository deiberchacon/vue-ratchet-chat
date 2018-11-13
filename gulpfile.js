var gulp = require('gulp');
var connect = require('gulp-connect');
var browserify = require('browserify');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var browserify = require('browserify');
var partialify = require('partialify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var concat =require('gulp-concat');

gulp.task('html', function() {
    gulp.src('src/client/*.html')
        .pipe(gulp.dest('dist'))
        .pipe(connect.reload());
});

gulp.task('css', function() {
    var stylesheets = [
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'src/client/css/chat.css'
    ];

    gulp.src(stylesheets)
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest('dist/css'))
        .pipe(connect.reload());
});

gulp.task('js', function() {
    browserify({
        entries: 'src/client/js/chat.js',
        debug: true
    })
    .transform(partialify)
    .bundle()
    .on('error', function (err) {
        console.log(err.toString());
        this.emit('end');
    })
    .pipe(source('app.min.js'))
    .pipe(buffer())
    .pipe(sourcemaps.init({
        loadMaps: true
    }))
    .pipe(uglify())
    .on('error', function (err) {
        console.log(err.toString());
        this.emit('end');
    })
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('dist/js'))
    .pipe(connect.reload());
});

gulp.task('start-server', function() {
    connect.server({
        root: 'dist',
        port: 3000,
        livereload: true
    })
});

gulp.task('watch:html', function() {
    gulp.watch('src/client/*.html', ['html']);
});

gulp.task('watch:js', function() {
    gulp.watch('src/client/js/**/*.*', ['js']);
});

gulp.task('watch:css', function() {
    gulp.watch('src/client/css/**/*.*', ['css']);
});

gulp.task('compile', ['html', 'css', 'js']);
gulp.task('watch', ['compile', 'watch:html', 'watch:js', 'watch:css']);
gulp.task('serve', ['watch', 'start-server']);
gulp.task('default', ['compile']);