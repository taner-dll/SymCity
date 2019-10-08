import '../css/ad_category.scss';


import Swal from 'sweetalert2';
import Sortable from 'sortablejs';



const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

if($('#success_message').val()){
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
for (let i = 0; i < an_length; i++){
    //console.log(all_names[i]);

    Sortable.create(document.getElementById('sort_list_'+all_names[i]), {
        //handle: '.drag-area',
        animation: 150,

    });
}