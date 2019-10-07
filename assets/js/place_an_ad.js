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




$('#sub_category').hide();
$('#advert_category').on('change', function () {

    let categorySelector = $(this);

    $('#sub_category').hide();
    $('#ajax-loader').show();
    $('#messages').hide();


    let delayInMilliseconds = 300;

    setTimeout(function () {

        $.ajax({
            url: Routing.generate('ajax_ad_subcategories'),
            type: "GET",
            dataType: "json",
            data: {category: categorySelector.val()},

            success: function (subcategories) {


                let subCategorySelect = $("#advert_sub_category");
                subCategorySelect.html('');

                console.log(subcategories);

                subCategorySelect.append('<option>Seçiniz</option>');

                $.each(subcategories, function (key, subcategory) {
                    subCategorySelect.append('<option value="' + subcategory.shortname + '">'
                        + subcategory.shortname_translated + '</option>');
                });

                $('#ajax-loader').hide();
                $('#sub_category').show();
                $('#messages').show();


            },
            error: function (err) {
                console.log(err);
            }


        });

    }, delayInMilliseconds);


    $('.ad_msg').hide();
    $('#advert_price').prop('disabled', false);
    $('#secret_price').prop('disabled', false);


    //console.log(this.value);

    switch (this.value) {
        case 'job':
            $('#job_ad_msg').show();
            break;

        case 'used-stuff':
            $('#used_stuff_ad_msg').show();
            break;

        case 'real-estate':
            $('#real_estate_ad_msg').show();
            break;

        case 'vehicle':
            $('#vehicle_ad_msg').show();
            break;

        case 'private-lesson':
            $('#private_lesson_ad_msg').show();
            break;

        case 'animals':

            $('#pet_ownership_ad_msg').show();


            break;

        default:
            $('.callout').hide();


    }


});


$('#advert_sub_category').on('change', function () {

    $('#advert_price').prop('disabled', false);
    $('#secret_price').prop('disabled', false);


    if (this.value === 'pet-ownership'){
        $('#advert_price').val(0);
        $('#advert_price').prop('disabled', true);
        $('#secret_price').iCheck('unCheck');
        $('#secret_price').removeAttr('checked');
        $('#secret_price').prop('disabled', true);

        Swal.fire(
            'Bilgilendirme',
            'Evcil Hayvan İlanlarında Yasalar Gereği Fiyat Bilgisi Girilemez.',
            'info'
        );
    }
});


//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#advert_description').trumbowyg();




const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

if ($('#success_message').val()) {
    Toast.fire({
        title: $('#success_message').val(),
        type: 'success',
    })
}



const $image = $("#selected_image");
const $input = $('#advert_featured_image');

$input.change(function () {

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
            aspectRatio: 800 / 600,
            dragMode: 'move',
            cropBoxMovable: false,
            cropBoxResizable: false,
            guides: false,
            minContainerWidth: 800,
            minContainerHeight: 600
        });
    };
});

$('#crop_image').on('click', function () {

    let imageData = $image.cropper('getCroppedCanvas', {
        width: 800,
        height: 600,
        fillColor: '#fff',
    }).toDataURL('image/jpeg');

    $('#preview').attr('src', imageData);
    $('#cropped_image').val(imageData);
});

