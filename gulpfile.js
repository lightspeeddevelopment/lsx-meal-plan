const gulp         = require('gulp');
const autoprefixer = require('gulp-autoprefixer');
const gettext      = require('gulp-gettext');
const jshint       = require('gulp-jshint');
const plumber      = require('gulp-plumber');
const rename       = require('gulp-rename');
const rtlcss       = require('gulp-rtlcss');
const sass         = require('gulp-sass');
const sort         = require('gulp-sort');
const sourcemaps   = require('gulp-sourcemaps');
const uglify       = require('gulp-uglify');
const gutil        = require('gulp-util');
const wppot        = require('gulp-wp-pot');

const browserlist  = ['last 2 version', '> 1%'];

gulp.task('default', function() {
	console.log('Use the following commands');
	console.log('--------------------------');
	console.log('gulp compile-css               to compile the scss to css');
	console.log('gulp compile-js                to compile the js to min.js');
	console.log('gulp watch                     to continue watching the files for changes');
	console.log('gulp wordpress-lang            to compile the lsx-health-plan.pot, lsx-health-plan-en_EN.po and lsx-health-plan-en_EN.mo');
});

gulp.task('styles', function (done) {
	return gulp.src('assets/css/scss/*.scss')
		.pipe(plumber({
			errorHandler: function(err) {
				console.log(err);
				this.emit('end');
			}
		}))
		.pipe(sourcemaps.init())
		.pipe(sass({
			outputStyle: 'compact',
			includePaths: ['assets/css/scss']
		}).on('error', gutil.log))
		.pipe(autoprefixer({
			browsers: browserlist,
			casacade: true
		}))
		.pipe(sourcemaps.write('maps'))
		.pipe(gulp.dest('assets/css')),
		done();
});

gulp.task('styles-rtl', function (done) {
	return gulp.src('assets/css/scss/*.scss')
		.pipe(plumber({
			errorHandler: function(err) {
				console.log(err);
				this.emit('end');
			}
		}))
		.pipe(sass({
			outputStyle: 'compact',
			includePaths: ['assets/css/scss']
		}).on('error', gutil.log))
		.pipe(autoprefixer({
			browsers: browserlist,
			casacade: true
		}))
		.pipe(rtlcss())
		.pipe(rename({
			suffix: '-rtl'
		}))
		.pipe(gulp.dest('assets/css')),
		done();
});

gulp.task('compile-css',  gulp.series( ['styles', 'styles-rtl'] , function(done) {
	done();
}));

gulp.task('js', function(done) {
	return gulp.src('assets/js/src/**/*.js')
		.pipe(plumber({
			errorHandler: function(err) {
				console.log(err);
				this.emit('end');
			}
		}))
		.pipe(jshint())
		.pipe(uglify())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest('assets/js')),
		done();
});

gulp.task('compile-js', gulp.series( ['js'] , function(done) {
	done();
}));

gulp.task('watch-css', function (done) {
	done();
	return gulp.watch('assets/css/**/*.scss', gulp.series('compile-css'));
});

gulp.task('watch-js', function (done) {
	done();
	return gulp.watch('assets/js/src/**/*.js', gulp.series('compile-js'));
});

gulp.task('watch', gulp.series( ['watch-css', 'watch-js'] , function(done) {
	done();
}));

gulp.task('wordpress-pot', function(done) {
	return gulp.src('**/*.php')
		.pipe(sort())
		.pipe(wppot({
			domain: 'lsx-health-plan',
			package: 'lsx-health-plan',
			bugReport: 'https://github.com/lightspeeddevelopment/lsx-health-plan/issues',
			team: 'LightSpeed <webmaster@lsdev.biz>'
		}))
		.pipe(gulp.dest('languages/lsx-health-plan.pot')),
		done();
});

gulp.task('wordpress-po', function(done) {
	return gulp.src('**/*.php')
		.pipe(sort())
		.pipe(wppot({
			domain: 'lsx-health-plan',
			package: 'lsx-health-plan',
			bugReport: 'https://github.com/lightspeeddevelopment/lsx-health-plan/issues',
			team: 'LightSpeed <webmaster@lsdev.biz>'
		}))
		.pipe(gulp.dest('languages/lsx-health-plan-en_EN.po')),
		done();
});

gulp.task('wordpress-po-mo', gulp.series( ['wordpress-po'], function(done) {
	return gulp.src('languages/lsx-health-plan-en_EN.po')
		.pipe(gettext())
		.pipe(gulp.dest('languages')),
		done();
}));

gulp.task('wordpress-lang', gulp.series( ['wordpress-pot', 'wordpress-po-mo'] , function(done) {
	done();
}));
