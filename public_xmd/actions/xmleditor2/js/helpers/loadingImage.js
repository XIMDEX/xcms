function loadingImg() {
	// Creating loading image
	this.createLoadingImage = function() {
		var tLayer = $('<div id="tLayer" class="kupu-tLayer"></div>',
		$('.kupu-editorframe')).css({
			display: 'none',
			border: '1px solid #222222',
			'background-color': '#000',
			opacity: '0.3',
			filter: 'alpha(opacity=30)',
			position: 'absolute',
			top: '0px',
			left: '0px',
			'text-align': 'center',
			width: '100%',
			height: '100%',
			zIndex: '1000'
		});
		var wMessage = $('<div id="wMessage" class="kupu-wMessage">' + _('Wait, please...') + '</div>',
		$('.kupu-editorframe')).css({
			display: 'none',
			position: 'absolute',
			top: '35%',
			left: '43%',
			//width: '30%',
			//height: '24px',
			zIndex: '1001'
		});
		$('.kupu-editorframe').append(tLayer);
		$('.kupu-editorframe').append(wMessage);
	}

	this.showLoadingImage = function() {
		$('.kupu-tLayer').show();
		$('.kupu-wMessage').show();
	}


	this.showLoadingImage = function() {
		$('.kupu-tLayer').show();
		$('.kupu-wMessage').show();
	}

	this.hideLoadingImage = function() {
		$('.kupu-tLayer').hide();
		$('.kupu-wMessage').hide();
	}

};

loadingImage = new loadingImg();
