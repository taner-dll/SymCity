//sweetalert2
import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

const email_input_loader = $('#email_input_loader');
const email_input_icon = $('#email_input_icon');
const email_form_group = $('#email_form_group');

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
                        footer: '<a href="#">Bu e-posta adresi size ait ise giriş yapmayı deneyin</a>'
                    })

                }
                else if (responseObject===0){
                    console.log("eposta uygun");
                    email_form_group.addClass('has-success');
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

