'use strict';

var cp            = require('child_process');

var browserSync   = require('browser-sync');
var del           = require('del');
var gulp          = require('gulp');
var cleanCSS      = require('gulp-clean-css');
var concat        = require('gulp-concat');
var header        = require('gulp-header');
var imagemin      = require('gulp-imagemin');
var plumber       = require('gulp-plumber');
var rename        = require('gulp-rename');
var uglify        = require('gulp-uglify');
var runSequence   = require('run-sequence');

var pkg = require('./package.json');
//console.log(pkg);

// Set the banner content
var banner = ['/*!\n',
' * RODRIGO3D.COM - <%= pkg.name %> v<%= pkg.version %> (https://rodrigo3d.com)\n',
' * Copyright 2012-' + (new Date()).getFullYear(), ' The rodrigo3d.com Authors\n',
' * Author <%= pkg.author.name %> - <%= pkg.author.email %>\n',
' * Licensed under MIT (https://github.com/rodrigo3d/<%= pkg.name %>/blob/master/LICENSE)\n',
' */\n',
''
].join('');

var messages = {
  jekyllBuild: '<span style="color: red">Running:</span> $ jekyll build'
};

var jekyllCommand = (/^win/.test(process.platform)) ? 'windows' : 'linux';

// Build the Jekyll Site
if(jekyllCommand === 'windows'){
  gulp.task('jekyll-build', function (done) {
    browserSync.notify(messages.jekyllBuild);
    return cp.spawn('jekyll.bat', ['build'], {stdio: 'inherit'})
    .on('close', done);
  });
}
if(jekyllCommand === 'linux'){
 gulp.task('jekyll-build', function (done) {
   browserSync.notify(messages.jekyllBuild);
   return cp.spawn('bundle', ['exec', 'jekyll build'], {stdio: 'inherit'})
   .on('close', done);
 });
}

// Rebuild Jekyll & do page reload
gulp.task('jekyll-rebuild', ['jekyll-build'], function () {
 browserSync.reload();
});

// Wait for jekyll-build, then launch the Server
gulp.task('browser-sync', ['jekyll-build'], function() {
 browserSync.init({
  server: { baseDir: "./_site", directory: false,  index: "index.html" }
});
});

// Gulp task to minify css files
gulp.task('css', function(){
  return gulp.src('./src/css/**/*.css')
  .pipe(plumber())
  // .pipe(concat('style.css'))
  .pipe(cleanCSS())
  .pipe(rename({suffix: '.min'}))
  .pipe(header(banner, {pkg: pkg}))
  .pipe(gulp.dest('./_site/assets/css/'))
  // .pipe(browserSync.reload({stream:true}))
  .pipe(gulp.dest('./assets/css/'))
  .pipe(browserSync.stream())
  .pipe(plumber.stop());
});

// Gulp task to minify JavaScript files
gulp.task('js', function(){
  return gulp.src(['./src/js/**/*.js', '!./src/js/analytics.js'])
  .pipe(plumber())
  // .pipe(concat('main.js'))
  .pipe(uglify())
  .pipe(rename({suffix: '.min'}))
  .pipe(header(banner, {pkg: pkg}))
  //.pipe(gulp.dest('./_site/assets/js/'))
  .pipe(gulp.dest('./assets/js/'))
  .pipe(browserSync.stream())
  .pipe(plumber.stop());
});

// Gulp task to minify image files
gulp.task('img', function() {
  return gulp.src(['./src/img/**/*.{gif,jpg,jpeg,png,svg,ico}', '!./src/img/**/*.fw.png'])
  .pipe(plumber())
  .pipe(imagemin({optimizationLevel: 5, progressive: true, interlaced: true}))
  //.pipe(gulp.dest('./_site/assets/img/'))
  .pipe(gulp.dest('./assets/img/'))
//  .pipe(browserSync.stream())
.pipe(plumber.stop());
});

// Copy third party libraries from /src into /vendor
gulp.task('vendor', function(){

  // Bootstrap CSS
  gulp.src([
    './src/vendor/bootstrap/css/*.min.css',
    '!./src/vendor/bootstrap/css/*{-grid*,-reboot*}'
    ])
  .pipe(gulp.dest('./assets/vendor/bootstrap/css/'));
 // .pipe(browserSync.stream());

  // Bootstrap JS
  gulp.src([
    './src/vendor/bootstrap/js/*bundle.min.js'
    ])
  .pipe(gulp.dest('./assets/vendor/bootstrap/js/'));
//  .pipe(browserSync.stream());

  // jQuery
  gulp.src([
    './src/vendor/jquery/jquery.min.js'
    ])
  .pipe(gulp.dest('./assets/vendor/jquery/'));
 // .pipe(browserSync.stream());

  // jQuery Easing
  gulp.src([
    './src/vendor/jquery-easing/jquery.easing.min.js'
    ])
  .pipe(gulp.dest('./assets/vendor/jquery-easing/'));
//  .pipe(browserSync.stream());

});

// Clean output directory
gulp.task('clean', function () {
  return del([
    './_site',
    './assets'
    ]);
});

// Watch all files for changes & recompile
//Watch all files, run jekyll & reload BrowserSync
gulp.task('watch', function () {
  gulp.watch('./src/css/**/*.css', ['css']);
  gulp.watch('./src/js/**/*.*', ['js']);
  gulp.watch('./src/img/**/*.{gif,jpg,jpeg,png,svg,ico}', ['img']);
  gulp.watch('./src/vendor/**/*.*', ['vendor']);
  gulp.watch(['**/*.yml', '**/*.html', 'index.html', '_includes/*.html', '_layouts/*.html', '_data/*.*'], ['jekyll-rebuild']);
});

// Gulp task to minify all files
// gulp.task('default', ['css', 'js', 'img', 'vendor', 'browser-sync', 'watch']);
gulp.task('default', ['clean'], function () {
  runSequence(
    'css',
    'js',
    'img',
    'vendor',
    'browser-sync',
    'watch'
    );
});

// Gulp task to build all files
// gulp.task('build', ['css', 'js', 'img', 'vendor', 'jekyll-build']);
gulp.task('build', ['clean'], function () {
  runSequence(
    'css',
    'js',
    'img',
    'vendor',
    'jekyll-rebuild'
    );
});
