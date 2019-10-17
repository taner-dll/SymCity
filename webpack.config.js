let Encore = require('@symfony/webpack-encore');
let webpack = require('webpack');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
// directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/edremitkorfezi.com/public/build')
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
