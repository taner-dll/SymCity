import '../css/dashboard.scss';
import Swal from 'sweetalert2';
import InputMask from 'inputmask';

require("bootstrap-datepicker/dist/js/bootstrap-datepicker.min");
require("bootstrap-datepicker/dist/locales/bootstrap-datepicker.tr.min");

import {scorePassword} from './utils/password_strength_checker';


let tel_mask = new InputMask("(999) 999-9999");
tel_mask.mask($('#gsm'));


$('#birthday').datepicker({
    format: "dd/mm/yyyy",
    language: "tr",
    orientation: "bottom left"
});


const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 8000
});

const Toast_Center = Swal.mixin({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: 3000
});

if ($('#success_message').val()) {
    Toast_Center.fire({
        title: $('#success_message').val(),
        type: 'success',
    });
}
//sweetalert2 end


$('#password').on('keyup', function () {
    //check password strength
    scorePassword(this.value);
});


$('#update_password_btn').on('click', function () {

    let password = $('#password').val();
    let re_password = $('#re_password').val();

    if (password !== re_password) {
        Toast_Center.fire({
            title: "Parolalar Uyuşmuyor!",
            type: "warning",
        });
        return false;
    }

    if (password.length === 0) {
        Toast_Center.fire({
            title: "Lütfen yeni parolanızı giriniz!",
            type: "warning",
        });
        return false;
    }


    $.ajax({
        url: Routing.generate('user_update_password'),
        type: "POST",
        dataType: "json",
        data: {password: password},
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
                Toast_Center.fire({
                    title: "Parolanız başarılı bir şekilde güncellendi.",
                    type: "success",
                });

            }
        }
    }).done(function (data) {
        console.log(data);
    }).fail(function (jqXHR, textStatus) {
        console.log('Something went wrong: ' + textStatus);
    }).always(function (jqXHR, textStatus) {
        console.log('Ajax request was finished')
    });


});


//cropper
import 'cropper/dist/cropper.min';
import 'jquery-cropper/dist/jquery-cropper.min';

const $image = $("#selected_image");
const $input = $('#profile_picture');

$input.on('change', function () {

    /**
     * Dosya uzantı kontrolü.
     * Sadece jpeg, jpg, png
     * @type {*[]}
     */
    let ext = ['jpeg', 'jpg', 'png'];
    if (!ext.includes(this.files[0]['name'].split(".")[1])) {
        Swal.fire({
            type: 'error',
            title: 'Geçersiz Dosya Formatı',
            text: 'Yalnızca jpg, png formatında görsel yükleyiniz.',
            confirmButtonText: 'Tamam',
        });
        // Destroy the old cropper instance
        $image.cropper('destroy');
        return false;
    }


    $('#myModal').modal('show');

    let oFReader = new FileReader();
    oFReader.readAsDataURL(this.files[0]);
    oFReader.onload = function () {

        // Destroy the old cropper instance
        $image.cropper('destroy');

        // Replace url
        $image.attr('src', this.result);

        // Start cropper
        $image.cropper({
            aspectRatio: 1,
            dragMode: 'move',
            cropBoxMovable: true,
            cropBoxResizable: true,
            guides: false,
            minContainerWidth: 800,
            minContainerHeight: 432,
            viewMode: 2 //?

        });
    };


});


$('#crop_image').on('click', function () {

    let imageData = $image.cropper('getCroppedCanvas', {
        width: 870,
        height: 470,
        fillColor: '#fff',
    }).toDataURL('image/jpeg');

    $('#preview').attr('src', imageData);
    $('#cropped_image').val(imageData);
});

$('#myModal').on('hidden.bs.modal', function () {
    // Destroy the old cropper instance
    $image.cropper('destroy');

    // Replace url
    $image.val('');
    //$input.val('');
});
$('#profile_picture_submit_btn').on('click', function () {


    if ($('#cropped_image').val() === "") {
        Toast_Center.fire({
            title: "Lütfen profil resmi seçiniz!",
            type: "warning",
        });
        return false;
    }


    $('#profile_picture_form').submit();

});
$('#update_user_info_btn').on('click', function () {

    let username = $('#username').val();
    let firstname = $('#firstname').val();
    let lastname = $('#lastname').val();
    let birthday = $('#birthday').val();
    let gender = $('#gender').val();
    let gsm = $('#gsm').val();
    let email = $('#email').val();

    let username_input_loader = $('#username_input_loader');
    let username_input_icon = $('#username_input_icon');
    let username_form_group = $('#username_form_group');


    //check username length. it must be minimum 5 chars.
    if (username.length < 5) {
        Toast_Center.fire({
            title: "Kullanıcı adınız en az 5 karakter olmalıdır!",
            type: "warning",
        });
        return false;
    } else {

        username_input_icon.hide();
        username_input_loader.show();

        $.ajax({
            url: Routing.generate('user_check_username'),
            type: "GET",
            dataType: "json",
            data: {username: username},
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
                302: function (responseObject, textStatus, errorThrown) {
                    // Found (302)
                    console.log("kayitli kullanıcı adı");
                    username_form_group.addClass('has-error');
                    username_input_loader.hide();
                    //username_form_group.removeClass('has-success');

                    Swal.fire({
                        type: 'error',
                        title: 'Kayıtlı Kullanıcı Adı!',
                        text: username + ' kullanımdadır, lütfen farklı bir kullanıcı adı deneyin.',
                        confirmButtonText: 'Tamam'
                    });

                },
                200: function (responseObject, textStatus, errorThrown) {

                    console.log("kullanıcı adı uygun");
                    //username_form_group.addClass('has-success');
                    username_form_group.removeClass('has-error');


                    //update info

                    $.ajax({
                        url: Routing.generate('user_update_info'),
                        type: "POST",
                        dataType: "json",
                        data: {
                            username: username,
                            firstname: firstname,
                            lastname: lastname,
                            birthday: birthday,
                            gender: gender,
                            gsm: gsm,
                            email:email
                        },
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

                                Toast_Center.fire({
                                    title: "Bilgileriniz başarılı bir şekilde güncellendi.",
                                    type: "success",
                                });

                                $('#username_text').html(username);
                                $('#firstname_tab_text').html(firstname);
                                $('#user_title_text').html(firstname+" "+lastname);
                                $('#gsm_text').html(gsm);
                                $('#birthday_text').html(birthday);
                                $('#gender_text').html(gender);
                            }
                        }
                    }).done(function (data) {
                        //response text
                        //console.log(data);
                    }).fail(function (jqXHR, textStatus) {
                        //hata anında
                        //console.log('Something went wrong: ' + textStatus);
                    }).always(function (jqXHR, textStatus) {
                        //her koşulda çalışır
                        //console.log('Ajax request was finished')
                    });

                }
            }
        }).done(function (data) {
            //response text
            //console.log(data);
            username_input_loader.hide();
            username_input_icon.show();

        }).fail(function (jqXHR, textStatus) {
            //hata anında
            //console.log('Something went wrong: ' + textStatus);
        }).always(function (jqXHR, textStatus) {
            //her koşulda çalışır
            //console.log('Ajax request was finished')
        });
    }






});


/**
 * Kapalı kutular açıldığında slide çalışmıyordu. Bu şekilde düzeltildi.
 */
$("[data-widget='collapse']").click(function () {
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