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


X.actionLoaded(function(event, fn, params)  {
        X.angularTools.initView(params.context, params.tabId);
	// var uploader = fn(".xim-uploader").uploader();
	// var btn1 = fn('.button').get(0);
	// var fm = btn1.getFormMgr();

	// fn(".extra-param").change(function(){
 //                var value = "";
 //                var name = $(this).attr("name");
 //                switch(this.tagName.toLowerCase()){

 //                        case "select":
 //                                value=[];
 //                                $("option:selected",$(this)).each(function(index,elemen){
 //                                        value.push($(this).val());
 //                                })
 //                                break;
 //                        case "input":
 //                                var type = $(this).attr("type");
 //                                switch(type){

 //                                        case "text":
 //                                        case "hidden":
 //                                                value = $(this).val();
 //                                                break;
 //                                        case "checkbox":
 //                                        case "radio":
 //                                                value=[];
 //                                                $("option:selected",$(this)).each(function(index,elemen){
 //                                                        value.push($(this).val());
 //                                                });
 //                                                break;

 //                                }
 //                                break;
 //                        default:

 //                }
 //                uploader.uploader("setExtraParam",name,value);
 //        });

	// btn1.beforeSubmit.add(function(event, button) {
	// 	var form = form = button.getForm();
	// 	var url = form.action;
	// 	var result = uploader.uploader("upload", url.replace("showUploadResult", "uploadFile") );

	// 	uploader.bind("filesUploaded", function() {
	// 		fm.sendForm();
	// 	});

	// 	event.preventDefault();
	// 	event.stopPropagation();
	// 	return true;
	// });
}); 
