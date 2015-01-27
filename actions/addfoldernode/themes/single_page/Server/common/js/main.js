$(document).ready (function () {

    responsive ();
    bootstrap ();
    carouselHeader ();
    dropdownHover ();
    scrollReady ();
    flag ();
    //carouselResize ();
});

$(window).load (function () {

    responsive ();
});

$(window).resize (function () {

    //carouselResize ();
    responsive ();
});

$(window).scroll (function (event) {

    var navbar = $('#navbar');

    if (navbar.offset().top > 50) navbar.addClass ('menu-offset');
    else                          navbar.removeClass ('menu-offset');
});

$(window).bind ('mousewheel DOMMouseScroll', function (event){

    return;

    /*if (! $('#navbar').data ('ready')) return;9

    var actual  = $($('.active', $('#navbar')));
    var prev    = actual.prev ();
    var next    = actual.next ();

    if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0)   scrollTop ($('a', prev).attr ('href'), 0);
    else                                                                        scrollTop ($('a', next).attr ('href'), 0);*/
});

function bootstrap () {

    if (navigator.userAgent.match(/IEMobile\/10\.0/)) {

        var msViewportStyle = document.createElement ('style');

        msViewportStyle.appendChild (document.createTextNode ('@-ms-viewport{width:auto!important}'));
        document.querySelector ('head').appendChild (msViewportStyle);
    }

    var nua       = navigator.userAgent;
    var isAndroid = (nua.indexOf ('Mozilla/5.0') > -1 && nua.indexOf ('Android ') > -1 && nua.indexOf ('AppleWebKit') > -1 && nua.indexOf ('Chrome') === -1);

    if (isAndroid) $('select.form-control').removeClass ('form-control').css ('width', '100%');
}

function flag () {

    var flag    = $('#flag');
    var offset  = flag.outerWidth ();

    flag.css ({
        right: -offset,
        opacity: 1
    })

    flag.data ('abierto', false).click (function () {

        $(this).data ('abierto', ! $(this).data ('abierto')).finish ().animate ({
            right: $(this).data ('abierto') ? 0 : -offset
        }, 500);
    });
}

function carouselHeader () {

    var carousel    = $('#carousel-1');
    var cargando    = $('.cargando', carousel);
    var item        = $('.item', carousel).first ();
    var img         = $('img', item);
    var src         = img.attr ('src');

    img.attr ({src: ''}).load (function () {

        $('img', carousel).css ({opacity: 1});
        cargando.hide ();
    }).attr ({src: src});
}

function scrollReady () {

    $('#navbar').data ('ready', true);
    $('.scroll').click (function (event) {

        event.preventDefault();
        scrollTop ($(this).attr ('href'), 0);
    });
}

function scrollTop (destino, offtop) {

    var navbar = $('#navbar');

    if (! destino || ! navbar.data ('ready')) return;
    if ($('#menu-fixed').hasClass ('in')) $('.navbar-toggle').click ();

    navbar.data ('ready', false);
    $('html, body').stop ().animate({

        scrollTop: $(destino).offset().top - $('#navbar-header', navbar).outerHeight () - offtop
    }, 1500, 'easeInOutExpo', function (event) {

        navbar.data ('ready', true);
    });
}

/*function carouselResize () {

    $('.item', $('.carousel-section')).css ({height: $(window).height ()});
}*/

//DROPDOWNS
function dropdownData (hover) {

    $('.dropdown-toggle').data ('hover', hover);
    dropdownClose ();
}

function dropdownHover () {

    $('.dropdown-toggle')
    .click (function (event) {

        var hover   = $(this).data ('hover');
        var open    = $(this).parent ('.open').length;
        var href    = $(this).attr ('href');
        var ul      = $(this).next ('.dropdown-menu').length;

        if (hover || (open && href && ul)) event.stopPropagation ();
    })
    .mouseenter (function () {

        var menu = $(this).next ('.dropdown-menu');

        if ($(this).data ('hover')) {

            var parent = $(this).parent ();

            menu.css ({minWidth: parent.outerWidth (), top: parent.outerHeight ()});
            parent.addClass ('open');
            return;
        }

        menu.css ({top: '100%'});
    });

    $('.btn-group, .dropdown').mouseleave (function () { dropdownClose (); });
}

function dropdownClose () {

    if ($('.dropdown-toggle').data ('hover')) $('.btn-group.open, .dropdown.open').removeClass ('open');
}
//FIN DROPDOWNS

function responsive () {

    var ventana = $(window).width ();

    if (ventana >= 1185)  return lg ();
    if (ventana >= 977)   return md ();
    if (ventana >= 753)   return sm ();
    return xs ();
}

function lg () {

    dropdownData (true);
}

function md () {

    dropdownData (true);
}

function sm () {

    dropdownData (true);
}

function xs () {

    dropdownData (false);
}