
(function($) {


    $.fn.gallerizer = function(options) {

            /**
             * <p>Plugin options</p>
             */
             var options = $.extend($.fn.gallerizer.defaults, options);

            /**
             * <p>Element where the plugin is applied</p>
             */
             var $that = $(this);

            /*
             * Auxiliary variables
             */
             var i = 0;
             var tam = $that.find('img').length;
             var $loader = null;

            // Call the preload images function to start the plugin
            preloadImages();


            /**
             * <p>Performs the preload images and then adds the plug-in functionality</p>
             */
             function preloadImages() {
                $that.hide();
                if (options.loading_text !== undefined && options.loading_text !== '') {
                    $loader = $("<div/>").addClass('gallerizer_loader').html(options.loading_text);
                    $that.before($loader);
                }
                else {
                    $loader = $("<div/>").addClass('gallerizer_loader').html("<img src='" + options.loading_image + "' />");
                    $that.before($loader);
                }

                //initializeGallery();
                $that.find('img').each(function() {
                    var $this = $(this);
                    var src = $this.attr('src');

                    $this.attr('src', '');

                    $this.load(function() {
                        i++;
                        if (i == tam) {
                            initializeGallery();
                        }
                    });

                    $this.attr('src', src);


                });
            }

            /**
             * <p>Initializes the gallery</p>
             */
             function initializeGallery() {
                $that.wrap('<div class="' + options.wrapper_class + '"></div>');

                $that.find('li').each(function() {
                    var $this = $(this);
                    $this.css("position", "relative");
                    var $img = $this.find('img');
                    var title = $img.attr('title');

                    /* create image caption */
                    var $caption = $("<div/>").addClass('caption').html(title);
                    $this.append($caption);

                    // binding click event
                    $this.bind('click', function() {
                        var $li = $(this);
                        if ($li.hasClass('viewing'))
                            // Don't do anything whether the image is being displayed
                        return;
                            //console.log($(this));
                        /* Get the last element in the "row" that is not of "opened" class.
                         (row is virtual because all elements are displayed in a floating way,
                         allowing responsive design).
                         This element will be used to append the new element which will show
                         the image in a bigger size and more information about it
                         */
                         var $anchorPoint = null;
                         var position = $li.position();
                         $li.nextAll().andSelf().filter(":not(.opened)").each(function() {
                            if ($(this).position().top > position.top) {
                                $anchorPoint = $(this).prevAll(":not(.opened)").first();
                                return false;
                            }
                        });

                         if ($anchorPoint === null) {
                            $anchorPoint = $li.nextAll().andSelf().filter(":not(.opened)").last();
                        }

                        /* create the new element which will contain the image in a bigger size and information about it, also a close button
                         li.opened div.image will containg the image and
                         li.opened div.info will contain the image information
                         li.opened span.close will be the close button
                         li.opened span.change_bg will be the change bg button
                         */
                         var $newElement = $("<li/>").addClass("opened light");
                         $newElement.css("display", "block");
                         var $divImage = $("<div/>").addClass("image").append($img.clone());
                         $newElement.append($divImage);






                    var $divInfoContainer = $("<div/>").addClass("image_info");
                    $.each(options.information, function(i, info) {
                        var text = $img.attr(info.attribute);
                        var label = info.label;
                        var theclass = "img_" + info.attribute.replace("data-", "");

                        $divInfoContainer.append($("<div/>").addClass("info "+theclass).html("<span class='label'>" + label + ":</span><span class='value'>" + text + "</span>"));



                    });

                    $newElement.append($divInfoContainer);

                    var $close = $('<span/>').addClass('close icon-font').text('Close');

                    $newElement.append($close);

                   // Close the opened image

                   $close.click(function(){
                       $(".opened").slideUp(400, function() {
                        $(".opened").remove();
                        $(".viewing", $that).removeClass("viewing");

                    });
                   });



                   var $next = $anchorPoint.next();

                        // If there is a .opened element for this "row", reuse it
                        if ($next.length === 1 && $next.hasClass("opened")) {
                            $next.html($newElement.html());
                            $newElement = $next;
                        }

                        else {
                            // Remove the previous .opened element if exists
                            $(".opened", $that).slideUp(400, function() {
                                $(this).remove();
                            });

                            $newElement.hide();
                            $anchorPoint.after($newElement);
                            $newElement.slideDown(500);
                        }

                        // Mark the item as being displayed
                        $(".viewing", $that).removeClass("viewing");
                        $li.addClass("viewing");

                         // Change background color

                    var $change_bg = $('<span/>', {class:'swiper'}).append($('<span/>').text('Change background color'));

                    $change_bg.click(function(){
                        var $opened = $(".opened");
                        if($opened.hasClass('dark')){
                            $opened.removeClass('dark');
                            $opened.addClass('light');
                        }

                        else{
                            $opened.removeClass('light');
                            $opened.addClass('dark');
                        }


                    });
                    $newElement.append($change_bg);


                        // Add full image functionality if needed
                        if (options.full_image_link_callback !== null) {
                            $("img", $newElement).bind('click', function() {
                                options.full_image_link_callback.call($(this), $(this));
                            });

                            if (options.full_image_link) {
                                var $divFullLink = $("<div/>").addClass('full_view icon-font');
                                var $span = $("<span/>").html(options.full_image_link_label).bind('click', function() {
                                    options.full_image_link_callback.call($("img", $divImage), $("img", $divImage));
                                });
                                $divFullLink.append($span);
                                $(".image", $newElement).append($divFullLink);

                            }
                        }

                    });

});

$loader.hide();
$that.fadeIn();
}
};

$.fn.gallerizer.defaults =
{
    loading_text: 'Loading Images',
    loading_image: '',
    wrapper_class: 'outer',
    full_image_link: false,
    full_image_link_label: "View full image",
    full_image_link_callback: null,
    show_information: true,
    information: [{attribute: "title", label: "Name"}]
};

})(jQuery);



