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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});


$('input').iCheck({
    checkboxClass: 'icheckbox_square-purple',
    radioClass: 'iradio_square-purple',
    increaseArea: '20%' // optional
});

/**
 * Tooltip aktif
 */
$('[data-toggle="tooltip"]').tooltip();



let tel_mask = new InputMask("(999) 999-9999");
tel_mask.mask($('#advert_telephone'));


let ajax_post = function (select, edit_mode=0) {





    $.ajax({
        url: Routing.generate('ajax_ad_subcategories'),
        type: "GET",
        dataType: "json",
        data: {category: select.val()},

        success: function (subcategories) {
            let subCategorySelect = $("#advert_sub_category");
            subCategorySelect.html('');
            subCategorySelect.append('<option>Seçiniz</option>');

            $.each(subcategories, function (key, subcategory) {

                subCategorySelect.append('<option value="' + subcategory.id + '">'
                    + subcategory.shortname_translated + '</option>');

            });

            /**
             * düzenleme sayfasında option selected yapıyoruz.
             */
            if(edit_mode===1){
                let selected = $('#selected_sub_category').val();
                subCategorySelect.val(selected);
            }

            $('#ajax-loader').hide();
            $('#sub_category').show();
            $('#messages').show();
        },
        error: function (err) {
            console.log(err);
        }
    });

};

/**
 * Sub category, düzenleme sayfasında görünür olarak
 * gelmelidir.
 */
console.log($('#current_page').val());
if($('#current_page').val()==='edit'){
    $('#sub_category').show();

    ajax_post($('#advert_category'),1);
}
else{
    $('#sub_category').hide();
    $('#advert_secretPrice').iCheck('check');
    $('#advert_secretPhone').iCheck('check');
    $('#advert_secretEmail').iCheck('check');
}



$('#advert_category').on('change', function () {

    // console.clear();
    // console.log(1111);

    let select = $(this);

    $('#sub_category').hide();
    $('#ajax-loader').show();
    $('#messages').hide();


    let delayInMilliseconds = 100;

    setTimeout(function () {

        ajax_post(select,0);

    }, delayInMilliseconds);


    $('.ad_msg').hide();
    $('#advert_price').prop('disabled', false);
    $('#advert_secretPrice').prop('disabled', false);



    $('#div_advert_status').show();


    switch (this.value) {

        case '4':
            $('#job_ad_msg').show();
            $('#div_advert_status').hide();
            break;

        case '5':
            $('#used_stuff_ad_msg').show();
            break;

        case '3':
            $('#real_estate_ad_msg').show();
            break;

        case '2':
            $('#vehicle_ad_msg').show();
            break;

        case '6':
            $('#private_lesson_ad_msg').show();
            $('#div_advert_status').hide();
            break;

        case '7':
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
    if (this.value === '87'){
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
$('#advert_secretPrice').on('ifChecked', function (event){
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


$('#advert_secretPhone').on('ifChecked', function (event){
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


$('#advert_secretEmail').on('ifChecked', function (event){
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
    let ext = ['jpeg','jpg','png'];
    if (!ext.includes(this.files[0]['name'].split(".")[1])){
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

    let imageData = $image.cropper('getCroppedCanvas',{
        width:870,
        height:470,
        fillColor: '#fff',
    }).toDataURL('image/jpeg');

    $('#preview').attr('src', imageData);
    $('#cropped_image').val(imageData);
});


