'use strict';

var del = require('del');
var gulp = require('gulp');
var sourcemaps = require('gulp-sourcemaps');
var prefixer = require('gulp-autoprefixer');
var sass = require('gulp-sass');
var browserSync = require('browser-sync').create();
var gulpIf = require('gulp-if');
var cssnano = require('gulp-cssnano');
var plumber = require('gulp-plumber');
var notify = require('gulp-notify');

var isDevelopment = !process.env.NODE_ENV || process.env.NODE_ENV == 'development';

gulp.task('styles', function() {

  return gulp.src('web/assets/scss/**/*.scss')
      .pipe(plumber({
        errorHandler: notify.onError(function(err) {
            return {
              title:   'Styles',
              message: err.message
            };
        })
      }))
      .pipe(gulpIf(isDevelopment, sourcemaps.init()))
      .pipe(sass())
      .pipe(prefixer({browsers: ['last 2 versions']}))
      .pipe(gulpIf(isDevelopment, sourcemaps.write()))
      .pipe(gulpIf(!isDevelopment, cssnano()))
      .pipe(gulp.dest('web/assets/css/'));

});


gulp.task('clean', function() {
  return del('web/assets/css/');
});

gulp.task('build', gulp.series('clean', 'styles'));

gulp.task('serve', function() {
  var domain = 'http://127.0.0.1:8000';

  browserSync.init({
    proxy:  {
        target: domain
    }
  });

  browserSync.watch(["web/assets/**/*.*","!web/assets/**/*.scss","app/Resources/views/**/*.twig"]).on('change', browserSync.reload);
});

gulp.task('dev',
    gulp.series(
        'build',
        gulp.parallel(
            'serve',
            function() {
              gulp.watch('web/assets/scss/**/*.scss', gulp.series('styles'));
            }
        )
    )
);

gulp.task('default', gulp.series('dev'));