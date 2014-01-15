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

    var submit = fn('.validate').get(0);

    fn('select#link_type').change(function() {
        var linkType= fn('#link_type option:selected').val();
        if(linkType=="url"){
            fn("label[for='url']").html("Web URL");
            fn("input#url").addClass("is_url");
            fn("input#url").removeClass("is_email");
        }   
        else{
            fn("label[for='url']").html("E-mail address");
            fn("input#url").addClass("is_email");
            fn("input#url").removeClass("is_url");
        }
    });

    submit.beforeSubmit.add(function(event, button) {
        if(fn("form#cln_form #link_type").val()==""){
            alert(_("Please, select one type of link"));
            event.preventDefault();
            event.stopPropagation();
            return true;
        }
    });
});

