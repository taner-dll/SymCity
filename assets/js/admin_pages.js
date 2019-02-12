import '../css/admin_pages.scss'


//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#places_to_visit_about').trumbowyg();


//cropper
import 'cropper/dist/cropper.min';
import 'jquery-cropper/dist/jquery-cropper.min';


$('#places_to_visit_featured_picture').change(function () {


    $('#myModal').modal('show');
    let $image = $("#image_demo"),  originalData = {};




        let oFReader = new FileReader();
        oFReader.readAsDataURL(this.files[0]);
        oFReader.onload = function () {

            // Destroy the old cropper instance
            $image.cropper('destroy');

            // Replace url
            $image.attr('src', this.result);


            // Start cropper
            $image.cropper({
                aspectRatio: 2,
                dragMode: 'move',
                cropBoxMovable: false,
                cropBoxResizable: false,
                guides: false,



                minContainerWidth: 800,
                minContainerHeight: 400
            });
        };





});











