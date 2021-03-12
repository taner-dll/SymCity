import '../css/event.scss'

//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#event_description').trumbowyg();


//sweetalert2
import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

if($('#success_message').val()){
    Toast.fire({
        title: $('#success_message').val(),
        type: 'success',
    })
}
//sweetalert2 end


//select2
import 'select2';
import 'select2/dist/js/i18n/tr';

//select2 theme
const bootstrap_theme = 'bootstrap';

//select2 uygula
$('#event_place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#event_subPlace').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#event_category').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});


$('#event_place').on('change', function () {
    getSubPlaces(this.value);
});

function getSubPlaces(id) {
    $('#sub_place_loader').css('display', 'inline');
    $.ajax({
        url: Routing.generate('ajax_get_place_neighborhoods'),
        type: "GET",
        dataType: "json",
        data: {place_id: id},
        statusCode: {
            200: function (responseObject, /*textStatus, errorThrown*/) {
                $('#event_subPlace').empty();
                $.each(responseObject, function (key, value) {
                    //select2 add dynamically
                    let newOption = new Option(value.name, value.id, false, false);
                    $('#event_subPlace').append(newOption);

                });
                $('#sub_place_loader').css('display', 'none');
                //seçilmedi olarak işaretle
                $('#event_subPlace').val(null).trigger('change');
            }
        }
    });
}

/**
 * Inputmask
 */
import InputMask from 'inputmask';

//telefon için maske oluştur
let tel_mask = new InputMask("(999) 999-9999");
tel_mask.mask($('#event_phone'));




//cropper
import 'cropper/dist/cropper.min';
import 'jquery-cropper/dist/jquery-cropper.min';
import $ from "jquery";
import Routing from "./Routing";

const $image = $("#selected_image");
const $input = $('#event_image');

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