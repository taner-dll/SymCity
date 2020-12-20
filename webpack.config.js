let Encore = require('@symfony/webpack-encore');
let webpack = require('webpack');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
// directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/edremitkorfezi.com/public/build')
    //.setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('app_left_menu_active', './assets/js/app_left_menu_active.js')
    .addEntry('places_to_visit', './assets/js/places_to_visit.js')
    .addEntry('municipality', './assets/js/municipality.js')
    .addEntry('event', './assets/js/event.js')
    .addEntry('announce', './assets/js/announce.js')
    .addEntry('place_an_ad', './assets/js/place_an_ad.js')
    .addEntry('place', './assets/js/place.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    .addEntry('business', './assets/js/business.js')
    .addEntry('ad_sub_category', './assets/js/ad_sub_category.js')
    .addEntry('ad_category', './assets/js/ad_category.js')
    .addEntry('business_category', './assets/js/business_category.js')
    .addEntry('register', './assets/js/register.js')
    .addEntry('feedback', './assets/js/feedback.js')
    .addEntry('article', './assets/js/article.js')


    /**
     * web site js
     */

    .addEntry('app_main', './assets/js/web_site/app_main.js')
    .addEntry('main_page', './assets/js/web_site/main_page.js')
    .addEntry('business_guide', './assets/js/web_site/business_guide.js')
    .addEntry('ad_guide', './assets/js/web_site/ad_guide.js')
    .addEntry('article_detail', './assets/js/web_site/article_detail.js')


    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })

    .enableSingleRuntimeChunk()

    .addPlugin(new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/))

    .addPlugin(new CopyWebpackPlugin([
        // copies to {output}/static
        { from: './assets/img', to: 'img' }
    ]))

    .configureBabel((babelConfig) => {
        const preset = babelConfig.presets.find(([name]) => name === "@babel/preset-env");
        if (preset !== undefined) {
            preset[1].useBuiltIns = "usage";
            preset[1].corejs = '3.0.0';
        }
    })

;

module.exports = Encore.getWebpackConfig();
