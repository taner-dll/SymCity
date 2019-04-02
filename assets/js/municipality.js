import '../css/municipality.scss'

//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#municipality_about').trumbowyg();
$('#municipality_mayor').trumbowyg();


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


//cropper
import 'cropper/dist/cropper.min';
import 'jquery-cropper/dist/jquery-cropper.min';

const $image = $("#selected_image");
const $input = $('#municipality_featured_picture');

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
                minContainerWidth: 825,
                minContainerHeight: 600
            });
        };
});

$('#crop_image').on('click', function () {

    let imageData = $image.cropper('getCroppedCanvas',{
        width:800,
        height:600,
        fillColor: '#fff',
    }).toDataURL('image/jpeg');

    $('#preview').attr('src', imageData);
    $('#cropped_image').val(imageData);
});