const mix = require('laravel-mix');
var path = require('path');

require('laravel-mix-tailwind');
require('laravel-mix-purgecss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.webpackConfig({
	resolve: {
		alias: {
			Vue: path.resolve(__dirname, 'resources/js/'),
		}
	}
});

mix.js('resources/js/app.js', 'public/js')
	.vue()
	.postCss('resources/css/app.css', 'public/css')
	.tailwind('./tailwind.config.js');

if (mix.inProduction()) {
	mix
		.version()
		.purgeCss();
}

mix.browserSync('127.0.0.1:8000')
