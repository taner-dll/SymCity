
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import '../css/app.scss';

// require jQuery normally
import $ from 'jquery';

// create global $ and jQuery variables
global.$ = global.jQuery = $;

import 'bootstrap';

import './app_left_menu_active';

import 'slimscroll';
import 'fastclick';
import '../js/theme';
import '../js/demo';


