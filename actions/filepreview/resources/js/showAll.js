X.actionLoaded(function(event, fn, params) {
   jQuery('.image_container').gallerizer({
    loading_text: _('Loading images'),             // if set, text will display while images are loading
        loading_image: 'http://i302.photobucket.com/albums/nn92/wandoledzep/spinner.gif',
                                     // image to display while images are loading if no text is set
        wrapper_class: 'thumbs',     // classname for outer wrapping div
        full_image_link: true,
        full_image_link_label: "Ver imagen completa",
        information: [
                {attribute: "title", label: _("Name")},
                {attribute: "data-nodeid", label: _("Node ID")},
                {attribute: "data-mime", label: _("Type")},
                {attribute: "data-dimensions", label: _("Dimensions")},
                {attribute: "data-size", label: _("Size")}

        ],
        // Open image in new window
        full_image_link_callback: function(img) {
                var url = $(img).attr("data-original_path");
                window.open(url, "imageViewer");
        }
    });
});
