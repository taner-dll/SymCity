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
$('#advert_category').on('change', function () {

    //seçili durumu iptal et
    $('#advert_sub_category').val(null).trigger('change');
    getAdvertSubCategories(this.value);

    $('#advert_price').prop('disabled', false);
    //fiyat gösterilsin mi (Checkbox)
    $('#advert_secretPrice').prop('disabled', false);
    $('#div_advert_status').css('display', 'none');//kiralık-satılık
    //eski mesaj varsa kaldırır.
    $('#ad_msg_div').css('display', 'none');

    console.log(this.value);
    let cat_change_msg;
    let msg = '';
    switch (this.value) {

        case 'shopping':
            msg = 'İkinci El / Sıfır Eşya';
            break;

        case 'real-estate':
            msg = 'Emlak';
            break;

        case 'vehicle':
            msg = 'Taşıt';
            break;

        case 'job':
            msg = 'İş';
            break;

        case 'private_lesson':
            msg = 'Özel Ders';
            break;

        case 'animals':
            msg = 'Evcil Hayvan';
            break;

        default:
            msg = false;
            break;
    }
    cat_change_msg = '<p style="font-size: 14px;background: #ded9ff;color: black;border-radius: 10px;' +
        'padding-left: 12px;padding-bottom: 2px;padding-top: 2px">\n' +
        'Şu anda\n' + '<b>' + msg + '</b> ilanı eklemektesiniz.\n' +
        '</p>';

    if (msg===false){
        cat_change_msg = '';
    }
    $('#cat_change_msg').html(cat_change_msg);
});

function getAdvertSubCategories(id) {
    $('#sub_cat_loader').css('display', 'inline');
    $.ajax({
        url: Routing.generate('ajax_get_sub_categories'),
        type: "GET",
        dataType: "json",
        data: {id: id},
        statusCode: {
            200: function (responseObject, /*textStatus, errorThrown*/) {
                $('#advert_sub_category').empty();
                $.each(responseObject, function (key, value) {
                    //select2 add dynamically
                    let newOption = new Option(value.name, value.id, false, false);
                    $('#advert_sub_category').append(newOption).trigger('change');
                });
                $('#sub_cat_loader').css('display', 'none');
                //seçili durumu iptal et
                $('#advert_sub_category').val(null).trigger('change');
            }
        }
    });
}


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
$('#advert_secretPrice').on('ifChecked', function (/*event*/) {
    Toast.fire({
        title: 'Fiyat Bilgisi İlanda Gösterilecek',
        type: 'success',
    })
});
$('#advert_secretPrice').on('ifUnchecked', function (/*event*/) {
    Toast.fire({
        title: 'Fiyat Bilgisi İlanda Gösterilmeyecek',
        type: 'warning',
    })
});


$('#advert_secretPhone').on('ifChecked', function (/*event*/) {
    Toast.fire({
        title: 'Telefon Bilgisi İlanda Gösterilecek',
        type: 'success',
    })
});
$('#advert_secretPhone').on('ifUnchecked', function (/*event*/) {
    Toast.fire({
        title: 'Telefon Bilgisi İlanda Gösterilmeyecek',
        type: 'warning',
    })
});


$('#advert_secretEmail').on('ifChecked', function (/*event*/) {
    Toast.fire({
        title: 'Eposta Bilgisi İlanda Gösterilecek',
        type: 'success',
    })
});
$('#advert_secretEmail').on('ifUnchecked', function (/*event*/) {
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

$input.on('change', function () {

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


$('#img_preview_div').on('click', function () {
    $('#preview').removeAttr('src');
});