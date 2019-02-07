require('../css/admin_pages.scss');


require('trumbowyg/dist/trumbowyg.min');
import icons from 'trumbowyg/dist/ui/icons.svg';
$.trumbowyg.svgPath = icons;
$('#places_to_visit_about').trumbowyg();


require('croppie');
$('#places_to_visit_featured_picture').croppie();