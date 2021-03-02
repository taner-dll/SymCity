import '../../css/web_site/business_guide.scss';
import 'select2';
import 'select2/dist/js/i18n/tr';

import {getPlaceNeighborhoods} from "../web_site/app_main";


//select2 theme
const bootstrap_theme = 'bootstrap';


$('#place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language:"tr",
    allowClear: true

});

/*

if ($('#sub_place_id').val()) {

    //console.log(typeof $('#sub_place_id').val());

    $.ajax({
        url: Routing.generate('ajax_get_place_neighborhoods'),
        type: "GET",
        dataType: "json",
        data: {place_id: $('#place_id').val()},
        statusCode: {
            200: function (responseObject, textStatus, errorThrown) {
                let html = '<option value="">Tümü</option>';
                let selected = '';
                $.each(responseObject, function (key, value) {
                    if (value.id.toString() === $('#sub_place_id').val()) {
                        selected = 'selected';
                    }else {selected='';}
                    html += '<option ' + selected + ' value="' + value.id + '">' + value.name + '</option>';
                });
                $('#sub_place').html(html);
            }
        }
    }).done(function (data) {
        $('#sub_place_form_group').css('display', 'block');
    });
}

// district/neighborhood selector
$('#place').on('click', function () {
    if (this.value === "") {
        $('#sub_place_form_group').css('display', 'none');
    } else {
        $('#sub_place_form_group').css('display', 'block');
        getPlaceNeighborhoods(this.value);
    }
});*/
