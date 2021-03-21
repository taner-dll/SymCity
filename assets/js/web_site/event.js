import '../../css/web_site/event.scss';
import 'select2';
import 'select2/dist/js/i18n/tr';

import {getPlaceNeighborhoods, getSubCategories} from "../web_site/app_main";

//
//select2 theme
const bootstrap_theme = 'bootstrap';


$('#place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language:"tr",
    allowClear: true
});
//select2 uygula
$('#sub_place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

$(function () {
    //console.log(typeof $('#sub_place_id').val());
    if ($('#place').val()){
        getPlaceNeighborhoods($('#place').val());
    }

});


//district/neighborhood selector
//place tıklandığında, place'e ait neighborhoods getirilir.
//edremit->akçay, zeytinli... gibi
$('#place').on('change', function () {
    if (this.value === "") {
        $('#sub_place_form_group').css('display', 'none');
    } else {
        $('#sub_place_form_group').css('display', 'block');
        getPlaceNeighborhoods(this.value);
    }
});