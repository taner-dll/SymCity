import '../css/register.scss'


const email_input_loader = $('#email_input_loader');
const email_input_icon = $('#email_input_icon');
const email_form_group = $('#email_form_group');
const login_url = Routing.generate('app_login');
const register_url = $('#app_register').val();

const username_input_loader = $('#username_input_loader');
const username_input_icon = $('#username_input_icon');
const username_form_group = $('#username_form_group');


import {scorePassword} from './utils/password_strength_checker';
$('#registration_form_plainPassword_first').on('keyup', function () {
    //check password strength
    scorePassword(this.value);
});


import 'icheck/icheck.min';
$('input').iCheck({
    checkboxClass: 'icheckbox_flat-grey',
    //increaseArea: '-10%' // optional
});


//sweetalert2
import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 8000
});

if($('#success_message').val()){
    Toast.fire({
        title: $('#success_message').val(),
        type: 'success',
    })
}

/**
 yönlendirilen sayfada, nereden geldiğimizin bilgisi varsa
 bu bilgiyi login form auth'a gönderiyoruz.
 onAuthenticationSuccess anında kontrol ederek,
 login sonrası yönlendirme işlemini gerçekleştiriyoruz.
 */
if($('#page_from').val()){
    Swal.fire({
        title: 'Giriş Uyarısı',
        html: "<p>İşleminizi gerçekleştirebilmek için lütfen giriş yapınız.</p>",
        type: 'warning',
        confirmButtonText: 'Tamam',
        footer: '<a href="'+register_url+
            '">Henüz üye değilseniz, üye olmak için buraya tıklayınız</a>'
    })
}
//sweetalert2 end



$('#registration_form_email').on('change',function () {
    //console.log(this.value.length);
    //console.log(Routing.generate('check_email'));

    email_input_icon.hide();
    email_input_loader.show();

    $.ajax({
        url: Routing.generate('check_email'),
        type: "POST",
        dataType: "json",
        data: {email: $('#registration_form_email').val()},
        statusCode: {
            /**
             * Response Manipulation
             * @param responseObject
             * @param textStatus
             * @param jqXHR
             */
            404: function (responseObject, textStatus, jqXHR) {
                // No content found (404)
                // This code will be executed if the server returns a 404 response
            },
            503: function (responseObject, textStatus, errorThrown) {
                // Service Unavailable (503)
                // This code will be executed if the server returns a 503 response
            },
            200: function (responseObject, textStatus, errorThrown) {
                //console.log(responseObject + textStatus + errorThrown)
                if (responseObject===1){
                    console.log("kayitli eposta");
                    email_form_group.addClass('has-error');
                    email_form_group.removeClass('has-success');

                    let email = $('#registration_form_email').val();

                    Swal.fire({
                        type: 'error',
                        title: 'Kayıtlı E-Posta!',
                        text: email+' adresi kullanımdadır, lütfen farklı bir adres deneyin.',
                        confirmButtonText: 'Tamam',
                        footer: '<a href="'+login_url+
                            '">Bu e-posta adresi size ait ise giriş yapmayı deneyin</a>'
                    })
                }
                else if (responseObject===0){
                    //console.log("eposta uygun");
                    //email_form_group.addClass('has-success');
                    email_form_group.removeClass('has-error');
                }
            }
        }
    }).done(function (data) {
        //response text
        //console.log(data);
        email_input_loader.hide();
        email_input_icon.show();

    })
        .fail(function (jqXHR, textStatus) {
            //hata anında
            //console.log('Something went wrong: ' + textStatus);
        })
        .always(function (jqXHR, textStatus) {
            //her koşulda çalışır
            //console.log('Ajax request was finished')
        });


});

$('#registration_form_userName').on('change',function () {
    //console.log(this.value.length);
    //console.log(Routing.generate('check_email'));


    //console.log(this.value.length);
    if (this.value.length < 5){
        return false;
    }

    username_input_icon.hide();
    username_input_loader.show();



    $.ajax({
        url: Routing.generate('check_username'),
        type: "POST",
        dataType: "json",
        data: {username: $('#registration_form_userName').val()},
        statusCode: {
            /**
             * Response Manipulation
             * @param responseObject
             * @param textStatus
             * @param jqXHR
             */
            404: function (responseObject, textStatus, jqXHR) {
                // No content found (404)
                // This code will be executed if the server returns a 404 response
            },
            503: function (responseObject, textStatus, errorThrown) {
                // Service Unavailable (503)
                // This code will be executed if the server returns a 503 response
            },
            200: function (responseObject, textStatus, errorThrown) {
                //console.log(responseObject + textStatus + errorThrown)
                if (responseObject===1){
                    console.log("kayitli kullanıcı adı");
                    username_form_group.addClass('has-error');
                    username_form_group.removeClass('has-success');

                    let user_name = $('#registration_form_userName').val();

                    Swal.fire({
                        type: 'error',
                        title: 'Kayıtlı Kullanıcı Adı!',
                        text: user_name+' kullanımdadır, lütfen farklı bir kullanıcı adı deneyin.',
                        confirmButtonText: 'Tamam',
                        footer: '<a href="'+login_url+
                            '">Bu kullanıcı adı  size ait ise giriş yapmayı deneyin</a>'
                    })
                }
                else if (responseObject===0){
                    console.log("kullanıcı adı uygun");
                    username_form_group.addClass('has-success');
                    username_form_group.removeClass('has-error');
                }
            }
        }
    }).done(function (data) {
        //response text
        //console.log(data);
        username_input_loader.hide();
        username_input_icon.show();

    })
        .fail(function (jqXHR, textStatus) {
            //hata anında
            //console.log('Something went wrong: ' + textStatus);
        })
        .always(function (jqXHR, textStatus) {
            //her koşulda çalışır
            //console.log('Ajax request was finished')
        });


});