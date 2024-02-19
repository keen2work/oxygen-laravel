const mix = require('laravel-mix');

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

mix.setPublicPath('public/');

mix.js('resources/js/public.js', 'js/dist')
    .js('resources/js/backend.js', 'js/dist')

	.sass('resources/sass/oxygen/bootstrap.scss', 'css/dist/bootstrap.css')
	.sass('resources/sass/public.scss', 'css/dist/public.css')
    .sass('resources/sass/backend.scss', 'css/dist/backend.css')
	.sass('resources/sass/auth.scss', 'css/dist/auth.css')
	.version()
	.sourceMaps()
	;

mix.browserSync({proxy: 'localhost.test'});
