
(function($) {
    $.fn.xTooltip = function(options) {
        var opts = $.extend({}, $.fn.xTooltip.defaults, options);
        return this.each(function(i) {
            var $this = $(this);
            var offset = $this.offset();
            if(opts.closeButton){
                $('body').append('<div class="tooltip"><div class="tooltipbtn">x</div><div class="tooltipinner"></div></div>');
                $('.tooltipbtn:eq('+ i +')').bind('click', function(){
                    disappear();
                });
            }else{
                if(opts.position == 'up')
                	$('body').append($('<div class="tooltip"><div class="tooltipinner">' + i + '</div><div class="tooltiparrow_' + opts.position + '"><div></div></div></div>').hide());
                else
                	$('body').append($('<div class="tooltip"><div class="tooltiparrow_' + opts.position + '"><div></div></div><div class="tooltipinner">' + i + '</div></div>').hide());
            }
            var xTooltip = $('.tooltip:eq('+ i +')');        

            $this.bind('mouseover', function(){
				appear();
            });
            
            $this.bind('mouseout', function(){
				disappear();
            });

            var appear = function(){
                xTooltip.show();
                xTooltip.children('.tooltipinner').text($this.attr('xim:title'));
                xTooltip.css({
					top: (opts.position == 'up') ? offset.top - xTooltip.outerHeight() : ((opts.position == 'down') ? offset.top + xTooltip.outerHeight() : offset.top + (($this.outerHeight() - xTooltip.outerHeight())/2)),
					left: (opts.position == 'left') ? offset.left - Math.abs(xTooltip.outerWidth()) : ((opts.position == 'right') ? offset.left + Math.abs($this.outerWidth()) : offset.left + (($this.outerWidth() - xTooltip.outerWidth())/2)),
					zIndex: 10000,
					opacity: (opts.animate) ? 0 : opts.fade
                });
                if(opts.animate) {
					xTooltip.animate({
						opacity: opts.fade
					}, opts.duration, opts.easing, opts.callbackOn);
                }
            }
            var disappear = function(){
                xTooltip.css({
                	opacity: (opts.animate) ? opts.fade : 0,
                	zIndex: -1
                });
                if(opts.animate) {
	                xTooltip.animate({
	                    opacity:0,
	                }, opts.duration, opts.easing, opts.callbackOff);
                }
            }
        });
    }
    $.fn.xTooltip.defaults = {
        fade:  0.9,
        duration: 'fast',
        easing: 'swing',
        position: 'down',
        closeButton: false,
        animate: false,
        callbackOn: function(){},
        callbackOff: function(){}
    };
})(jQuery);