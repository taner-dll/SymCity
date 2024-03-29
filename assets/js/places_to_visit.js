import '../css/places_to_visit.scss'

//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#places_to_visit_about').trumbowyg();


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
$('#places_to_visit_place').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});

//select2 uygula
$('#places_to_visit_subPlace').select2({
    theme: bootstrap_theme,
    placeholder: "Seçiniz...",
    language: "tr",
    allowClear: true
});


$('#places_to_visit_place').on('change', function () {
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
                $('#places_to_visit_subPlace').empty();
                $.each(responseObject, function (key, value) {
                    //select2 add dynamically
                    let newOption = new Option(value.name, value.id, false, false);
                    $('#places_to_visit_subPlace').append(newOption);

                });
                $('#sub_place_loader').css('display', 'none');
                //seçilmedi olarak işaretle
                $('#places_to_visit_subPlace').val(null).trigger('change');
            }
        }
    });
}








//cropper
import 'cropper/dist/cropper.min';
import 'jquery-cropper/dist/jquery-cropper.min';
import $ from "jquery";
import Routing from "./Routing";

const $image = $("#selected_image");
const $input = $('#places_to_visit_featured_picture');

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


const $gallery_image = $("#gallery_selected_image");
const $gallery_input = $('#ptv_gallery');

$gallery_input.change(function () {

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
        $gallery_image.cropper('destroy');
        return false;
    }

    $('#gallery_myModal').modal('show');

    let oFReader = new FileReader();
    oFReader.readAsDataURL(this.files[0]);
    oFReader.onload = function () {

        // Destroy the old cropper instance
        $gallery_image.cropper('destroy');

        // Replace url
        $gallery_image.attr('src', this.result);

        // Start cropper
        $gallery_image.cropper({
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

$('#gallery_crop_image').on('click', function () {

    let imageData = $gallery_image.cropper('getCroppedCanvas', {
        width: 870,
        height: 470,
        fillColor: '#fff',
    }).toDataURL('image/jpeg');

    $('#gallery_preview').attr('src', imageData);
    $('#cropped_image').val(imageData);
});

$('#gallery_myModal').on('hidden.bs.modal', function () {
    // Destroy the old cropper instance
    $gallery_image.cropper('destroy');

    // Replace url
    $gallery_image.val('');
    $gallery_input.val('');
});

$("form").on("change", ".file-upload-field", function () {
    $(this).parent(".file-upload-wrapper").attr("data-text", $(this).val().replace(/.*(\/|\\)/, ''));
    $('#gallery_preview').show();
    $('#upload_button').show();
});