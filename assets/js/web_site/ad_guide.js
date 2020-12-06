/**
 * @author Taner Deliloglu <tnrdll@gmail.com>
 */
import '../../css/web_site/ad_guide.scss';
import 'select2';
import 'select2/dist/js/i18n/tr';
import {getPlaceNeighborhoods, getSubCategories} from "../web_site/app_main";

//select2 theme
const bootstrap_theme = 'bootstrap';

//select2 uygula
$('#place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true,
    disabled: false
});

//select2 uygula
$('#sub_place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#sub_cat').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#cat').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
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

//category selector
//category tıklandığında, category'e ait sub categories getirilir.
//emlak ilanları->konut, dükkan... gibi
$('#cat').on('change', function () {
    if (this.value === "") {
        $('#sub_cat_form_group').css('display', 'none');
    } else {
        $('#sub_cat_form_group').css('display', 'block');
        getSubCategories(this.value);

    }
});

//sub_place_id hidden value'dur.
//sayfa yüklendiğinde get parametresi varsa bu sorgu çalışır.
//place'e ait neighborhoods getirilir. selected işaretlenir.
$(function () {
    //console.log(typeof $('#sub_place_id').val());
    if ($('#place').val()){
        getPlaceNeighborhoods($('#place').val());
    }
    if ($('#cat').val()){
        getSubCategories($('#cat').val());
    }
});

$('#ilan_table tr').on('click', function () {
    //console.log(this.dataset.id);
    window.location.href = Routing.generate('advert_detail',
        {
            id: this.dataset.id,
            cat: this.dataset.cat,
            sub: this.dataset.sub,
            slug: this.dataset.slug
        }
    );

});