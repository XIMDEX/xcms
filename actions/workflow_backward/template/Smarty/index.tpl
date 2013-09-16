{**
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
 *}
<h2>{t}Previous state{/t}</h2>
<form method="post" name="workflow_backward" action="{$action_url}">
        <fieldset class="">
                <ol>   
                        <li>   
                                <p>{t}Do you want to move this file{/t} {t}from the state{/t} <strong>{$currentStateName}</strong> {t}to the state{/t} <strong>{$prevStateName}</strong>?</p>
                        </li>
                </ol>
        </fieldset>

        <fieldset class="buttons-form">
                {button class="close-button btn" label="Cancel"}
                {button class="validate accept-button btn main_action" label="Accept"}
        </fieldset>

</form>

