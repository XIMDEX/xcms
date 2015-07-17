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

<div class="kupu-toolboxes" id="kupu-toolboxes" style="display: none;">

	<div class="kupu-toolbox" id="kupu-toolbox-info">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}Information{/t}
		</h1>
		<div>
		</div>
	</div>

	<div class="kupu-toolbox" id="kupu-toolbox-annotationbox">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}Annotation{/t}
		</h1>
		<div id="kupu-toolbox-annotation" class="kupu-toolbox-label">
		<div class="load-annotation"><button type="button" class="kupu-annotation"
				id="kupu-annotation-button" xim:title="{t}Annotations{/t}"
				i18n:attributes="title">
				{t}Load{/t}
			</button></div>

			<h3 id="anottationtoolbox-section-header-link"
				class="anottationtoolbox-section-header">
				<span>{t}Links{/t}</span>
			</h3>
			<div id="anottationtoolbox-section-link" class="anottationtoolbox-section">
				<div id="anottationtoolbox-link-template">
					<div class="anottationtoolbox-linkheader"
						id="anottationtoolbox-linkheader-template">
						{t}Link header{/t}
					</div>
					<div id="anottationtoolbox-linkitem-template"
						class="anottationtoolbox-linkitem">
						{t}Link Source{/t}
						<a id="anottationtoolbox-linkitem-template_template_visit"
						title="{t}Visitar{/t}" target="_blank"
						href="#" class="anottationtoolbox-linkgo">
							<span>{t}Visit{/t}</span>
						</a>
						<a title="{t}Add{/t}" anchorname="Anchor Name (like Link Header)"
						id="anottationtoolbox-linkitem-template_template" class="anottationtoolbox-linkadd">
							<span>{t}Add{/t}</span>
						</a>
						<br>
					</div>
				</div>
			</div>
			<h3 id="anottationtoolbox-section-header-image" class="anottationtoolbox-section-header">
				<span>{t}Images{/t}</span>
			</h3>
			<div id="anottationtoolbox-section-image" class="anottationtoolbox-section">
				<div id="sliderContainer-image" class="slideImages">
					<img class="anottationtoolbox-imageitem-image" id="anottationtoolbox-imageitem-template" height="150">
				</div>
				<div id="navContainer-image">
					<div id="prevButton-image" class="anottationtoolbox-imageitem-prevbutton" title="{t}Previous{/t}"></div>
					<div id="nextButton-image" class="anottationtoolbox-imageitem-nextbutton" title="{t}Next{/t}"></div>
				</div>
				<div id="infoContainer-image">
					<div id="infoImage-template" class="anottationtoolbox-imageitem">
						<ul>
							<li id="description-image">{t}Description{/t}</li>
							<li id="license-image">{t}License{/t}</li>

						</ul>
					</div>
				</div>
			</div>
			<h3 class="anottationtoolbox-section-header" id="anottationtoolbox-section-header-article">
				<span>{t}Articles{/t}</span>
			</h3>
			<div id="anottationtoolbox-section-article" class="anottationtoolbox-section">
				<div id="articleContainer-article">
					<div id="anottationtoolbox-articleitem-template" class="anottationtoolbox-articleitem">
						{t}Description{/t}
						<div class="anottationtoolbox-actions">
							<a id="anottationtoolbox-articleitem-template_template_visit"
							title="{t}Visit{/t}" target="_blank"
							href="#" class="anottationtoolbox-linkgo">
								<span>{t}Visit{/t}</span>
							</a>
							<a title="{t}Add{/t}" anchorname="Article Name"
							id="anottationtoolbox-articleitem-template_template" class="anottationtoolbox-linkadd">
								<span>{t}Add{/t}</span>
							</a>
						</div>
					</div>
				</div>
			</div>

			<h3 class="anottationtoolbox-section-header" id="anottationtoolbox-section-header-people">
				<span>{t}People{/t}</span>
			</h3>
			<div id="anottationtoolbox-section-people" class="anottationtoolbox-section">
				<div id="peopleContainer">
					<div id="anottationtoolbox-personitem-template" class="anottationtoolbox-articleitem">
						{t}Person{/t}
						<div class="anottationtoolbox-actions">
							<a id="anottationtoolbox-personitem-template_template_visit"
							title="{t}Visit{/t}" target="_blank"
							href="#" class="anottationtoolbox-linkgo">
								<span>{t}Visit{/t}</span>
							</a>
							<a title="{t}Add{/t}" anchorname="Entity Name"
							id="anottationtoolbox-personitem-template_template" class="anottationtoolbox-linkadd">
								<span>{t}Add{/t}</span>
							</a>
						</div>
					</div>
				</div>
			</div>

			<h3 class="anottationtoolbox-section-header" id="anottationtoolbox-section-header-organisations">
				<span>{t}Organizations{/t}</span>
			</h3>
			<div id="anottationtoolbox-section-organisations" class="anottationtoolbox-section">
				<div id="organisationsContainer">
					<div id="anottationtoolbox-organisationitem-template" class="anottationtoolbox-articleitem">
						{t}Organization{/t}
						<div class="anottationtoolbox-actions">
							<a id="anottationtoolbox-organisationitem-template_template_visit"
							title="{t}Visit{/t}" target="_blank"
							href="#" class="anottationtoolbox-linkgo">
								<span>{t}Visit{/t}</span>
							</a>
							<a title="{t}Add{/t}" anchorname="Entity Name"
							id="anottationtoolbox-organisationitem-template_template" class="anottationtoolbox-linkadd">
								<span>{t}Add{/t}</span>
							</a>
						</div>
					</div>
				</div>
			</div>

			<h3 class="anottationtoolbox-section-header" id="anottationtoolbox-section-header-places">
				<span>{t}Places{/t}</span>
			</h3>
			<div id="anottationtoolbox-section-places" class="anottationtoolbox-section">
				<div id="placesContainer">
					<div id="anottationtoolbox-placeitem-template" class="anottationtoolbox-articleitem">
						{t}Place{/t}
						<div class="anottationtoolbox-actions">
							<a id="anottationtoolbox-placeitem-template_template_visit"
							title="{t}Visit{/t}" target="_blank"
							href="#" class="anottationtoolbox-linkgo">
								<span>{t}Visit{/t}</span>
							</a>
							<a title="{t}Add{/t}" anchorname="Entity Name"
							id="anottationtoolbox-placeitem-template_template" class="anottationtoolbox-linkadd">
								<span>{t}Add{/t}</span>
							</a>
						</div>
					</div>
				</div>
			</div>

            <h3 class="anottationtoolbox-section-header" id="anottationtoolbox-section-header-creativework">
                <span>{t}Creative Work{/t}</span>
            </h3>
            <div id="anottationtoolbox-section-creativework" class="anottationtoolbox-section">
                <div id="creativeworkContainer">
                    <div id="anottationtoolbox-creativeworkitem-template" class="anottationtoolbox-articleitem">
                        {t}Creative Work{/t}
                        <div class="anottationtoolbox-actions">
                            <a id="anottationtoolbox-creativeworkitem-template_template_visit"
                               title="{t}Visit{/t}" target="_blank"
                               href="#" class="anottationtoolbox-linkgo">
                                <span>{t}Visit{/t}</span>
                            </a>
                            <a title="{t}Add{/t}" anchorname="Entity Name"
                               id="anottationtoolbox-creativeworkitem-template_template" class="anottationtoolbox-linkadd">
                                <span>{t}Add{/t}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="anottationtoolbox-section-header" id="anottationtoolbox-section-header-others">
                <span>{t}Others{/t}</span>
            </h3>
            <div id="anottationtoolbox-section-others" class="anottationtoolbox-section">
                <div id="othersContainer">
                    <div id="anottationtoolbox-othersitem-template" class="anottationtoolbox-articleitem">
                        {t}Others{/t}
                        <div class="anottationtoolbox-actions">
                            <a id="anottationtoolbox-othersitem-template_template_visit"
                               title="{t}Visit{/t}" target="_blank"
                               href="#" class="anottationtoolbox-linkgo">
                                <span>{t}Visit{/t}</span>
                            </a>
                            <a title="{t}Add{/t}" anchorname="Entity Name"
                               id="anottationtoolbox-othersitem-template_template" class="anottationtoolbox-linkadd">
                                <span>{t}Add{/t}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
{* rdfa annotations *}
	<div class="kupu-toolbox" id="kupu-toolbox-annotationrdfabox">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}Rdfa annotations{/t}
		</h1>
		<div id="kupu-toolbox-annotationrdfa" class="kupu-toolbox-label">
			<h3 id="anottationrdfatoolbox-section-header-link"
				class="anottationrdfatoolbox-section-header">
				{t}Friend of a friend{/t}
			</h3>
			<div id="anottationrdfatoolbox-section-link" class="anottationrdfatoolbox-section-foaf">
			</div>
			<h3 id="anottationrdfatoolbox-section-header-image" class="anottationrdfatoolbox-section-header">
				{t}Geospatial{/t}
			</h3>
			<div id="anottationrdfatoolbox-section-image" class="anottationrdfatoolbox-section-geo">
			</div>
		</div>
	</div>
{* end of rdfa annotations *}

