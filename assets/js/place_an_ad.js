import '../css/place_an_ad.scss'

import 'icheck/icheck.min';
//sweetalert2
import Swal from 'sweetalert2';

/**
 * Inputmask
 */
import InputMask from 'inputmask';

/**
 * Fake Routing oluşturuldu.
 * routing.yaml expose: true yapmayı unutma.
 */
import Routing from './Routing';

//cropper
import 'cropper/dist/cropper.min';
import 'jquery-cropper/dist/jquery-cropper.min';


import 'select2';
import 'select2/dist/js/i18n/tr';


import {getPlaceNeighborhoods, getSubCategories} from "./web_site/app_main";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});


$('input').iCheck({
    checkboxClass: 'icheckbox_minimal-grey',
    //increaseArea: '-10%' // optional
});

/**
 * Tooltip aktif
 */
$('[data-toggle="tooltip"]').tooltip();


let tel_mask = new InputMask("(999) 999-9999");
tel_mask.mask($('#advert_telephone'));


//select2 theme
const bootstrap_theme = 'bootstrap';


//select2 uygula
$('#advert_category').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#advert_sub_category').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#advert_place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#advert_sub_place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

/////////////////////////////////////////////////////


//category selector
//category tıklandığında, category'e ait sub categories getirilir.
//emlak ilanları->konut, dükkan... gibi
/*$('#advert_category').on('change', function () {

    //kategori değişince alt kategoriyi temizle
    $('#advert_sub_category').val(null).trigger('change');

    $.ajax({
        url: Routing.generate('ajax_get_place_neighborhoods'),
        type: "GET",
        dataType: "json",
        data: {place_id: place_id},
        statusCode: {
            200: function (responseObject, textStatus, errorThrown) {
                //console.log(responseObject + textStatus + errorThrown)
                let html = '<option value="">Tümü</option>';

                $.each(responseObject, function (key, value) {

                    //console.log(value.name);
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#sub_place').html(html);
                $('#opt_loader').css('display','none');
            }
        }
    });



});*/







//district/neighborhood selector
//place tıklandığında, place'e ait neighborhoods getirilir.
//edremit->akçay, zeytinli... gibi
$('#advert_place').on('change', function () {
    if (this.value === "") {
        $('#sub_place_form_group').css('display', 'none');
    } else {
        $('#sub_place_form_group').css('display', 'block');
        getPlaceNeighborhoods(this.value);
    }
});



/*
$('#advert_secretPrice').iCheck('check');
$('#advert_secretPhone').iCheck('check');
$('#advert_secretEmail').iCheck('check');*/








$('#advert_category').on('change', function () {


    let select = $(this);

    $('#sub_category').hide();
    $('#ajax-loader').show();
    $('#messages').hide();

    ajax_post(select, 0);

    $('.ad_msg').hide();
    $('#advert_price').prop('disabled', false);
    $('#advert_secretPrice').prop('disabled', false);

    $('#div_advert_status').show();

    switch (this.value) {

        case '3':
            $('#job_ad_msg').show();
            $('#div_advert_status').hide();
            break;

        case '4':
            $('#used_stuff_ad_msg').show();
            break;

        case '2':
            $('#real_estate_ad_msg').show();
            break;

        case '1':
            $('#vehicle_ad_msg').show();
            break;

        case '5':
            $('#private_lesson_ad_msg').show();
            $('#div_advert_status').hide();
            break;

        case '6':
            $('#pet_ownership_ad_msg').show();
            break;

        default:
            $('.callout').hide();
            $('#ajax-loader').hide();
    }


});


$('#advert_sub_category').on('change', function () {

    $('#advert_price').prop('disabled', false);
    $('#advert_secretPrice').prop('disabled', false);

    //87: pet-ownership
    if (this.value === '87') {
        $('#advert_price').val(0);
        $('#advert_price').prop('disabled', true);
        $('#advert_secretPrice').iCheck('unCheck');
        $('#advert_secretPrice').removeAttr('checked');
        $('#advert_secretPrice').prop('disabled', true);

        Swal.fire(
            'Bilgilendirme',
            'Evcil Hayvan İlanlarında Yasalar Gereği Fiyat Bilgisi Girilemez.',
            'info'
        );
    }
});


/**
 * iCheck için kullanılan özel yöntemler: ifChecked, ifUnchecked
 */
$('#advert_secretPrice').on('ifChecked', function (event) {
    Toast.fire({
        title: 'Fiyat Bilgisi İlanda Gösterilecek',
        type: 'success',
    })
});
$('#advert_secretPrice').on('ifUnchecked', function (event) {
    Toast.fire({
        title: 'Fiyat Bilgisi İlanda Gösterilmeyecek',
        type: 'warning',
    })
});


$('#advert_secretPhone').on('ifChecked', function (event) {
    Toast.fire({
        title: 'Telefon Bilgisi İlanda Gösterilecek',
        type: 'success',
    })
});
$('#advert_secretPhone').on('ifUnchecked', function (event) {
    Toast.fire({
        title: 'Telefon Bilgisi İlanda Gösterilmeyecek',
        type: 'warning',
    })
});


$('#advert_secretEmail').on('ifChecked', function (event) {
    Toast.fire({
        title: 'Eposta Bilgisi İlanda Gösterilecek',
        type: 'success',
    })
});
$('#advert_secretEmail').on('ifUnchecked', function (event) {
    Toast.fire({
        title: 'Eposta Bilgisi İlanda Gösterilmeyecek',
        type: 'warning',
    })
});


//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';
import $ from "jquery";

$.trumbowyg.svgPath = icons;
$('#advert_description').trumbowyg();


if ($('#success_message').val()) {
    Toast.fire({
        title: $('#success_message').val(),
        type: 'success',
    })
}


const $image = $("#selected_image");
const $input = $('#advert_featured_image');

$input.change(function () {

    /**
     * Dosya uzantı kontrolü.
     * Sadece jpeg, jpg, png
     * @type {*[]}
     */
    let ext = ['jpeg', 'jpg', 'png'];
    if (!ext.includes(this.files[0]['name'].split(".")[1])) {
        Swal.fire({
            type: 'error',
            title: 'Geçersiz Dosya Formatı',
            text: 'Yalnızca JPEG, PNG formatında görsel yükleyiniz.',
            confirmButtonText: 'Tamam',
        });
        // Destroy the old cropper instance
        $image.cropper('destroy');
        return false;
    }

    $('#myModal').modal('show');

    let oFReader = new FileReader();
    oFReader.readAsDataURL(this.files[0]);
    oFReader.onload = function () {

        // Destroy the old cropper instance
        $image.cropper('destroy');

        // Replace url
        $image.attr('src', this.result);

        // Start cropper
        $image.cropper({
            aspectRatio: 870 / 470,
            dragMode: 'move',
            cropBoxMovable: true,
            cropBoxResizable: true,
            guides: false,
            minContainerWidth: 800,
            minContainerHeight: 432,
            viewMode: 2 //?

        });
    };
});

$('#crop_image').on('click', function () {

    let imageData = $image.cropper('getCroppedCanvas', {
        width: 870,
        height: 470,
        fillColor: '#fff',
    }).toDataURL('image/jpeg');

    $('#preview').attr('src', imageData);
    $('#cropped_image').val(imageData);
});

$('#myModal').on('hidden.bs.modal', function () {
    // Destroy the old cropper instance
    $image.cropper('destroy');

    // Replace url
    $image.val('');
    $input.val('');
});