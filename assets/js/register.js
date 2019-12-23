//sweetalert2
import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$('#registration_form_email').on('change',function () {
    //console.log(this.value.length);
    //console.log(Routing.generate('check_email'));

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
                    $('#email_avaible').hide();
                    $('#email_in_use').show();
                }
                else if (responseObject===0){
                    console.log("eposta uygun");
                    $('#email_avaible').show();
                    $('#email_in_use').hide();
                }
            }
        }
    }).done(function (data) {
        //response text
        //console.log(data);
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

