import '../css/places_to_visit.scss'

//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#places_to_visit_about').trumbowyg();


//cropper
import 'cropper/dist/cropper.min';
import 'jquery-cropper/dist/jquery-cropper.min';

const $image = $("#selected_image");
const $input = $('#places_to_visit_featured_picture');

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
                aspectRatio: 800 / 500,
                dragMode: 'move',
                cropBoxMovable: false,
                cropBoxResizable: false,
                guides: false,
                minContainerWidth: 825,
                minContainerHeight: 500
            });
        };
});

$('#crop_image').on('click', function () {

    let imageData = $image.cropper('getCroppedCanvas',{
        width:800,
        height:500,
        fillColor: '#fff',
    }).toDataURL('image/jpeg');

    $('#preview').attr('src', imageData);
    $('#cropped_image').val(imageData);
});