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

X.actionLoaded(function(event, fn, params) {
	var form = params.actionView.getForm('as_form');
	var fm = form.getFormMgr();
	var submit = fn('.validate').get(0);
	
	fn('select#type_sec').change(function() {
                var urler = fn('#nodeURL').val() + '&type_sec=' + fn('#type_sec option:selected').val();
                fn('#as_form').attr('action', urler);
                fm.sendForm();
        });

	if(fn('#type_sec option:selected').text()=="ximNEWS"){
		$("div.folder-name").removeClass("folder-normal").addClass("folder-news"); 
	}
	else{
		$("div.folder-name").removeClass("folder-news").addClass("folder-normal");
	}

	fn(".subfolder > label.icon").click(function(){ 
                        var readonly = $(this).prev().attr("readonly"); 
                        if(readonly && readonly.toLowerCase()!=='false') { 
                                return false; 
                        } 
   	}); 

	submit.beforeSubmit.add(function(event, button) {
		if(!fn("form#as_form input[name='folderlst[]']").is(":checked")){
			alert("You cannot create an empty section. Please, click at least one of the allowed subfolders.");
                  	event.preventDefault();
                 	event.stopPropagation();
                 	return true;
		}
      	});
});
