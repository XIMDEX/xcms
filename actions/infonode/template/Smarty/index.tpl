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

<div class="action_header">
    <h2>{t}Information about{/t}: {$info.name}</h2>
</div>

<div class="action_content">
    <input class="idNode" type="hidden" value="{$id_node}"/>
    <input class="depUrl" type="hidden" value="{$jsonUrl}"/>

    <div class="info--node">
        <h3>{t} General info {/t}</h3>
        <div class="row">
            <div class="small-6 columns">
                <div class="row">
                    <div class="small-6 columns">
                        <div class="box-content green"><strong>{t}NodeId{/t}</strong> {$info.nodeid}</div>

                    </div>
                    <div class="small-6 columns">
                        <div class="box-content green"><strong>{t}Parent node{/t}</strong> {$info.parent}</div>

                    </div>
                </div>
                <div class="row">
                    <div class="small-12 columns">
                        <div class="box-content green"><strong>{t}NodeType{/t}</strong> {$info.typename}<span
                                    class="nodetype"> ({$info.type}) </span><span
                                    class="path">{$info.path|replace:"/Ximdex/Projects":""}</span></div>
                    </div>
                </div>

            </div>
            <div class="small-6 columns">
                <div class="row">
                    <div class="small-12 columns">
                        <div class="box-content"><strong>{t}Languages{/t}</strong>
                            {if ($languages)}
                                {section name=i loop=$languages}
                                    {$languages[i].Name} ( {$languages[i].Id} )
                                    {if (!$smarty.section.i.last)},{/if}
                                    {if (0 == $smarty.section.i.index_next%4  )}<br/>{/if}
                                {/section}

                            {else}
                                {t}Not found{/t}
                            {/if}            </div>
                    </div>
                    <div class="small-12 columns">
                        <div class="box-content"><strong>{t}Channels{/t}</strong>
                            {section name=i loop=$channels}
                                {$channels[i].Name} ({$channels[i].IdChannel})
                                {if (!$smarty.section.i.last)},{/if}
                                {if (0 == $smarty.section.i.index_next%4  )}<br/>{/if}
                            {/section}    </div>
                    </div>
                </div>
            </div>
        </div>
        {if ($info.date)}
            <h3>{t} Properties info {/t}</h3>
            <div class="row">

                <div class="small-6 columns">
                    <div class="box-content green"><strong>{t}State{/t}</strong><span
                                class="state">{if ($info.published)}{t}Published{/t}{else}{t}Not published{/t}{/if}
                            ({if isset($statusInfo)}{t}{$statusInfo}{/t}{else} Not status {/if})</span></strong>
                    </div>
                </div>

                <div class="small-6 columns">
                    <div class="box-content"><strong>{t}Last version{/t}</strong> {$info.version}.{$info.subversion}
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="small-12 columns">


                    <div class="box-content"><strong>{t}Last modified{/t}</strong>
                        <span
                                class="date">{$info.date}</span>
                        <br>
                        <span
                                class="user">{$info.lastusername} ({$info.lastuser})</span></div>
                </div>

            </div>
            <div class="row">
                <div class="small-12 columns">
                    <div class="box-content green"><strong>{t}Version manager{/t}</strong>
                        {foreach from=$valuesManageVersion.versionList key=version item=versionInfo}
                            {foreach from=$versionInfo item=subVersionList}
                                <div class="version-info version-info-node-info row-item">
			<span class="version">
				<strong>{$version}.{$subVersionList.SubVersion}</strong>
                  <strong> {if ($version == 0 && $subVersionList.SubVersion == 0)}{t}New{/t} {elseif ($subVersionList.SubVersion == 0)}{t}Published{/t}
                      {else} {t}Draft{/t} {/if}</strong>
			</span>

                                    <span class="version-name row-item-col">{$subVersionList.Name}</span>
                                    <span class="version-date row-item-col">{$subVersionList.Date}</span>
                                    <span class="version-comment row-item-col">{$subVersionList.Comment}</span>

                                </div>
                            {/foreach}
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}


        <div class="row">
            <div class="small-12 columns">
                <h3> {t}Dependencies{/t} </h3>

                <div class="graph-container">
                    <div id="graph{$id_node}" class="graph"></div>
                </div>

            </div>
        </div>

    </div>
</div>
