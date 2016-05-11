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

<div class="welcome" ng-controller="listPostsCtrl">
    <div class="action_header">
	    <h2>{t}Welcome to Ximdex CMS{/t}, <em>{$user}</em>!</h2>
		<fieldset class="buttons-form">
			<a href="#" type="button" id="" onclick="this.blur();" class="new_post validate btn main_action ui-state-default ui-corner-all button submit-button ladda-button" data-style="slide-up" data-size="xs" tabindex="" ng-click="createnew({$documentsid})"><span class="ladda-label">{t}Add new post{/t}</span></a>
		</fieldset>
    </div>
	<div class="action_content">
				<h2>{t}Your latest posts{/t}
					<div class="pull-right noselect">
						<input class="input-sm" ng-model="textQuery" placeholder="{t}Enter a query{/t}">
						<span class="slide-element">
							<input class="showmodified input-slide" type="checkbox" checked id="modified" name="enabled" ng-model="showModified" ng-change="updateQuery()">
							<label for="modified" class="label-slide" > Ver borradores</label>
						</span>
						<span class="slide-element">
							<input class="showpublished input-slide" type="checkbox" checked id="published" name="enabled" ng-model="showPublished" ng-change="updateQuery()">
							<label for="published" class="label-slide"> Ver publicados</label>
						</span>
					</div>
				</h2>

					<ul class="media-list">
						<li ng-repeat="post in posts | filter:query track by post.IdNode" class="media" ng-class="{literal}{'not-published': post.Published == 0, 'published': post.Published == 1, 'modified': post.Published == 2, 'processing': post.Published == 3}{/literal}">
							<a class="pull-right" ng-if="post.imgpreview.length > 0">
								<img class="img-responsive img-rounded media-object" src="#/post.imgpreview/#" alt="Generic placeholder image">
							</a>
							<div class="media-body">
								<h4 class="media-heading title">#/post.title/#</h4>
								<p class="intro" ng-if="post.intro.length > 0">#/post.intro/#</p>
								<div class="pull-left">
									<small ng-if="post.Published != 1">{t}Modified{/t}: #/post.PublishDate * 1000 | date:'yyyy-MM-dd HH:mm:ss' /# (v#/post.Version/#.#/post.Subversion/#)</small>
									<br ng-if="post.Published > 1" />
									<small ng-if="post.Published != 0">{t}Published{/t}:&nbsp;&nbsp;&nbsp;#/post.ModificationDate * 1000 | date:'yyyy-MM-dd HH:mm:ss' /# (v#/post.Version/#.0)</small>
								</div>
							</div>
							<div class="media-body">
								<div class="pull-right">
									<a href="#" type="button" id="" onclick="this.blur(); " class="xmltext validate btn main_action ui-state-default ui-corner-all button submit-button ladda-button" data-style="slide-up" data-size="xs" tabindex="" ng-click="edit(post.IdNode)"><span class="ladda-label">{t}Edit{/t}</span></a>
									<a href="#" type="button" id="" onclick="this.blur(); " class="tags validate btn main_action ui-state-default ui-corner-all button submit-button ladda-button" data-style="slide-up" data-size="xs" tabindex="" ng-click="tag(post.IdNode)"><span class="ladda-label">{t}Tag it{/t}</span></a>
									<a href="#" type="button" id="" onclick="this.blur(); " class="preview validate btn main_action ui-state-default ui-corner-all button submit-button ladda-button" data-style="slide-up" data-size="xs" tabindex="" ng-click="preview(post.IdNode)"><span class="ladda-label">{t}Preview{/t}</span></a>
										<a href="#" type="button" id="" onclick="this.blur(); " class="expire validate btn main_action ui-state-default ui-corner-all button submit-button ladda-button" data-style="slide-up" data-size="xs" tabindex="" ng-if="post.Published != 0"><span class="ladda-label" ng-click="expire(post.IdNode)">{t}Expire{/t}</span></a>
									<a href="#" type="button" id="" onclick="this.blur(); " class="publish validate btn main_action ui-state-default ui-corner-all button submit-button ladda-button" data-style="slide-up" data-size="xs" tabindex=""><span class="ladda-label" ng-click="publish(post.IdNode)">{t}Publish{/t}</span></a>
								</div>
							</div>
						</li>
					</ul>
		<br/><br/>

	</div>


