import $ from "jquery";

/**
 * Left Menu - Active Dynamically
 */
let current_route = $('#current_route').val();
let active = {'font-weight': 'bold'};
//console.log(current_route);


/**
 * Dashboard Menu
 */
if (current_route === 'app_dashboard') {
    $('._dashboad_menu').children('a').css(active);
}

/**
 * Advert Menu
 */
if (current_route === 'advert_index' || current_route === 'advert_new') {
    $('._advert_menu').toggleClass('menu-open');
    $('._advert_menu').children('a').css(active);
    $('._advert_menu').children('.treeview-menu').css('display', 'block');
    $('._advert_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'advert_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'advert_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}

/**
 * Announce Menu
 */
if (current_route === 'announce_index' || current_route === 'announce_new') {
    $('._announce_menu').toggleClass('menu-open');
    $('._announce_menu').children('a').css(active);
    $('._announce_menu').children('.treeview-menu').css('display', 'block');
    $('._announce_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'announce_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'announce_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}

/**
 * Place Menu
 */
if (current_route === 'place_index' || current_route === 'place_new') {
    $('._place_menu').toggleClass('menu-open');
    $('._place_menu').children('a').css(active);
    $('._place_menu').children('.treeview-menu').css('display', 'block');
    $('._place_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'place_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'place_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}

/**
 * Places to Visit Menu
 */
if (current_route === 'places_to_visit_index' || current_route === 'places_to_visit_new') {
    $('._places_to_visit_menu').toggleClass('menu-open');
    $('._places_to_visit_menu').children('a').css(active);
    $('._places_to_visit_menu').children('.treeview-menu').css('display', 'block');
    $('._places_to_visit_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'places_to_visit_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'places_to_visit_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}

/**
 * Municipality Menu
 */
if (current_route === 'municipality_index' || current_route === 'municipality_new') {
    $('._municipality_menu').toggleClass('menu-open');
    $('._municipality_menu').children('a').css(active);
    $('._municipality_menu').children('.treeview-menu').css('display', 'block');
    $('._municipality_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'municipality_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'municipality_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}

/**
 * Event Menu
 */
if (current_route === 'event_index' || current_route === 'event_new') {
    $('._event_menu').toggleClass('menu-open');
    $('._event_menu').children('a').css(active);
    $('._event_menu').children('.treeview-menu').css('display', 'block');
    $('._event_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'event_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'event_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}


/**
 * Article Menu
 */
if (current_route === 'article_index' || current_route === 'article_new') {
    $('._article_menu').toggleClass('menu-open');
    $('._article_menu').children('a').css(active);
    $('._article_menu').children('.treeview-menu').css('display', 'block');
    $('._article_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'article_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'article_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}


/**
 * Business Menu
 */
if (current_route === 'business_index' || current_route === 'business_new') {
    $('._business_menu').toggleClass('menu-open');
    $('._business_menu').children('a').css(active);
    $('._business_menu').children('.treeview-menu').css('display', 'block');
    $('._business_menu').children('.treeview-menu').children('li').each(function (index) {
        if (current_route === 'business_new' && index === 0) {
            $(this).children('a').css(active);
        }
        if (current_route === 'business_index' && index === 1) {
            $(this).children('a').css(active);
        }
    });
}


/**
 * Categories Menu
 */

let category_routes = [
    "business_category_index", "business_category_new", "ad_category_new", "ad_category_index",
    "ad_sub_category_new", "ad_sub_category_index", "p_t_v_category_new", "p_t_v_category_index"
];

if (category_routes.includes(current_route)) {
    $('._category_menu').toggleClass('menu-open');
    $('._category_menu').children('a').css(active);
    $('._category_menu').children('.treeview-menu').css('display', 'block');

    if (current_route === 'business_category_new' || current_route === 'business_category_index') {
        $('._business_category_menu').toggleClass('menu-open');
        $('._business_category_menu').children('a').css(active);
        $('._business_category_menu').children('.treeview-menu').css('display', 'block');
        $('._business_category_menu').children('.treeview-menu').children('li').each(function (index) {
            if (current_route === 'business_category_new' && index === 0) {
                $(this).children('a').css(active);
            }
            if (current_route === 'business_category_index' && index === 1) {
                $(this).children('a').css(active);
            }
        });
    }

    if (current_route === 'ad_category_new' || current_route === 'ad_category_index') {
        $('._ad_category_menu').toggleClass('menu-open');
        $('._ad_category_menu').children('a').css(active);
        $('._ad_category_menu').children('.treeview-menu').css('display', 'block');
        $('._ad_category_menu').children('.treeview-menu').children('li').each(function (index) {
            if (current_route === 'ad_category_new' && index === 0) {
                $(this).children('a').css(active);
            }
            if (current_route === 'ad_category_index' && index === 1) {
                $(this).children('a').css(active);
            }
        });
    }

    if (current_route === 'ad_sub_category_new' || current_route === 'ad_sub_category_index') {
        $('._ad_sub_category_menu').toggleClass('menu-open');
        $('._ad_sub_category_menu').children('a').css(active);
        $('._ad_sub_category_menu').children('.treeview-menu').css('display', 'block');
        $('._ad_sub_category_menu').children('.treeview-menu').children('li').each(function (index) {
            if (current_route === 'ad_sub_category_new' && index === 0) {
                $(this).children('a').css(active);
            }
            if (current_route === 'ad_sub_category_index' && index === 1) {
                $(this).children('a').css(active);
            }
        });
    }

    if (current_route === 'p_t_v_category_new' || current_route === 'p_t_v_category_index') {
        $('._ptv_category_menu').toggleClass('menu-open');
        $('._ptv_category_menu').children('a').css(active);
        $('._ptv_category_menu').children('.treeview-menu').css('display', 'block');
        $('._ptv_category_menu').children('.treeview-menu').children('li').each(function (index) {
            if (current_route === 'p_t_v_category_new' && index === 0) {
                $(this).children('a').css(active);
            }
            if (current_route === 'p_t_v_category_index' && index === 1) {
                $(this).children('a').css(active);
            }
        });
    }

}



/**
 * Feedback Menu
 */
if (current_route === 'app_feedback') {
    $('._feedback_menu').children('a').css(active);
}