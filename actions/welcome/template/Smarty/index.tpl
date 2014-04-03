{* *
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

<div class="welcome">
    <div class="action_header">
	    <h2>{t}Welcome to Ximdex CMS{/t}, <em>{$user}</em>!</h2>
    </div>
	<div class="action_content">
	    <div class="main_content">
		    <div class="ximdex_projects">
			    <h2>{t}Existing projects{/t}</h2>
			    {if $projects_info|@count gt 0}	
                    {foreach from=$projects_info key=index item=p }
				    <div class="project_item">
					    <img src="actions/welcome/resources/imgs/project_default.jpg" alt="" class="project_image">
					    <span class="project_name">{$p.name}</span>
				    	<div class="project_actions hidden">
						    <button class="config_button icon">{t}Details{/t}</button>
					    </div>
                        {*TODO: get more info for a project*}
                        {*<div class="details">
                            <span class="tooltip">
                            <p>Servers defined: 3</p>
                            <p>XML Documents: 6</p>
                            <p>External links: 19</p>
                            </span>
                        </div>*}
				    </div>
                    {/foreach}
                {else}
				    <div class="empty_state project_empty">
					    {t}Seems you don't have any projects yet. Let's start now!{/t}
    				</div>
                {/if}
				<button  class="new_item project_new">{t}Create a new project{/t}</button>			
			</div>
					
			<div class="ximdex_documents">
				<h2>{t}Your latest documents{/t}</h2>
			    {if $docs|@count gt 0}	
                    {foreach from=$docs key=index item=d }
				<div class="document_item">
                    <span class="icon document">{$d.name}</span>
                    <span class="document-version">({$d.Version}.{$d.Subversion})</span>
                    <span class="document-path" data-tooltip="{$d.path}">{$d.path}</span>
					<div class="document_actions">
                    {if $d.IdNodeType eq 5040}
						<button class="preview icon">{t}Preview Image{/t}</button>
                    {elseif $d.IdNodeType eq 5032}
						<button class="edit icon">{t}Edit in XML mode{/t}</button>
                    {elseif $d.IdNodeType eq 5028 || $d.IdNodeType eq 5077 || $d.IdNodeType eq 5078}
						<button class="edit icon">{t}Edit in text mode{/t}</button>
                    {/if}
					</div>
				</div>
                    {/foreach}
                {else}
				<div class="empty_state document_empty">
					<ol>
		                <li class="step_document created_project icon">{t}Create a new project from treeview or by clicking on the button above {/t}</li>
						<li class="step_document">{t}Select the '<em>documents folder</em>' in the treeview on the left panel{/t}</li>
						<li class="step_document">{t}Perform the '<em>Add new document</em>' action to create new documents{/t}</li>
					</ol>
				</div>
                {/if}
			</div>
			
		</div>
		<div class="sidebar">
			<h3>{t}Learn how to{/t}...</h3>
			<ul>
				<li>
					<a href="https://github.com/XIMDEX/ximdex/wiki/Recipes#create-a-new-project-easy" target="_blank">{t}Create a new project{/t}</a>
				</li>
				<li>
					<a href="https://github.com/XIMDEX/ximdex/wiki/Recipes#create-a-new-server-medium" target="_blank">{t}Create a new Server & publish data{/t}</a>
				</li>
				<li>					
					<a href="https://github.com/ximdex/ximdex/wiki/Ximdex-Basics" target="_blank">{t}Ximdex CMS, the basics{/t}</a>
				</li>
				<li>					
					<a href="https://github.com/ximdex/ximdex/wiki/Recipes" target="_blank">{t}RNG schemes & XSL templates{/t}</a>
				</li>
				<li>					
					<a href="https://github.com/XIMDEX/ximdex/wiki/Faqs" target="_blank">{t}FAQs{/t}</a>
				</li>
				<li>					
					<a href="mailto:help@ximdex.org">{t}Contact us{/t}</a>
				</li>
			</ul>

		
		</div>
	</div>


