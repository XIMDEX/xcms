/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision: 8120 $
 */




var ChannelsToolBox = Object.xo_create(FloatingToolBox, {

	initialize: function(tool, editor) {
		AttributesToolBox._super(this, 'initialize', tool, editor);
		this.setTitle(_('Channels'));
		this.select = $('<select></select>')
			.addClass('wide')
			.attr('id', 'kupu-channels');
		$(this.editor.channels).each(function(index, elem) {
			this.select.append(
				$('<option></option>')
					.val(elem.channelId)
					.html(elem.channel)
			);
		}.bind(this));
		this.select = this.select[0];
		this.getChannelId();
		this._createAttributeInput(_('Select a channel'), this.select);
		$('#kupu-toolbox-channels').unbind().remove();
	},
	updateState: function(options) {
		// None at this moment
	},
	updateButtonHandler: function(event) {

		loadingImage.showLoadingImage();
		var newChannel = this.select.options[this.select.selectedIndex].text;
		var channelId = this.select.options[this.select.selectedIndex].value;
		this.editor.getXimDocument()._channelId = channelId;
		var docxap = this.editor.getXimDocument().getRootElement();
		docxap.attributes['canal'] = newChannel;
		// TODO: set 'canales' & 'canales_desc' attributes?

		this.setActionDescription(_('Channel changed to') + ' ' + newChannel);
		this.editor.logMessage(_('Channel changed to') + ' ' + newChannel);
		this.editor.updateEditor({caller: this, updateContent: false});
		loadingImage.hideLoadingImage();
	},
	getChannelId: function(){
                var channelId = this.select.options[this.select.selectedIndex].value;
                this.editor.getXimDocument()._channelId = channelId;
	}
});

