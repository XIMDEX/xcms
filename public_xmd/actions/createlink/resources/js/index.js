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
	var $inputUrl = fn("input#url");
    var prefix = $inputUrl.val().split(":");

    fn('select#link_type option').each(function(){
                                        if(prefix[0]=="http" && $(this).val()=="url"){
                                            fn("label[for='url']").html("Web URL");
                                            $(this).attr("selected",true);
                                            $inputUrl.addClass("is_url");
                                            $inputUrl.removeClass("is_email");
                                        }
                                        else if (prefix[0]=="mailto" && $(this).val()=="email"){
                                            fn("label[for='url']").html("E-mail address");
                                            $(this).attr("selected",true);
                                            fn("input#url").addClass("is_email");
                                            fn("input#url").removeClass("is_url");
                                        }
    });


    fn('select#link_type').change(function() {
        var linkType= fn('#link_type option:selected').val();
		
        if(linkType=="url"){
            fn("label[for='url']").html("Web URL");
            $inputUrl.addClass("is_url");
            $inputUrl.removeClass("is_email");
			$inputUrl.val($inputUrl.val().replace(/^mailto:/,''));
            // remove email rule and add url rule to validation
            var validator = $.data($inputUrl[0].form, 'validator');
            if (validator && validator.settings) {
                $inputUrl.rules('remove', 'email');
                $inputUrl.rules('add', 'url');
            }
			if (!/^https?:\/\//.test($inputUrl.val()))
				$inputUrl.val("http://"+$inputUrl.val());
        }   
        else{
            fn("label[for='url']").html("E-mail address");
            fn("input#url").addClass("is_email");
            fn("input#url").removeClass("is_url");
            // remove url rule and add email rule to validation
            var validator = $.data($inputUrl[0].form, 'validator');
            if (validator && validator.settings) {
                $inputUrl.rules('remove', 'url');
                $inputUrl.rules('add', 'email');
            }
			$inputUrl.val($inputUrl.val().replace(/^https?:\/\//,''));
			if (!/^mailto:/.test($inputUrl.val()))
				$inputUrl.val("mailto:"+$inputUrl.val());
        }
        // trigger an keyup in inputurl field to load again validation
        $inputUrl.trigger("keyup");
    });
});

