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

    $.ajax({
        url: Routing.generate('check_email'),
        type: "POST",
        dataType: "json",
        data: {email: email},
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

                Toast.fire({
                    title: 'Liste Başarıyla Güncellendi',
                    type: 'success',
                })

            }
        }
    }).done(function (data) {
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

