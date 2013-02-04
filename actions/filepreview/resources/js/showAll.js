X.actionLoaded(function(event, fn, params) {
   fn('.action_container').css({'overflow': 'hidden'});
   fn('.image_container').bxGallery({
        maxwidth: '',              // if set, the main image will be no wider than specified value (in pixels)
	maxheight: '',             // if set, the main image will be no taller than specified value (in pixels)
	thumbwidth: 90,           // thumbnail width (in pixels)
	thumbcrop: true,          // if true, thumbnails will be a perfect square and some of the image will be cropped
	croppercent: .35,          // if thumbcrop: true, the thumbnail will be scaled to this
	                                   // percentage, then cropped to a square
        thumbplacement: 'right',  // placement of thumbnails (top, bottom, left, right)
	thumbcontainer: 150,        // width of the thumbnail container div (in pixels)
	opacity: .7,               // opacity level of thumbnails
	load_text: _('Loading images'),             // if set, text will display while images are loading
	load_image: 'http://i302.photobucket.com/albums/nn92/wandoledzep/spinner.gif',
	                                   // image to display while images are loading
	wrapperclass: 'outer'      // classname for outer wrapping div
    }); 
});
