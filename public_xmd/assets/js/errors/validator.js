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


//Event.observe(window, 'load', initialize_body, false);

var validator = Class.create({
	checked_elements: Array(),

	//Find elements class: validate, form_reset, form_active
	initialize: function() {
		console.log('initialize')
		this._initialize_form_validations();
		this._initialize_form_actives();
		this._initialize_form_resets();
	 },

	/** ****************************************** ACTIONS ****************************************** */
	gestForm: function(event, id_form) {
		 console.log('gestform');
		//Stop event
		Event.stop(event);
		//Delete errors signs ( red borders, backgrounds, .... )
		Notifier.clear(id_form); 

		//Form validation with javascript ( not ajax )
		if( this.submit_with_validation(event, id_form) ) {
			if( this._do_question(id_form) ) {
				this.submit(id_form);
			}
		}
	},

	resetForm: function(event, id_form) {
		//Stop event
		Event.stop(event);
		//Form reset
		$(id_form).reset();

		//Notifier clear
		Notifier.clear(id_form); 
		$(id_form).select('input[type="text"]').first().activate();

		return false;
	},

	submit: function(id_form) {
		//Taking of validations over the form whe submitting
		alert('to submit');
		Event.stopObserving(id_form, 'submit');
		if ($('submitter')) {
			iframe = $('submitter')
		} else {
			iframe = document.createElement('iframe');
			iframe.id = 'submitter';
			iframe.name = 'submitter';
			iframe.style = "display: none";
			iframe.src = "http://192.168.100.212/ximdex_deviation/test.php";
		}
		$(id_form).target = 'submitter';
		$(id_form).submit();
		return true;
	},

	submit_with_validation: function(event, id_form) {
		this.checked_elements = new Array();

		Notifier.startCapture();
console.log('submit_with_validation');
		if (!$(id_form)) {
			Notifier.send('Developer warning: Form could not be obtained, o it has not the attibute id', id_form, Notifier.E_SYSTEM);
		}else {
			elements_to_validate = $A($(id_form).getElementsByClassName('validable'));
	
			//Validations for each form elements(validables)
			elements_to_validate.each(function (element_to_validate) {
				this._validations(id_form, element_to_validate );
			}.bind(this)
			);
	
			if(Notifier.validateError() ) {
				Notifier.send('Se han encontrado errores al procesar el formulario', id_form, Notifier.E_CRITICAL);
				Notifier.showAll(id_form)
				Notifier.endCapture();
				return false;
			}else {
				Notifier.endCapture();
				return true;
			}
		}

	},

	/** ************************************* VALIDATIONS *************************************** */
	_validations: function (id_form, element_to_validate ) {
		class_names = element_to_validate.classNames();

		class_names.each(function(class_name) {
			var reg_exp = new RegExp("(.*)__(.*)");
			var matches = reg_exp.exec(class_name);
			if (matches !== null) {
				var class_name = matches[1];
				var param_1 = matches[2];
			}

			switch (class_name) {
				case 'not_empty': this._validation_not_empty(element_to_validate ); break;
				case 'field_equals': this._validation_field_equals(element_to_validate, param_1); break;
				case 'check_group': this._validation_check_group(element_to_validate, param_1); break;
				case 'validate_alpha': this._validation_field_validate_alpha(element_to_validate); break;
				case 'validate_alpha_num': this._validation_field_validate_alpha_num(element_to_validate); break;
				case 'validate_number': this._validation_field_validate_number(element_to_validate); break;
				case 'validate_date': this._validation_field_validate_date(element_to_validate); break;
				case 'validate_email': this._validation_field_validate_email(element_to_validate); break;
				case 'validate_url': this._validation_field_validate_url(element_to_validate); break;

			}
		}.bind(this)
		);
	},

	_validation_is_empty: function(element_to_validate) {
		var reg_exp = new RegExp("^.+$");
		match = reg_exp.exec(element_to_validate);
		if (match === null) {
			return true;
		} else {
			return false;
		}
	},
	_validation_field_validate_number: function(element_to_validate) {
		if (isNaN(element_to_validate.value)){
			var friendly_name = this._get_friendly_name(element_to_validate.id);
			Notifier.send('The field \"' + friendly_name + '\" is mandatory ', element_to_validate.id, Notifier.E_WARNING);
			return false;
		}else{
			return true;
		}
	},

	_validation_not_empty: function(element_to_validate) {
		if (this._validation_is_empty(element_to_validate.value) ) {
			var friendly_name = this._get_friendly_name(element_to_validate.id);
			Notifier.send('The field \"' + friendly_name + '\" is mandatory ', element_to_validate.id, Notifier.E_WARNING);
		}
	},

	_validation_field_equals: function(element_to_validate, param_1 ) {
		var value1 = element_to_validate.value;
		var value2 = $(param_1).value;
		if (value1 != value2) {
			var field1_friendly_name = this._get_friendly_name(element_to_validate.id);
			var field2_friendly_name = this._get_friendly_name(param_1);
			Notifier.send('The field \"' + field1_friendly_name + '\" should be equal to the field \"' + field2_friendly_name + '\"      ', element_to_validate.id, Notifier.E_WARNING);
		}
	},

	_validation_check_group: function(element_to_validate, param_1 ) {

		if (in_array(param_1, this.checked_elements)) {
			return;
		} else {
			this.checked_elements[this.checked_elements.length] = param_1;
		}

		check_elements = $$('.' + param_1);
		var checked = false;
		var elements_checked = new Array();
		var index = 0;

		check_elements.each(
			function (check_element) {
				if (check_element.checked) {
					checked = true;
				} else {
					elements_checked[index++]  = check_element.id;
				}
			}
		);

		if (checked == false) {
			Notifier.send('It should be selected at least one of the element of ' + this._get_friendly_name(param_1), element_to_validate.id, Notifier.E_WARNING);
		}
	},

	_validation_field_validate_alpha: function(element_to_validate) {
		if(!this._validation_is_empty(element_to_validate.value) ) {
			var reg_exp = /^[a-zA-Z]+$/;
			var valid_reg_exp = reg_exp.test(element_to_validate.value);
	
			if (valid_reg_exp ) {
				var friendly_name = this._get_friendly_name(element_to_validate.id);
				Notifier.send('The field \"' + friendly_name + '\" should be a word ', element_to_validate.id, Notifier.E_WARNING);
			}
		}
	},

	_validation_field_validate_string: function(element_to_validate) {
		if(!this._validation_is_empty(element_to_validate.value) ) {
			var reg_exp = /\W/;
			var valid_reg_exp = reg_exp.test(element_to_validate.value);
	
			if (valid_reg_exp  ) {
				var friendly_name = this._get_friendly_name(element_to_validate.id);
				Notifier.send('The field \"' + friendly_name + '\" should be an alphanumeric string ', element_to_validate.id, Notifier.E_WARNING);
			}
		}
	},

	_validation_field_validate_alpha_num: function(element_to_validate) {
		if(!this._validation_is_empty(element_to_validate.value) ) {
			var reg_exp = /[^\d]/;
			var valid_reg_exp = reg_exp.test(element_to_validate.value);
			var isnum = !isNaN(element_to_validate.value);
			if (   valid_reg_exp || !isnum ) {
				var friendly_name = this._get_friendly_name(element_to_validate.id);
				Notifier.send('The field \"' + friendly_name + '\" should be numeric ', element_to_validate.id, Notifier.E_WARNING);
			}	
		}
	},


	//Validate date. dd/mm/yyyy
	_validation_field_validate_date: function(element_to_validate) {
		if(!this._validation_is_empty(element_to_validate.value) ) {
			var date = element_to_validate.value.split("/");
			var obj_date =   new Date(date[2], date[1]-1, date[0]);
			if (date[0] != obj_date.getDate() || date[1] != (obj_date.getMonth()+1) || date[2] != obj_date.getFullYear() ) {
				var friendly_name = this._get_friendly_name(element_to_validate.id);
				Notifier.send('The field \"' + friendly_name + '\" should be a valid date ( dd/mm/yyyy ) ', element_to_validate.id, Notifier.E_WARNING);
			}
		}
	},

	_validation_field_validate_email: function(element_to_validate) {
		if(!this._validation_is_empty(element_to_validate.value) ) {
			var reg_exp = /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/;
			var valid_reg_exp = reg_exp.test(element_to_validate.value);
			if (   !valid_reg_exp  ) {
				var friendly_name = this._get_friendly_name(element_to_validate.id);
				Notifier.send('The field \"' + friendly_name + '\" should be an email ', element_to_validate.id, Notifier.E_WARNING);
			}	
		}
	},

	_validation_field_validate_url: function(element_to_validate) {
		if(!this._validation_is_empty(element_to_validate.value) ) {
			var reg_exp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
			var valid_reg_exp = reg_exp.test(element_to_validate.value);
			if (   !valid_reg_exp  ) {
				var friendly_name = this._get_friendly_name(element_to_validate.id);
				Notifier.send('The field \"' + friendly_name + '\" should be a web path', element_to_validate.id, Notifier.E_WARNING);
			}	
		}
	},

	/** ************************************** INITALIZES ************************************** */
	_initialize_form_validations: function() {
		//Class for validates elements
		console.log('ok');
		$$('.validate').each(function(validate_element) {
			this.element = validate_element;

			validate_element.ancestors().each( function(validate_element_ancestor) {
				if (validate_element_ancestor.tagName == 'form') {
					console.log('test')
					Element.observe(validate_element_ancestor.id, 'submit', this.gestForm.bindAsEventListener(this, validate_element_ancestor.id));
					Element.observe(validate_element, 'click', this.gestForm.bindAsEventListener(this, validate_element_ancestor.id));
				} else {
					console.log('test1');
				}
			}.bind(this)
			)
		}.bind(this)
		);
	},

	_initialize_form_resets: function() {
		//class for reset
		$$('.form_reset').each(function(validate_element) {
				this.element = validate_element;
	
				validate_element.ancestors().each( function(validate_element_ancestor) {
					if (validate_element_ancestor.tagName == 'FORM') {
						Element.observe(validate_element, 'click', this.resetForm.bindAsEventListener(this, validate_element_ancestor.id ) );
					}
				}.bind(this)
				)
			}.bind(this)
		);
	},

	_initialize_form_actives: function() {
		//class for active form ( active + forcus )
		$$('.form_active').each(function(form_active) {
			form_active.select('input[type="text"]').first().activate();
		});
	},

	/** ************************************** OTHERS ************************************** */
	_get_friendly_name: function(element_id) {
		var friendly_name = null;
		labels = $A($$('label'));
		labels.each(function(label_element){
			if (label_element.htmlFor == element_id) {
				friendly_name = label_element.innerHTML;
			}
		});
		return friendly_name;
	},

	_do_question: function(id_form) {
		question = $(id_form).select('[class="submit_message"]').first();
		if(question) {
// 			return confirm(question.value);
			View.question(question.value, this, "_do_question_ok", id_form);
			return  false;
		}else {
			return	true;
		}
	}, 

	_do_question_ok: function(event, id_form) {
			if ( this._do_request_ajax(id_form) ) {
				return	this.submit(id_form);
			}else {
				return false;
			}
	},


	//Send petition to server
	_do_request_ajax: function(id_form) {
		//If form has validate_ajax class 

		if($(id_form).hasClassName('validate_ajax') )  {

			//Create query string
			if($(id_form).action.indexOf('?') != -1  )
				var destiny =  $(id_form).action + '&ajax=json';
			else
				var destiny = $(id_form).action + '?ajax=json';

			var parameters = "";
			for( var i= 0; i< $(id_form).elements.length; i++ ) {
				if( ( $(id_form).elements[i].type != "radio"  || $(id_form).elements[i].checked ) && $(id_form).elements[i].name  ) {
					parameters += $(id_form).elements[i].name+"="+$(id_form).elements[i].value+"&";

					//we put id if element doesnt have it
					if(!$(id_form).elements[i].id) {
						$(id_form).elements[i].id = $(id_form).elements[i].name;
					}
				 }
			}
			//Send query string
			var myAjax = new Ajax.Request(
					destiny,
					{
						method: 'post',
						parameters: parameters,
						onSuccess: this.processInfo.bindAsEventListener(this, id_form)
					}
			);

			return false;
		}
		return true;
	},

	processInfo: function(response, id_form) {
//  		alert(response.responseText);
		var datos = response.responseText.evalJSON()

		if(datos[0]["type"] != "2") {
			Notifier.startCapture();

			for(var i=0; i<datos.length; i++) {
				Notifier.send(datos[i]["message"], id_form, Notifier.E_WARNING);
			}

			if(Notifier.validateError() ) {
				Notifier.send('Errors found processing the form', id_form, Notifier.E_CRITICAL);
				Notifier.showAll(id_form)
				Notifier.endCapture();
				return false;
			}else {
				Notifier.endCapture();
				return true;
			}

		}else if(datos[0]["type"] == "2") { 
			Notifier.startCapture();
			for(var i=0; i<datos.length; i++) {
				Notifier.send(datos[i]["message"], id_form, Notifier.INFORMATIVE);
			}

			if(Notifier.validateInformative() ) {
				Notifier.send('Form send successfully', id_form, Notifier.SUCCESSFULL);
				var node_id = $('nodeid') || $('id_node');

				if(node_id) {
					Notifier.showResultAction( id_form, $(node_id).value );	
				}else {
					Notifier.showResultAction( id_form );	
				}
				Notifier.endCapture();
				return false;
			}else {
				Notifier.endCapture();
				return true;
			}
		}
	}

});

// {{{ in_array
function in_array(needle, haystack, strict) {
    // Checks if a value exists in an array
    //
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_in_array/
    // +       version: 804.1015
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: true

    var found = false, key, strict = !!strict;

    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }

    return found;
}// }}}


//var obj_validator;

function initialize_body() {
	obj_validator = new validator();
	focusElement = $A($$('.focus'));
	focusElement.each (function(element) {
			element.focus();
		}	
	)
}
