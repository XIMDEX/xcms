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


<div ng-controller="ximPUBLISHtools">
    <fieldset>
        <legend><span>{t}Informe de publicación{/t}</span></legend>
        <ol><li>{t}Progreso de publicación de documentos de Ximdex{/t}</li></ol>
    </fieldset>

    <fieldset>
        <legend><span>{t}Listado de documentos{/t}</span></legend>	
        {if $frames}
            <div id="frame_list">
            </div>
        {else}
            <div id="frame_list">{t}Aún no se ha enviado a publicar ningún documento{/t}</div>
        {/if}
    </fieldset>



    <h2>Angular</h2>
    <ul>
        <div class="batch_container" ng-repeat="publicationList in json">
            <span class="ui-icon ui-icon-triangle-1-e"></span>
            <div class="progressbar ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
                <div class="ui-progressbar-value ui-widget-header ui-corner-left ui-corner-right"></div>
            </div>

            <div class="frame_filename">
                <strong>
                    <em>ParentFrame!!</em>
                </strong>
                <em>Esta publicación está <strong>isActiveBatch!!</strong></em>
                <a class="batch_toggle">[Detener esta publicación]</a>
            </div>

            <div class="frame_default"></div>

            <div ng-repeat="frame in publicationList" style="display: block;">
                <span class="frame_indent"></span>
                <div class="progressbar ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
                    <div class="ui-progressbar-value ui-widget-header ui-corner-left ui-corner-right" style="width: 102%;"></div>
                </div>
                <div class="frame_filename">#/frame.PubTime/#
                    <strong>
                        <em>#/frame.FilePath + '/' + frame.FileName /#</em>
                    </strong>
                </div>
                <div class="frame_default"></div>
            </div>
        </div>
    </ul>
</div>
