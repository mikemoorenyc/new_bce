var buildDir = '../bce-prod-dev';
var gulp = require('gulp');
var del = require('del');
var plumber = require('gulp-plumber');
var concat = require('gulp-concat');
var replace = require('gulp-replace');
var gulpif = require('gulp-if');
var argv = require('yargs').argv



var sass = require('gulp-sass'),
    postcss = require('gulp-postcss'),
    mqpacker = require('css-mqpacker'),
    autoprefixer = require('autoprefixer'),
    cssnano = require('cssnano');

var babel = require('gulp-babel');
var uglify = require('gulp-uglify');

//CLEAN
gulp.task('clean', function(){
  return del([buildDir+'/**'], {force:true});
  clean();
});
//CSS
gulp.task('css', function(){
  gulp.src('sass/main.scss')
    .pipe(plumber())
    .pipe(sass())
    .pipe(gulp.dest(buildDir+'/css'));
});
gulp.task('js', function(){
  gulp.src([ 'js/plugins/*.js', 'js/site.js', 'js/modules/*.js'])
    .pipe(plumber())
    .pipe(babel({
        presets: [["es2015", { "modules": false }]]
    }))
    .pipe(concat('main.js'))
    .pipe(gulpif(argv.production, uglify()))
    .pipe(gulp.dest(buildDir+'/js'));
});

//Template Move
gulp.task('templates', function(){
  return gulp.src(['*.html', '*.php'])
    .pipe(gulp.dest(buildDir));
});
//Asset Move
gulp.task('assetmove', function(){
  return gulp.src('assets/**/*')
    .pipe(gulp.dest(buildDir+'/assets'));
});
//WP
gulp.task('wpdump', function(){
  return gulp.src(['style.css', 'screenshot.png'])
    .pipe(gulp.dest(buildDir));
});
gulp.task('watch', function() {
    gulp.watch('js/**/*.js', ['js']);
    gulp.watch(['sass/**/*'], ['css']);
    gulp.watch('assets/**/*', ['assetmove']);
    gulp.watch(['*.php', '*.html'], ['templates']);
});

gulp.task('build', [ 'js','wpdump','assetmove','templates','css']);
