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
        $(this).addClass("checking_status");
        checkLink(url,nodeid,divResult);
        return false;
    });
    
    
    //Check all button
    fn("button.js_check_all").on("click", function(){
        /*Using interval to test the untested links.
         * Only will be tested chunk links at time
         * When a link will be tested, we will check the next one.
         */
       var linksToCheck = fn("a.js_check");
       var chunk = 5;
       var nextLinksToCheck = [];
       var cont = 0;
       while (nextLinksToCheck.length<chunk && cont<linksToCheck.length){
               element = linksToCheck[cont];
               cont++;
               $(element).click();
               nextLinksToCheck.push(element);               
       }
       var checkAllInterval = setInterval(function(){
           if (linksToCheck.length === cont)
                clearInterval(checkAllInterval);
           for (var i = 0; i < nextLinksToCheck.length; i++){
               var element = nextLinksToCheck[i];
               if (!$(element).hasClass("checking_status")&& cont<linksToCheck.length){
                    nextLinksToCheck[i] = null;
                    element = linksToCheck[cont];
                    cont++;
                    $(element).click();
                    nextLinksToCheck[i] = element;
                    i--;
               }
           }
       },300);
       
    });

    //Get the state for a link
    function readLinkState(nodeid, div){
        
        var interval = setInterval(function(){            
            $.ajax({
            url: X.baseUrl+"/?action=linkreport&method=readLinkState",
            type: 'POST',
            dataType: 'json',
            data:{nodeid:nodeid},
            success: function(data,status,event) {                
                switch (data.state){
                case "ok":
                    $(div).find("a.js_check").removeClass("checked_not_checked");
                    $(div).find("a.js_check").removeClass("checked_fail");
                    $(div).find("a.js_check").removeClass("checking_status");
                    $(div).find("a.js_check").addClass("checked_ok");
                    clearInterval(interval);
                    
                    break;
                case "waiting":                    
                    break;
                case "fail":
                default: 
                    $(div).find("a.js_check").removeClass("checked_not_checked");
                    $(div).find("a.js_check").removeClass("checked_ok");
                    $(div).find("a.js_check").removeClass("checking_status");
                    $(div).find("a.js_check").addClass("checked_fail");
                    clearInterval(interval);
            }                
            $(div).find("a.js_check span").html("<p class='status'>"+data.state+"</p><p class='date_check'>"+_("Last check")+" " +data.date+"</p>");
            }
        })
        }, 2000);
        
    }

    //Renew the state for the link
    function checkLink(linkurl,nodeid,div) {
        try {
            if (linkurl == "#") {
                return;
            }
        var that = this;
        $.ajax({
            url: X.baseUrl+"/?action=linkreport&method=checkLink",
            type: 'POST',
            dataType: 'json',
            data: {linkurl:linkurl,nodeid:nodeid},
            success: function(data,status,event) {
                console.log(that, this);
                readLinkState(nodeid, div);                
            },
            error: function(hxr,status,error) {
                alert("An unexpected error has been detected. Please, contact your admin");
            }
        });
    } catch (e) { }
}

});
