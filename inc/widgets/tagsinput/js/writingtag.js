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

	X.writingtag = Object.xo_create({ 

		inputNewTag: null,
		text: '',
		inputWidth: 16,
		key: 0,
		element: null,
		container: null,
		
		_init: function(options) {
		
  			 this.container = options.container;
			 this.element = $('.xim-tagsinput-newtag', this.container);
			 this.inputNewTag = $(':input', this.element);		  
  			 this.inputNewTag.focus().val("");
		    this.inputNewTag.attr("style", "width:"+this.inputWidth+"px");		 
  	
  			 this.inputNewTag.keydown(function(event) {
					//set text written
					this.setText();
					//key writting
					this.key = event.which;
					//log
					//console.info("Press key: ", this.key, this.text);
					//add char(key) in tag
					this.writing(event); 
  			 }.bind(this));
		
		    this.inputNewTag.blur(function() {
				//set text written
				this.setText();
				//send createTag
	  			this.container.tagsinput('createTag',{text: this.text, typeTag: 'generics', url: '#', description:''});
	  			this.reset();
  			 }.bind(this));
  			 
  			 this.element.focus(function() {
  			 	$(this.inputNewTag).focus();
  			 
  			 }.bind(this));
		},
	
				
		writing: function(event) {
				if ( this._keyLEFT()  )  this._moveLEFT(); 
				else if ( this._keyRIGHT() )  this._moveRIGHT();
				else if ( this._keyDEL()  )   this._moveDEL(); 
				else if ( this._keySUPR()  )   this._moveSUPR(); 
  				else if (this._keySEPARATOR() ) {
  			  			this.container.tagsinput('createTag',{text: this.text, typeTag: 'generics', url: '#', description:''});
  				    this.reset();				
			        event.preventDefault(); 
		     // user still typing a tag
  				}else if(this._keyALFHA() || this._keyNUM() || this._keySPACE() || this._keySEPARATORTYPE() ) {
				     this.inputWidth = this.inputWidth + 7;
				     this.inputNewTag.attr("style", "width:"+this.inputWidth+"px");
				     this.element.attr("style", "width:"+this.inputWidth+"px");
  				}else {
			        event.preventDefault(); 
  				}

		},
		
	
	
			
		setText: function(text) {
			if(null == text) {
			 var text = $.trim(this.inputNewTag.val());
			}
			
			this.text = $.trim(text);
		},
		
		
		reset: function() {
			this.inputWidth = 16;
		   this.inputNewTag.attr("style", "width:"+this.inputWidth+"px");
		   this.element.attr("style", "width:23px")
		   this.inputNewTag.val("");
		},

		
		/** ********************************* MOVES ******************************************** */
		_moveLEFT: function() {
			 this.element.insertBefore(this.element.prev());
			 this.inputNewTag.focus();
		},
		
		_moveRIGHT: function() {
		    this.element.insertAfter(this.element.next());
		    this.inputNewTag.focus();
		},
		
		_moveBACK: function() {
				this.inputWidth -= 7
				this.inputNewTag.attr("style", "width:"+this.inputWidth+"px");
		},
		//delete prev tag
		_moveDEL: function() {
			if( this.text == '' ) {
				//this.removeTag(this.element.prev() );
			}else {
				this._moveBACK();
			}
		},
		
		// delete next tag
		_moveSUPR: function() {
			//this.removeTag(this.element.next() );
			this.inputWidth -= 7
			this.inputNewTag.attr("style", "width:"+this.inputWidth+"px");
		},
		
	
		/** ********************************* KEYS ******************************************** */
		_keyALFHA: function() {
			return ( this.key >= 65 /*a*/ && this.key <= 90/*z*/ ) || 0 == this.key /* ñ */;
		},
		
		_keyNUM: function() {
			return this.key >= 48/*0*/ && this.key<=57 /*9*/;
		},
		
      // move left (left arrow pressed)
		_keyLEFT: function() {
			return 37 == this.key && '' == this.text;
		},
		
		// move right (right arrow pressed)
		_keyRIGHT: function() {
			return 39 == this.key && '' == this.text;
		},
		
		_keySPACE: function() {
			return 32 == this.key && '' != this.text;
		},

     // delete prev tag (backspace pressed)
		_keyBACK: function() {
			return 8 == this.key && '' == this.text;
		},
		
		_keyDEL: function() {
			return 8 == this.key;
		},
		
		_keySUPR: function() {
			return 46 == this.key && '' == this.text;
		},
		
		_keySEPARATOR: function() {
			return ( 188 == this.key /* space */ ||  13 == this.key /* enter */ ) && '' != this.text;
		},
		
		_keySEPARATORTYPE: function() {
			return ( 188 == this.key /* space */ ||  13 == this.key /* enter */ ) && '' != this.text;
		}
	
	
	});

})(X);
