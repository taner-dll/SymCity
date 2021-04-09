
/**
 * @author Taner Deliloglu <tnrdll@gmail.com>
 */

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
/*$("form").find(":input").prop("disabled", false);*/



//ilçelere ait mahalle ve semtler.
export function getPlaceNeighborhoods(place) {
    $('#opt_loader_place').css('display','inline');
    $.ajax({
        url: Routing.generate('ajax_get_place_neighborhoods'),
        type: "GET",
        dataType: "json",
        data: {place: place},
        statusCode: {
            200: function (responseObject, textStatus, errorThrown) {
                $('#sub_place').empty();
                $.each(responseObject, function (key, value) {
                    let newOption = new Option(value.name, value.short_name, false, false);
                    $('#sub_place').append(newOption);
                });
                $('#opt_loader_place').css('display','none');
                if ($('#sub_place').val()){
                    $('#sub_place').val($('#sub_place_id').val()).trigger("change");
                }
            }
        }
    });
}


//ilçelere ait mahalle ve semtler.
export function getSubCategories(cat_id) {
    $('#opt_loader_cat').css('display','inline');
    $.ajax({
        url: Routing.generate('ajax_get_sub_categories'),
        type: "GET",
        dataType: "json",
        data: {id: cat_id},
        statusCode: {
            200: function (responseObject, textStatus, errorThrown) {
                $('#sub_cat').empty();
                $.each(responseObject, function (key, value) {
                    //select2 add dynamically
                    let newOption = new Option(value.name, value.short_name, false, false);
                    $('#sub_cat').append(newOption);
                });
                //$('#sub_cat').html(html);
                $('#opt_loader_cat').css('display','none');
                if ($('#sub_cat').val()){
                    $('#sub_cat').val($('#sub_cat_id').val()).trigger("change");
                }
            }
        }
    });
}