import gulp from 'gulp'
import taskListing from 'gulp-task-listing'
import ghPages from 'gulp-gh-pages'

gulp.task('default', taskListing)
gulp.task('doxygen', ['doxygen-deploy'])

// gulp.task('doxygen-build', () => {
//   PROJECT_NUMBER=$(git describe --abbrev=0) doxygen ./doxygen/Doxyfile
// })
gulp.task('doxygen-deploy', () => {
  return gulp.src('./doxygen/dist/**/*')
    .pipe(ghPages({
      remoteUrl: 'git@github.com:xpressengine/xe-manual-api.git'
    }))
});
