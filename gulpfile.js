var buildDir = '../bce';
var gulp = require('gulp');
var del = require('del');
var plumber = require('gulp-plumber');
var concat = require('gulp-concat');
var replace = require('gulp-replace');
var gulpif = require('gulp-if');
var argv = require('yargs').argv;
var svgo = require('gulp-svgo');
var strip = require('gulp-strip-code');

var htmlmin = require('gulp-cleanhtml');

var sass = require('gulp-sass'),
    postcss = require('gulp-postcss'),
    mqpacker = require('css-mqpacker'),
    autoprefixer = require('autoprefixer'),
    cssnano = require('cssnano');

var babel = require('gulp-babel');
var uglify = require('gulp-uglify');
var dir = buildDir;
if(argv.production) {
  dir = buildDir+'_production';
} else {
  dir = buildDir+'_development';
}
//CLEAN
gulp.task('clean', function(){
  return del([buildDir+'_production'+'/**'], {force:true});
  clean();
});
//CSS
gulp.task('css', function(){
  var plugins = [
  //    mqpacker(),
      autoprefixer({grid:true}),
      cssnano(),

  ]

  gulp.src('sass/main.scss')

    .pipe(gulpif(argv.production, strip({start_comment: "/* REMOVE IN PRODUCTION*/", end_comment: "/* END REMOVE IN PRODUCTION*/"})))
    .pipe(plumber())
    .pipe(sass())
    .pipe(gulpif(argv.production, postcss(plugins)))
    .pipe(gulp.dest(dir+'/css'));
});
gulp.task('js', function(){
  gulp.src([ 'js/plugins/*.js', 'js/site.js', 'js/modules/*.js'])
    .pipe(plumber())
    .pipe(babel({
        presets: [["es2015", { "modules": false }]]
    }))
    .pipe(concat('main.js'))
    .pipe(gulpif(argv.production, uglify()))
    .pipe(gulp.dest(dir+'/js'));
});

//Template Move
gulp.task('templates', function(){
  return gulp.src(['*.html', '*.php'])
    .pipe(gulpif(!argv.production, strip({start_comment: "REMOVE IN DEV", end_comment: "END REMOVE IN DEV"})))
    .pipe(gulpif(argv.production, strip({start_comment: "REMOVE FROM PRODUCTION", end_comment: "END REMOVE FROM PRODUCTION"})))
    .pipe(gulpif(argv.production, replace('$cacheBreaker = time();','$cacheBreaker = 1.1;')))
    .pipe(gulpif(argv.production, htmlmin()))

    .pipe(gulp.dest(dir));
});
//Asset Move
gulp.task('assetmove', function(){
  return gulp.src('assets/**/*')
    .pipe(gulpif(argv.production, svgo()))
    .pipe(gulp.dest(dir+'/assets'));
});
//WP
gulp.task('wpdump', function(){
  gulp.src(['style.css' ])
    .pipe(gulpif(!argv.production, replace('NEW BCE SITE SRC','NEW BCE SITE DEV VERSION')))
    .pipe(gulpif(argv.production, replace('NEW BCE SITE SRC','NEW BCE SITE PRODUCTION VERSION')))
    .pipe(gulp.dest(dir));
  gulp.src(['screenshot.png' ])
      .pipe(gulp.dest(dir));
});
gulp.task('watch', function() {
    gulp.watch('js/**/*.js', ['js']);
    gulp.watch(['sass/**/*'], ['css']);
    gulp.watch('assets/**/*', ['assetmove']);
    gulp.watch(['*.php', '*.html'], ['templates']);
});

gulp.task('build', [ 'js','wpdump','assetmove','templates','css']);
