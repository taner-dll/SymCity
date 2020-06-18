import '../../css/front_end/web_site.scss';
import 'select2';
import Routing from "../Routing";





//select2 theme
const bootstrap_theme = 'bootstrap';


//all select definitions
const category = $('#select_category');
const business_category = $('#select_business_category');
const advert_category = $('#select_advert_category');
const advert_sub_category = $('#select_advert_subcategory');
const place = $('#select_location');




/**
 * select2 start
 */
category.select2({
    theme: bootstrap_theme,
    placeholder: "İşlem Seçiniz...",
});


//----------------------------------------------------------
business_category.select2({
    theme: bootstrap_theme,
    placeholder: "İşletme Kategorisi Seçiniz",
});

//hidden by default
business_category.next(".select2-container").hide();
//----------------------------------------------------------



//----------------------------------------------------------
advert_category.select2({
    theme: bootstrap_theme,
    placeholder: "İlan Kategorisi Seçiniz",
});

//hidden by default
advert_category.next(".select2-container").hide();
//----------------------------------------------------------



//----------------------------------------------------------
advert_sub_category.select2({
    theme: bootstrap_theme,
    placeholder: "İlan Alt Kategorisi Seçiniz",
});

//hidden by default
advert_sub_category.next(".select2-container").hide();
//----------------------------------------------------------


place.select2({
    theme: bootstrap_theme,
    placeholder: "Bölge Seçiniz"
});

/**
 * select2 end
 */


// Kategori seçimi yapılmışsa alt kategorileri göster
category.on('change', function () {

    business_category.next(".select2-container").hide();
    business_category.val('').trigger('change');

    advert_category.next(".select2-container").hide();
    advert_category.val('').trigger('change');

    advert_sub_category.next(".select2-container").hide();
    advert_sub_category.val('').trigger('change');


    if (this.value === 'business') {
        business_category.next(".select2-container").show();
    }

    if (this.value === 'advert') {
        advert_category.next(".select2-container").show();
    }

});


advert_category.on('change', function (e) {

    let data = advert_category.select2('data');

    let html = '<option></option><option value="all">Tüm İlan Alt Kategorilerinde Ara</option>';


    /**
     * ilan kategorisi seçimi "tümü" değilse
     */
    if (data[0].id!=='all' && (data[0].id).length>0){

        $.ajax({
            url: Routing.generate('ajax_ad_subcategories'),
            type: "GET",
            dataType: "json",
            data: {
                category: data[0].id //1 (vasita)
            },

            success: function (categories) {

                $.each(categories, function(item, value){

                    //console.log(value.shortname);
                    html += '<option value="'+value.shortname+'">'+value.shortname_translated+'</option>';

                });
                advert_sub_category.html(html);

            },
            error: function (err) {
                console.log(err);
            }
        });
    }







    advert_sub_category.next(".select2-container").show();


});


