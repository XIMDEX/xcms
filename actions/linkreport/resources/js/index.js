/*  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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
    var link = fn("a.js_check").click(function(){
            var url = $(this).closest(".result_info").find(".result_url").text();
            var nodeid = $(this).closest(".result_info").find(".result_url").attr("data-idnode");
            var divResult = $(this).closest(".result_info");
            checkLink(url,nodeid,divResult);
            return false;
         });

    function checkLink(linkurl,nodeid,div) {
        try {
            if (linkurl == "#") {
                return;
            }
        $.ajax({
            url: X.baseUrl+"/?action=linkreport&method=checkLink",
            type: 'POST',
            dataType: 'json',
            timeout: 25000,
            data: {linkurl:linkurl,nodeid:nodeid},
            success: function(data,status,event) {
                if(data.state=="ok"){
                    $(div).find("a.js_check").removeClass("checked_not_checked");
                    $(div).find("a.js_check").removeClass("checked_fail");
                    $(div).find("a.js_check").addClass("checked_ok");
                    $(div).find("a.js_check span").text("ok");
                }
                else{
                    $(div).find("a.js_check").removeClass("checked_not_checked");
                    $(div).find("a.js_check").removeClass("checked_ok");
                    $(div).find("a.js_check").addClass("checked_fail");
                    $(div).find("a.js_check span").text("fail");
                }
            },
            error: function(hxr,status,error) {
                alert("An unexpected error has been detected. Please, contact your admin");
            }
        });
    } catch (e) { }
}

});
