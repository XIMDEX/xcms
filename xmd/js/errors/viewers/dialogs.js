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
 *  @version $Revision$
 */


var Dialogs = Class.create({

	wrapper: 'actioncontainer',


	//Initialize alert objets
	initialize: function() {
	},

	/** ******************************* ACTIONS ************************************************ */
	clear: function(obj_notify, id_form )  {
		$(id_form).descendants().each(function(field) {
			if(field.hasClassName('field_in_error') ) {
				$(field.id).removeClassName('field_in_error');
			}
		}
		)
	},

	notify: function(obj_notify, id_form) {
		var msg = '';

		if(obj_notify.validateError()) {
			msg += "<p> Atenci&oacute;n. "+obj_notify.alerts[obj_notify.level_index][obj_notify.E_CRITICAL].text+":</p>"
	;		msg += "<ul>";
			for(var i= 1; i< obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING].length; i++) {
				try {
					msg += "<li>"+ /* ( "+ obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id + " ) */" " +  	obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].text+"</li>";
					if( obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id != id_form && obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id  ) {
						$(obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id).addClassName('field_in_error');
					}

				} catch(e) { }
			}
			msg += "</ul>";
			//put focus in first field with error
			if( obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING].length && obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][1].id != id_form && obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][1].id  ) {
				$(obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][1].id).activate();
			}

		}else {
			msg += " Atención. "+obj_notify.alerts[obj_notify.level_index][obj_notify.SUCCESSFULL].text+":";
		}
 		this._showDialog("Aviso", msg);
	},

	display: function(msg) {
		this._showDialog("Informaci&oacute;n", msg);
	},

	question: function(question, obj, func_ok,  param) {
		this._showDialog("Pregunta", question, "prompt");
		try{
			Event.observe($('dialog-input_ok').id, 'click', obj[func_ok].bindAsEventListener(obj, param));
			}catch(err) { }
		return true;
	},

	resultActionOk: function(obj_notify, id_form, id_node) {
		msg = " <ul> ";
// 		msg = "<p>"+obj_notify.alerts[obj_notify.level_index][obj_notify.SUCCESSFULL].text+"</p>";
			for(var i= 1; i< obj_notify.alerts[obj_notify.level_index][obj_notify.INFORMATIVE].length; i++) {
				try {
					msg += "<li>"+obj_notify.alerts[obj_notify.level_index][obj_notify.INFORMATIVE][i].text+"</li>";

				} catch(e) { }
			}
		msg +=  " </ul> ";


		if(id_form != "undefined") {
	 		$(id_form).replace(msg);
		}

		//Reload node
		if(id_node != "undefined") {
			parent.parent.frames['toolbar'].reloadNode(id_node);
		}
	},


	/** ************************************** OTHERS ************************************** */
	// calculate the current window width //
	_pageWidth: function () {
		return window.innerWidth != null ? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body != null ? document.body.clientWidth : null;
	},


	// calculate the current window height //
	_pageHeight: function () {
		return window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;
	},

	// calculate the current window vertical offset //
	_topPosition: function () {
		return typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ? document.body.scrollTop : 0;
	},

	// calculate the position starting at the left of the window //
	_leftPosition: function () {
		return typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement && document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft ? document.body.scrollLeft : 0;
	},


	// build/show the dialog box, populate the data and call the this._fadeDialog function //
	_showDialog: function ( title,message,type) {
		if(!type) {
			type = 'warning';
		}
		var dialog;
		var dialogheader;
		var dialogclose;
		var dialogtitle;
		var dialogcontent;
		var dialogmask;

		dialog = document.createElement('div');
		dialog.id = 'dialog';
		dialogheader = document.createElement('div');
		dialogheader.id = 'dialog-header';
		dialogtitle = document.createElement('div');
		dialogtitle.id = 'dialog-title';
		dialogclose = document.createElement('div');
		dialogclose.id = 'dialog-close'
		dialogcontent = document.createElement('div');
		dialogcontent.id = 'dialog-content';
		dialogmask = document.createElement('div');
		dialogmask.id = 'dialog-mask';
		document.body.appendChild(dialogmask);
		document.body.appendChild(dialog);
		dialog.appendChild(dialogheader);
		dialogheader.appendChild(dialogtitle);
		dialogheader.appendChild(dialogclose);
		dialog.appendChild(dialogcontent);;


		Event.observe(dialogclose.id, 'click', this.hideDialog.bindAsEventListener(this));

		dialog.style.opacity = .00;
		dialog.style.filter = 'alpha(opacity=0)';

		var width = this._pageWidth();
		var height = this._pageHeight();
		var left = this._leftPosition();
		var top = this._topPosition();
		var dialogwidth = dialog.offsetWidth;
		var dialogheight = dialog.offsetHeight;
		var topposition = top + (height / 3) - (dialogheight / 2);
		var leftposition = left + (width / 2) - (dialogwidth / 2);
		dialog.style.top = topposition + "px";
		dialog.style.left = leftposition + "px";
		dialogheader.className = type + "header";
		dialogtitle.innerHTML = title;
		dialogcontent.className = type;
		dialogcontent.innerHTML = message;


		var buttons = document.createElement('div');
		buttons.id = 'dialog-buttons';
		dialogcontent.appendChild(buttons);


		 if(type == "prompt") {
			//Button cancel
			var input_nok = document.createElement('input');
			input_nok.id = 'dialog-input_nok';
			input_nok.type = 'button';
			input_nok.value = _('Cancel');
			buttons.appendChild(input_nok);
			Event.observe(input_nok.id, 'click', this.hideDialog.bindAsEventListener(this));

			//Button Acept
			var input_ok = document.createElement('input');
			input_ok.id = 'dialog-input_ok';
			input_ok.value = _('Accept');
			input_ok.type = 'button';
			buttons.appendChild(input_ok);
			Event.observe(input_ok.id, 'click', this.hideDialog.bindAsEventListener(this));


		}else {
			//Button Acept
			var input_ok = document.createElement('input');
			input_ok.id = 'dialog-input_ok';
			input_ok.type = 'button';
			input_ok.value = _('Accept');
			buttons.appendChild(input_ok);
			Event.observe(input_ok.id, 'click', this.hideDialog.bindAsEventListener(this));
		}


		var content = document.getElementById(this.wrapper);
		dialogmask.style.height = content.offsetHeight + 'px';
		dialogclose.style.visibility = "visible";
		dialog.style.opacity = (90 / 100);
		dialog.style.filter = 'alpha(opacity=90)';

	},

	// hide the dialog box //
	hideDialog: function (event) {
		Event.stop(event);
		$('dialog-buttons').remove();
		$('dialog-close').remove();
		$('dialog-title').remove();
		$('dialog-header').remove();
		$('dialog-content').remove();
		$('dialog').remove();
		$('dialog-mask').remove();
	},

});


View = new Dialogs();
View.initialize();