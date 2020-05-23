let mix = require('laravel-mix');
let minifier = require('minifier');
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

mix.copyDirectory('resources/img', 'public/img')
    .js('resources/js/admin/admin.js', 'public/js')
    .sass('resources/sass/admin.scss', 'public/css')
    .sass('resources/sass/_base0.scss', 'public/css')
    .copyDirectory('resources/fonts', 'public/fonts')
    .sass('resources/sass/front.scss', 'public/css')
    .extract(['vue','vue-router','moment','axios','lodash','dropzone']);

mix.then(() => {
    minifier.minify('public/css/front.css')
    minifier.minify('public/css/_base0.css')
})
mix.disableSuccessNotifications();

/*mix.browserSync('localhost:8000');*/
