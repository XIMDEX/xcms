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


(function(X) {

	X.session = {
		
		options: {
			semicolon: '%3B'
		},
		
		get: function(name) {
			var value = null;
			var nameEQ = name + '=';
			var ca = document.cookie.split(';');
			for (var i=0, l=ca.length; i<l; i++) {
				var c = ca[i].trim();
				if (c.indexOf(nameEQ) == 0) {
					value = c.substring(nameEQ.length, c.length);
					break;
				}
			}
			var decoded = X.session.decodeValue(value);
			return decoded;
		},
		
		timeToMs: function(time) {
		
			// time can be expressed in milliseconds, hours (h),
			// days (d), months (m) and years (y).
			
			var reg = /^(\d+)([hdmy])?$/;
			if (!reg.test(time)) return null;
			
			var r = reg.exec(time);
			var value = r[1];
			var unit = r[2] === undefined ? null : r[2].toUpperCase();
			
			switch (unit) {
				case 'H':
					// hours
					value = value * 60 * 60;
					break;
				case 'D':
					value = value * 24 * 60 * 60;
					// days
					break;
				case 'M':
					value = value * 30 * 24 * 60 * 60;
					// months
					break;
				case 'Y':
					// years
					value = value * 365 * 24 * 60 * 60;
					break;
			}
			
			// to milliseconds
			return value * 1000;
		},
		
		set: function(name, value, expires) {
		
			expires = X.session.timeToMs(expires);
			expires = isNaN(expires) || Object.isEmpty(expires)
				? 31536000000	// one year
				: expires;
			
			var date = new Date();
			date.setTime(date.getTime() + expires);
			expires = date.toGMTString();
			
			var encoded = X.session.encodeValue(value);
			document.cookie = '%s=%s; expires=%s; path=/'.printf(name, encoded, expires);
		},
		
		encodeValue: function(value) {
			var encoded = $.toJSON(value);
			encoded = encoded.replace(/;/g, X.session.options.semicolon);
			return encoded;
		},
		
		decodeValue: function(value) {
			var re = new RegExp(X.session.options.semicolon, 'g');
			// NOTE: Lazy function definition
			X.session.decodeValue = function(value) {
				if (!Object.isString(value)) return value;
				var decoded = $.secureEvalJSON(value.replace(re, ';'));
				return decoded;
			}
			return X.session.decodeValue(value);
		}
	};

	/**
	 * <p>SessionTimer class</p>
	 * <p>Utility class to obtain minutes or miliseconds
	 *    until a given timestamp from the timestamp set 
	 * 	  in the object creation
	 * </p>
	 */
	X.SessionTimer = Object.xo_create({

	 _init: function(options) {
	
		this.inactivityLength = options.inactivityLength || 15;
		this.sessionLength = options.sessionLength || 10;
		this.gapToRefresh = options.gapToRefresh || 5;
		
		/* Inactivity dialog to show when inactivity limit is reached */
		this.inactivityDialog = new X.dialogs.MessageDialog({
								id: 'messageDialog',
								title: _('Inactivity period reached'),
								onclose: function(event,params) {
									window.location.href='##BASE_URL##/xmd/loadaction.php?action=logout';
								}.bind(this),
								message: [{type: 'STRING', message: _('You have reached the limit of inactivity period. Please, re-authenticate again in order to continue working. ')}],
								buttons: [{text: 'Login', value: 6, onPress: function() {window.location.href='##BASE_URL##/xmd/loadaction.php?action=logout'; this.close();} }]
							});
							
		/* Function to execute in the timeout timer */	
		var timeoutFn = function() {this.inactivityDialog.open();}.bind(this);
		
		/* Timer to show the inactivity dialog */
		this.inactivityTimer = setTimeout(function(){timeoutFn();}, this.inactivityLength*1000);
		
		
		// Refreshing the session 2 seconds before the session expires
		setInterval(function() {
					console.log("Refreshing session");
					jQuery.ajax({ url: '##BASE_URL##/xmd/loadaction.php?action=browser3&method=refreshSession',
  								  success: function(data) {
    							  	console.log("Session refreshed");
      							  },
      							  error: function(jqXhr, textStatus, error) {
      							  	console.log("Error refreshing session. Show dialog to re-authenticate?");
      							  }
      				});
      							},this.sessionLength*1000-this.gapToRefresh*1000); 
	
		// Clear and set again the inactivity timer when any event occurs in the document
		var registeredEvents = jQuery.event.global;
		for(event in registeredEvents) {
			jQuery(document).bind(event+"", function() { 
												clearTimeout(this.inactivityTimer);
												this.inactivityTimer = setTimeout(function(){timeoutFn();}, this.inactivityLength*1000);
														}.bind(this));
		}
		
	
					
	},
	
	getSessionDuration: function() {
		return this.sessionDuration;
	}
	
	});
})(com.ximdex);