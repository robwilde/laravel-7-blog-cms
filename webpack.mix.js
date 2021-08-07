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

mix.js('resources/js/app.js', 'public/js')
.js('resources/js/post-form.js', 'public/js')
.sass('resources/sass/app.scss', 'public/css')
.copy('resources/css/select2-4.0.13.min.css', 'public/css/select2.css')
.sourceMaps();
