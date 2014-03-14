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
	<h2>{t}Status Report{/t}</h2>
</div>
	<div class="action_content versions" ng-controller="XPublishStatus" ng-cloak xim-nodeid={$id_node}>
	<p><strong>{t}Modified documents{/t}:</strong></p>
{if {count($files) eq 0}}
            <span>{t}There aren't any edited files yet{/t}.</span>
<!--    <div class="action_content"></div>-->
{else}
		<span>{t}Below are listed all the relevant documents in your system, grouped by state. Only are shown the files that are modified in comparation with its last published version.{/t}</span>
			
		{foreach from=$files key=state item=statenode}
			<div class="state-info row-item_selectable">
				<span class="state">{t}Documents in{/t} {$statesFull[{$state}].stateName}</span>
	            <div class="docs-amount right">{$statesFull[{$state}].count}</div>
			
			    <div class="documents-info">
				{foreach from=$statenode item=file}		
					<div class="version-info">
						<span class="file-path">{$file.Path}/<strong>{$file.Name}</strong></span>
						<span class="file-date">{$file.Date} hrs.</span>
						<span class="file-version">{$file.Version}.{$file.SubVersion}</span>
					</div>
				{/foreach}

			    </div>
	        </div>
	    {/foreach}
{/if}
	<br/>
	<p><strong>{t}Published documents{/t}:</strong></p>
		<span>{t}Below are listed all the documents that have been sent to publish{/t}.</span>
	    <div class="state-info row-item_selectable" 
	    	ng-repeat="(key, pubSet) in publications" 
	    	ng-show="pubSet.length" 
	    	ng-class="{literal}{opened: opened}{/literal}"
	    	ng-click="opened = !opened">
			<span class="state">#/'actions.checkstatus.publications.'+key+'.title' | xI18n/#</span>
            <div class="docs-amount right">#/pubSet.length/#</div>
		
		    <div class="documents-info" 
		    	ng-class="{literal}{'hide-toggle': !opened}{/literal}"
		    	ng-click="event.stopPropagation();">	
				<div class="version-info" ng-repeat="pub in pubSet">
					<span class="file-path">#/pub.path/#/#/pub.name/#</span><span class="file-size">#/pub.filesize | xBytes/#</span>
					<span class="file-date">#/pub.date+'000' | date:'dd/MM/yyyy HH:MM'/# hrs.</span>
					<span class="file-version">#/pub.version/#</span>
				</div>
		    </div>
        </div>
	    <!-- <div class="state-info row-item_selectable" ng-show="publications.published.length">
			<span class="state">{t}Published documents{/t}</span>
            <div class="docs-amount right">#/publications.published.length/#</div>
		
		    <div class="documents-info">	
				<div class="version-info" ng-repeat="pub in publications.published">
					<span class="file-path">#/pub.path/#/#/pub.name/#</span><span class="file-size">#/pub.filesize | xBytes/#</span>
					<span class="file-date">#/pub.date+'000' | date:'dd/MM/yyyy HH:MM'/# hrs.</span>
					<span class="file-version">#/pub.version/#</span>
				</div>
		    </div>
        </div>
        <div class="state-info row-item_selectable" ng-show="publications.unpublished.length">
			<span class="state">{t}Documents in publication queue{/t}</span>
            <div class="docs-amount right">#/publications.unpublished.length/#</div>
		
		    <div class="documents-info">	
				<div class="version-info" ng-repeat="pub in publications.unpublished">
					<span class="file-path">#/pub.path/#/#/pub.name/#</span><span class="file-size">#/pub.filesize | xBytes/#</sp
					<span class="file-date">#/pub.date+'000' | date:'dd/MM/yyyy HH:MM'/# hrs.</span>
					<span class="file-version">#/pub.version/#</span>
				</div>
		    </div>
        </div> -->
      <p>{t}If you want to have a deeper look into the Ximdex CMS publication process, upgrade this status report with <strong>XPublishTools</strong> module{/t}.</p> 
	</div>
	
