
var gulp = require('gulp');
var del = require('del');
var plumber = require('gulp-plumber');
var concat = require('gulp-concat');
var replace = require('gulp-replace');
var gulpif = require('gulp-if');
var argv = require('yargs').argv;
var svgo = require('gulp-svgo');
var strip = require('gulp-strip-code');

var buildDir = require("./gulp-locals.js").buildDir;

var htmlmin = require('gulp-htmlmin');

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
var buildDate = Math.floor(new Date() / 1000);
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

  return gulp.src(['sass/main.scss','sass/dark-mode.scss'])

    .pipe(gulpif(argv.production, strip({start_comment: "/* REMOVE IN PRODUCTION*/", end_comment: "/* END REMOVE IN PRODUCTION*/"})))
    .pipe(plumber())
    .pipe(sass())
    .pipe(gulpif(argv.production, postcss(plugins)))
    .pipe(gulp.dest(dir+'/css'));
});

function jsBuilder(srcArray, newName) {
 return gulp.src(srcArray)
    .pipe(plumber())
    .pipe(babel({
        presets: [["es2015", { "modules": false }]]
    }))
    .pipe(concat(newName))
    .pipe(gulpif(argv.production, uglify()))
    .pipe(gulp.dest(dir+'/js'));

}

gulp.task('js', function(){
    return jsBuilder([ 'js/plugins/*.js', 'js/site.js', 'js/modules/*.js'], 'main.js');
});

//Template Move
gulp.task('templates', gulp.series( function(){
  return gulp.src(['includes_media_stream_template/**/*'])
    .pipe(gulp.dest(dir+'/includes_media_stream_template'));
},
function() { 
  return gulp.src(['*.html', '*.php','!backend_plugin_markdown.php'])
    .pipe(gulpif(!argv.production, strip({start_comment: "REMOVE IN DEV", end_comment: "END REMOVE IN DEV"})))
    .pipe(gulpif(argv.production, strip({start_comment: "REMOVE FROM PRODUCTION", end_comment: "END REMOVE FROM PRODUCTION"})))
    .pipe(gulpif(argv.production, replace('$cacheBreaker = time();','$cacheBreaker = '+buildDate+';')))
    .pipe(gulpif(argv.production, htmlmin({ 
      collapseWhitespace: true,
      ignoreCustomFragments:[ /<%[\s\S]*?%>/, /<\?[\s\S]*?\?>/ ]
    })))

    .pipe(gulp.dest(dir));

}));
//Asset Move
gulp.task('assetmove', function(){
  return gulp.src('assets/**/*')
    .pipe(gulpif(argv.production, svgo()))
    .pipe(gulp.dest(dir+'/assets'));
});
//WP
gulp.task('wpdump', gulp.series(function() {
  return gulp.src(['style.css', "backend_plugin_markdown.php" ])
  .pipe(gulpif(!argv.production, replace('NEW BCE SITE SRC','NEW BCE SITE DEV VERSION')))
  .pipe(gulpif(argv.production, replace('NEW BCE SITE SRC','NEW BCE SITE PRODUCTION VERSION')))
  .pipe(gulp.dest(dir));
},
function() {
  return gulp.src(['screenshot.png' ])
  .pipe(gulp.dest(dir));
}));

gulp.task('watch', function() {
    gulp.watch(['js/**/*.js'], gulp.series('js'));
    gulp.watch(['sass/**/*'], gulp.series('css'));
    gulp.watch('assets/**/*', gulp.series('assetmove'));
    gulp.watch(['*.php', '*.html','includes_media_stream_template/**/*'], gulp.series('templates'));
});


gulp.task('build', gulp.series("clean", 'js','wpdump','assetmove','templates','css'));