{* rdfa annotations *}
	<div class="kupu-toolbox" id="kupu-toolbox-annotationrdfabox">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}Rdfa annotations{/t}
		</h1>
		<div id="kupu-toolbox-annotationrdfa" class="kupu-toolbox-label">
			<h3 id="anottationrdfatoolbox-section-header-link"
				class="anottationrdfatoolbox-section-header">
				{t}Friend of a friend{/t}
			</h3>
			<div id="anottationrdfatoolbox-section-link" class="anottationrdfatoolbox-section-foaf">
			</div>
			<h3 id="anottationrdfatoolbox-section-header-image" class="anottationrdfatoolbox-section-header">
				{t}Geospatial{/t}
			</h3>
			<div id="anottationrdfatoolbox-section-image" class="anottationrdfatoolbox-section-geo">
			</div>
		</div>
	</div>
{* end of rdfa annotations *}

	<div class="kupu-toolbox" id="kupu-toolbox-attributes">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}Attributes{/t}
		</h1>
		<div>
			<div id="kupu-toolbox-attributes-elements">
				{*Control dinamic generation*}
			</div>
			<div class="kupu-toolbox-buttons">
				<button type="button" id="kupu-attribute-button"
					class="kupu-toolbox-action" i18n:translate="">
					{t}Update{/t}
				</button>
			</div>
		</div>
	</div>

	<div class="kupu-toolbox" id="kupu-toolbox-properties">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}Document properties{/t}
		</h1>
		<div>
			<div class="kupu-toolbox-label" i18n:translate="">
				Tag
			</div>
			<input class="wide" id="kupu-properties-title" />
			<div class="kupu-toolbox-label" i18n:translate="">
				Url
			</div>
			<textarea class="wide"
				id="kupu-properties-description">
			</textarea>
		</div>
	</div>

	<div class="kupu-toolbox" id="kupu-toolbox-undo">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}History{/t}
		</h1>
		<div id="kupu-toolbox-undolog"
			class="kupu-toolbox-label">
		</div>
	</div>

	<div class="kupu-toolbox" id="kupu-toolbox-debug">
		<h1 class="kupu-toolbox-heading" i18n:translate="">
			{t}Debug log{/t}
			<input type="button" value="Clean" onclick="javascript: getFromSelector('kupu-toolbox-debuglog').innerHTML = '';" />
		</h1>
		<div id="kupu-toolbox-debuglog"
			class="kupu-toolbox-label">
		</div>
	</div>
</div>
