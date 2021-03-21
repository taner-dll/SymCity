import '../css/feedback.scss';
import Swal from 'sweetalert2';
import $ from "jquery";


import {minutesDiffNow} from "./utils/date_differance";


const Toast_Center = Swal.mixin({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: 5000
});



$('#fb_submit_btn').on('click', function () {


    //localStorage içerisinde, en son gönderilen fb tarihine eriş
    let last_feedback = new Date(localStorage.getItem('last_feedback'));

    //son feedback üzerinden 5 dk geçmiş olmalıdır.
    if (minutesDiffNow(last_feedback)<5){
        Toast_Center.fire({
            title: 'Kısa süre önce geri bildiriminiz gönderilmiş.' +
                ' Lütfen daha sonra tekrar deneyiniz.',
            type: 'warning',
        });
        $('#fb_submit_btn').prop('disabled', true);
        return false;
    }


    $('#fb-overlay').css('display','block');

    let subject = $('#fb-subject');
    let message = $('#fb-message');


    //console.log(subject, message); return false;

    if (subject.val()===""){
        Toast_Center.fire({
            title: 'Lütfen mesaj konusu seçiniz.',
            type: 'warning',
        });
        $('#fb-overlay').css('display','none');
        return false;
    }

    if (message.val()===""){
        Toast_Center.fire({
            title: 'Lütfen mesaj giriniz.',
            type: 'warning',
        });
        $('#fb-overlay').css('display','none');
        return false;
    }


    $.ajax({
        url: Routing.generate('ajax_send_feedback'),
        type: "POST",
        dataType: "json",
        data: {

            subject: subject.val(),
            message: message.val(),
        },
        statusCode: {

            200: function (responseObject, textStatus, errorThrown) {

                //console.log(responseObject + textStatus + errorThrown);
                $('#fb-overlay').css('display','none');

                Toast_Center.fire({
                    title: 'Geri bildiriminiz başaralı bir şekilde gönderildi.',
                    type: 'success',
                });

                localStorage.setItem('last_feedback', new Date);

                $('#fb_submit_btn').prop('disabled', true);

            }
        }
    })
        .done(function (data) {
            //response text
            //console.log(data);
        })
        .fail(function (jqXHR, textStatus) {
            //hata anında
            console.log('Something went wrong: ' + textStatus);
        })
        .always(function (jqXHR, textStatus) {
            //her koşulda çalışır
            //console.log('Ajax request was finished')
        });


});