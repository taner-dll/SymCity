import '../css/article.scss'

//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#article_article').trumbowyg();

//sweetalert2
import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});



$('#new_article_submit_btn').on('click',function () {



    let title = $('#article_title').val();
    let article = $('#article_article').val();



    //console.log(author_name.length);
/*    if (author_name.length< 5){
        Swal.fire({
            position: 'center',
            showConfirmButton: true,
            timer: 8000,
            type: 'error',
            title: 'Geçersiz Yazar Adı!',
            text: 'Lütfen "Bilgilerim" ekranına giderek, geçerli bir ad ve soyad belirleyiniz.'
        });
        return false;
    }*/

    if (title.length< 3){
        Swal.fire({
            position: 'center',
            showConfirmButton: true,
            timer: 8000,
            type: 'error',
            title: 'Geçersiz Başlık!',
            text: 'Lütfen geçerli bir başlık belirleyiniz.'
        });
        return false;
    }

    if (article.length< 140){
        Swal.fire({
            position: 'center',
            showConfirmButton: true,
            timer: 8000,
            type: 'error',
            title: 'Geçersiz Yazı!',
            text: 'Yazının 140 karakterden az olmamalıdır.'
        });
        return false;
    }


    $('#article_form').submit();


});



if($('#success_message').val()){
    Toast.fire({
        title: $('#success_message').val(),
        type: 'success',
    })
}

