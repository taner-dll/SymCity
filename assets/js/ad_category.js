import '../css/ad_category.scss';

import Swal from 'sweetalert2';
import Sortable from 'sortablejs';

/**
 * Fake Routing oluşturuldu.
 * routing.yaml expose: true yapmayı unutma.
 */
import Routing from './Routing';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

if ($('#success_message').val()) {
    Toast.fire({
        title: $('#success_message').val(),
        type: 'success',
    })
}

/*
Sortable.create(document.getElementById('simpleList-vehicle'), {
    //handle: '.glyphicon-move',
    animation: 200,

});

Sortable.create(document.getElementById('simpleList-animals'), {
    //handle: '.glyphicon-move',
    animation: 200,

});

 */

/**
 * HTML input hidden içerisindeki twig arrayi parse ediyoruz.
 * @type {any}
 */
let all_names = JSON.parse($('#all_names').val());
//console.log(all_names);
//console.log(all_names.length);
let an_length = parseInt(all_names.length);

/**
 * dynamic sortable creation
 */
let all_names_array = [];
for (let i = 0; i < an_length; i++) {
    //console.log(all_names[i]);

    Sortable.create(document.getElementById('sort_list_' + all_names[i]), {
        //handle: '.drag-area',
        animation: 200,
        ghostClass: 'ghost',
        onEnd: function (evt) {

            let sort_list = [];
            let count_index = 0;
            $(evt.item).parent().find('.list-group-item').each(function () {

                //set data-sort-no value
                $(this).attr('data-sort-no', count_index)

                //create multidimensional with 'shortname => no'
                sort_list.push([$(this).attr('data-shortname'), $(this).attr('data-sort-no')]);

                //sample usages
                //$(this).find('span').text($(this).index() + 1);
                //$(this).attr('data-sort-no',$(this).index());
                count_index++;
            });


            $.ajax({
                url: Routing.generate('ajax_ad_subcategories_sort'),
                type: "POST",
                dataType: "json",
                data: {sortlist: sort_list, cat: all_names[i]},
                statusCode: {
                    /**
                     * Response Manipulation
                     * @param responseObject
                     * @param textStatus
                     * @param jqXHR
                     */
                    404: function(responseObject, textStatus, jqXHR) {
                        // No content found (404)
                        // This code will be executed if the server returns a 404 response
                    },
                    503: function(responseObject, textStatus, errorThrown) {
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
    });

    //all_names_array.push(all_names[i]);

}


