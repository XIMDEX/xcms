<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="footer_block" match="footer_block">
        <footer>
            <xsl:apply-templates/>
        </footer>
        <script>
            $(document).ready(function () {

            //dropdownHover ();
            carouselResize();
            scroll();
            //responsive ();
            });

            $(window).load(function () {

            //responsive ();
            });

            $(window).resize(function () {

            carouselResize();
            //responsive ();
            });

            $(window).scroll(function () {

            var navbar = $('#navbar');

            if (navbar.offset().top > 50) navbar.addClass('menu-offset');
            else                          navbar.removeClass('menu-offset');
            });


            function scroll() {

            $('.scroll').click(function (event) {

            event.preventDefault();

            var destino = $(this).attr('href');

            if (!destino) return;
            if ($('#menu-fixed').hasClass('in')) $('.navbar-toggle').click();

            $('html, body').stop().animate({

            scrollTop: $(destino).offset().top
            }, 1500, 'easeInOutExpo');
            });
            }

            function carouselResize() {

            $('.item', $('.carousel-section')).css({height: $(window).height()});
            }

            function dropdownHover() {

            $('.dropdown-toggle').click(function (event) {

            event.stopPropagation();
            }).mouseenter(function () {

            $('.open').removeClass('open');
            $(this).parent().addClass('open');
            });

            $('.btn-group, .dropdown').mouseleave(function () {

            $('.open').removeClass('open');
            });
            }

            /*function responsive () {

            var ventana = $(window).width ();

            if (ventana >= 1185)  return lg ();
            if (ventana >= 977)   return md ();
            if (ventana >= 753)   return sm ();
            return xs ();
            }

            function lg () {

            }

            function md () {

            }

            function sm () {

            }

            function xs () {

            }*/
        </script>
    </xsl:template>
</xsl:stylesheet>