import '../css/article.scss'

//trumbowyg
import 'trumbowyg/dist/trumbowyg.min';
import icons from 'trumbowyg/dist/ui/icons.svg';

$.trumbowyg.svgPath = icons;
$('#article_article').trumbowyg();

if($('#success_message').val()){
    Toast.fire({
        title: $('#success_message').val(),
        type: 'success',
    })
}