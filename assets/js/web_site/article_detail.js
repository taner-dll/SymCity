import '../../css/web_site/article_detail.scss';
import $ from "jquery";

let text_length = $('#article_text_count').val().length;
let article_id = $('#article_id').val();
//console.log(text_length);

//okuma süresine göre read fonksiyonunu çalıştır.
if (text_length < 140) {
    read(15000)
} else if (text_length >= 140 && text_length < 300) {
    read(20000)
} else if (text_length >= 300) {
    read(30000)
}

/*let searchParams = new URLSearchParams(window.location.search);
console.log(searchParams.has("id"));*/
function read(time) {
    setTimeout(function () {
        $.ajax({
            url: Routing.generate('ajax_read_article'),
            type: "POST",
            dataType: "json",
            data: {article_id: article_id},
            statusCode: {
                200: function (responseObject, textStatus, errorThrown) {
                    console.log("okundu");
                }
            }
        });

    }, time);
}

//TODO yazı applause/alkış adetleri db'ye yazılacak.