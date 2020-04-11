var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');

sass.compiler = require('node-sass');

var bower = require('gulp-bower');
var config = {
    bowerDir: './bower_components'
};

// Install ow Bower Components
gulp.task('bower', function() {
    return bower()
        .pipe(gulp.dest(config.bowerDir))
});


gulp.task('css', function () {
    return gulp.src([
        'public/vendors/bootstrap.min.css',
        'public/vendors/app.min.css',
        'public/vendors/icons.min.css',
        'public/vendors/jquery-toast/jquery.toast.css'
    ])
        .pipe(concat('vendor.min.css'))
        .pipe(gulp.dest('public/css'))
});

gulp.task('js', function () {
    return gulp.src([
        'public/vendors/vendor.min.js',
        'public/vendors/app.min.js',
        'public/vendors/matchHeight.js',
        'public/vendors/moment.js',
        'public/vendors/base64.js',
    ])
        .pipe(concat('vendor.min.js'))
        .pipe(gulp.dest('public/js'))
});
gulp.task('main', function () {
    return gulp.src('public/scss/main.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('main.min.css'))
        .pipe(gulp.dest('public/css'));
});
gulp.task('scss', function () {
    return gulp.src('public/scss/dashboard.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('dashboard.min.css'))
        .pipe(gulp.dest('public/css'));
});
gulp.task('fscss', function () {
    return gulp.src('public/scss/frontend.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('frontend.min.css'))
        .pipe(gulp.dest('public/css'));
});
gulp.task('option', function () {
    return gulp.src('public/scss/options.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('option.min.css'))
        .pipe(gulp.dest('public/css'));
});
