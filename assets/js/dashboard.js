import Swal from 'sweetalert2';
import InputMask from 'inputmask';

let date_mask = new InputMask({ 'alias': 'datetime', 'inputFormat': 'dd/mm/yyyy'  });
date_mask.mask($('#birthday'));

let tel_mask = new InputMask("(999) 999-9999");
tel_mask.mask($('#gsm'));




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
//sweetalert2 end

/**
 * Kapalı kutular açıldığında slide çalışmıyordu. Bu şekilde düzeltildi.
 */
$("[data-widget='collapse']").click(function() {
    //Find the box parent........
    let box = $(this).parents(".box").first();
    //Find the body and the footer
    let bf = box.find(".box-body, .box-footer");
    if (!$(this).children().hasClass("fa-plus")) {
        $(this).children(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
        bf.slideUp();
    } else {
        //Convert plus into minus
        $(this).children(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
        bf.slideDown();
    }
});