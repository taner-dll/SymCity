
/*const warningTitleCSS = 'color:red; font-size:60px; font-weight: bold; -webkit-text-stroke: 1px black;';
const warningDescCSS = 'font-size: 24px;';

console.log('%cDur!', warningTitleCSS);
console.log("%cHack girişimlerine karşı, geliştirme araçları açıldığı andan itibaren " +
    "tüm hareketleriniz ve IP adresiniz kayıt altına alınmaktadır. ", warningDescCSS);*/



// Remove empty fields from GET forms
// Author: Bill Erickson
// URL: http://www.billerickson.net/code/hide-empty-fields-get-form/

// Change 'form' to class or ID of your specific form
import $ from "jquery";

$("form").submit(function () {
    $(this).find(":input").filter(function () {
        return !this.value;
    }).attr("disabled", "disabled");
    return true; // ensure form still submits
});

// Un-disable form fields when page loads, in case they click back after submission
$("form").find(":input").prop("disabled", false);


export function getPlaceNeighborhoods(place_id) {
    $('#opt_loader').css('display','inline');
    $.ajax({
        url: Routing.generate('ajax_get_place_neighborhoods'),
        type: "GET",
        dataType: "json",
        data: {place_id: place_id},
        statusCode: {

            200: function (responseObject, textStatus, errorThrown) {
                //console.log(responseObject + textStatus + errorThrown)

                let html = '<option value="">Tümü</option>';

                $.each(responseObject, function (key, value) {

                    //console.log(value.name);
                    html += '<option value="' + value.id + '">' + value.name + '</option>';

                });
                $('#sub_place').html(html);
                $('#opt_loader').css('display','none');




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
}