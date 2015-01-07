var gulp = require('gulp')
  , apigen = require('gulp-apigen')

gulp.task('gendocs', function() {
    gulp.src('apigen.neon').pipe(apigen('bin/apigen'))
});
