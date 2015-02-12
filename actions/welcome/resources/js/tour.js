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

window.X.actionLoaded(function (event, fn, params) {
    $('h3.action_tour', params.context).click(function () {
        start_tour(params.action.command);
    }.bind(this));
});

function start_tour(command) {

    var ximTourDefaultNode = "";
    $.getJSON(
        X.restUrl + '?method=getDefaultNode&ajax=json',
        function (data) {
            var node_list = data["nodes"];
            ximTourDefaultNode = node_list[0]["IdNode"];
        }.bind(this)
    );

    var config_action = [
        {
            "name": "#angular-content",
            "position": "R",
            "timeToWait": 400,
            "beforeTooltip": function () {
                var treeScope = $("#angular-tree").scope();
                var tabsScope = $("#angular-content").scope();
                treeScope.showTree();
                if (!treeScope.expanded) {
                    treeScope.toggleTree();
                }
                tabsScope.closeTabById(ximTourDefaultNode + "_xmleditor2");
                tabsScope.closeTabById(ximTourDefaultNode + "_setmetadata");
            }
        }, //Explaining the help guide (1/1)
        {
            "name": "div.hbox-panel-container-0",
            "position": "L",
            "beforeTooltip": function () {
                var tabsScope = $("#angular-content").scope();
                tabsScope.closeTabById(ximTourDefaultNode + "_xmleditor2");
            },
            "callback": function () {
                var treeScope = $("#angular-tree").scope();
                var closeIco = $("div.advanced-search").parent();
                $("a.ui-dialog-titlebar-close", closeIco).click();
                treeScope.navigateToNodeId(ximTourDefaultNode);
            }

        },//Left Panel
        //Left Panel
        {
            "name": "div.hbox-panel-container-1",
            "position": "R",
            "callback": function () {
                var treeScope = $("#angular-tree").scope();
                var tabsScope = $("#angular-content").scope();
                if (!treeScope.expanded) {
                    treeScope.showTree();
                    treeScope.toggleTree();
                }
                var tabsScope = $("#angular-content").scope();
                activeTab = tabsScope.getActiveTab();
                if (!(activeTab && activeTab.id == ximTourDefaultNode + "_xmleditor2")) {
                    var labelValue = _("Edit XML document")
                    tabsScope.openAction({
                        label: labelValue,
                        name: labelValue,
                        command: 'xmleditor2',
                        params: '',
                        bulk: '0'
                    }, [ximTourDefaultNode]);
                }
            }
        },//Editor
        {
            "name": "#angular-tree-toggle",
            "position": "L",
            "timeToWait": 400,
            "beforeTooltip": function () {
                var treeScope = $("#angular-tree").scope();
                treeScope.showTree();
            },
            "callback": function () {
                var treeScope = $("#angular-tree").scope();
                if (!treeScope.expanded) {
                    treeScope.toggleTree();
                }
            }
        },//10
        {
            "name": "div.kupu-editorframe",
            "position": "B",
            "scope": "editor",
            "beforeTooltip": function () {
                var treeScope = $("#angular-tree").scope();
                if (treeScope.expanded) {
                    treeScope.toggleTree();
                }
                var frameCount = window.frames.length;
                $("button.kupu-designview", window.frames[frameCount - 1].document).click();
            }
        },//Editor
        {
            "name": "button.kupu-treeview",
            "position": "TL",
            "scope": "editor",
            "leftOffset": -21,
            "callback": function () {
                var frameCount = window.frames.length;
                $("button.kupu-treeview", window.frames[frameCount - 1].document).click();
            }
        },//3
        {
            "name": "button.kupu-designview, button.kupu-designview-pressed",
            "position": "T",
            "scope": "editor",
            "callback": function () {
                var frameCount = window.frames.length;
                $("button.kupu-designview", window.frames[frameCount - 1].document).click();
            }
        },//4
        {
            "name": "div.kupu-preview",
            "position": "TL",
            "scope": "editor",
            "beforeTooltip": function(){
                var frameCount = window.frames.length;
                $("body div.ui-dialog div.ui-dialog-buttonpane button", window.frames[frameCount - 1].document).click();
            }
        },//5

        {
            "name": "div#xedit-annotations-toolbox button.kupu-annotation",
            "position": "R",
            "scope": "editor",
            "timeToWait": 400,
            "beforeTooltip": function () {
                var tabsScope = $("#angular-content").scope();
                activeTab = tabsScope.getActiveTab();
                if (activeTab.id != ximTourDefaultNode + "_xmleditor2") {
                    tabsScope.setActiveTabById(ximTourDefaultNode + "_xmleditor2");
                    var treeScope = $("#angular-tree").scope();
                    treeScope.hideTree();
                }
                tabsScope.closeTabById(ximTourDefaultNode + "_setmetadata");
                var frameCount = window.frames.length;
                var display = $("div.kupu-toolbox-container div#xedit-annotations-toolbox", window.frames[frameCount - 1].document).css("display");
                if (display == "none")
                    $("button.xedit-annotations-toolbox-button:parent", window.frames[frameCount - 1].document).click();
            },
            "callback": function () {
                var frameCount = window.frames.length;
                $("div#xedit-annotations-toolbox button.kupu-annotation", window.frames[frameCount - 1].document).click();

            }

        },//8
    ];

    var arrayText = new Array;
    var arrayTitle = new Array;

    var textDescription = "";
    var titleDescription = "";
    textDescription = _('Welcome to Ximdex!<br/>In a few steps you will discover the basic features of Ximdex CMS.');
    titleDescription = _("Ximdex Demo Tour.");
    arrayText.push(textDescription);
    arrayTitle.push(titleDescription);

    textDescription = _("In the left panel you will find the tree with all your documents, images, links and other files.");
    arrayText.push(textDescription);

    textDescription = _("Use Xedit, the XML editor,<br/> to edit your document in a WYSIWYG way.");
    arrayText.push(textDescription);
    textDescription = _('Click here to hide the left panel.');
    arrayText.push(textDescription);
    textDescription = _("This is the document area.<br/>It looks like a HTML document. Actually it's a XML file with a semantic layer.");
    arrayText.push(textDescription);
    textDescription = _('Press the Tree View button to change to XML view for your structured document.');
    arrayText.push(textDescription);
    textDescription = _('Press the Design View button in order to return to WYSIWYG view.');
    arrayText.push(textDescription);
    textDescription = _("We've been editing a XML file. <br/><br/>The preview button allows you to see it in the final format for your web portal (HTML5, php, jsp, RoR, ...) or for other platform (digital TV, smartphone apps, etc.)<br/><br/>It's really easy to extend it with templates for oncoming technologies.");
    arrayText.push(textDescription);
    textDescription = _('With Xowl module you will get automagically links (to images, maps, videos, references, ...) and semantic tags from sources like DBpedia.');
    arrayText.push(textDescription);


    textDescription = _("You can easily add your own custom tags or use an ontology to select semantic tags...");
    arrayText.push(textDescription);
    config_action.push({
        "name": "#angular-content",
        "position": "R",
        "timeToWait": 400,
        "beforeTooltip": function () {
            var treeScope = $("#angular-tree").scope();
            if (!treeScope.expanded) {
                treeScope.showTree();
            }
        },
        "callback": function () {
            var tabsScope = $("#angular-content").scope();
            activeTab = tabsScope.getActiveTab();
            if (activeTab.id != ximTourDefaultNode + "_setmetadata") {
                var labelValue = _("Edit metadata");
                var tabsScope = $("#angular-content").scope();
                tabsScope.openAction({
                    label: labelValue,
                    name: labelValue,
                    module: "ximTAGS",
                    command: 'setmetadata',
                    params: '',
                    bulk: '0'
                }, [ximTourDefaultNode]);
            }
        }

    });


    textDescription = _("Ximdex CMS can automatically suggest tags based on your written text.<br/>Let's select a couple of tags from these suggestions.");
    arrayText.push(textDescription);
    config_action.push({
        "name": "div.xim-tagsinput-container-related",
        "position": "B",
        "callback": function () {
            var l = $("ul.xim-tagsinput-list-related li").length;
            setTimeout(function () {
                if (l >= 1) {
                    $("ul.xim-tagsinput-list-related li")[1].click();
                }
            }, 500);
            if (l >= 4) {
                $("ul.xim-tagsinput-list-related li")[l - 4].click();
            }
        }
    });

    textDescription = _("You can also create your own custom tags or browse tag sets to choose the best one for your content.");
    arrayText.push(textDescription);
    config_action.push({
        "name": ".ontology-browser-container",
        "position": "B",
        "beforeTooltip": function () {
            if ($(".ontology-close").length) {
                $(".ontology-close").click();

            }
        }
    });

    textDescription = _("With the Ontology Browser you can navigate through vocabularies and ontologies as schema.org, etc.");
    arrayText.push(textDescription);
    config_action.push({
        "name": "div.ontology-browser div.treeViewer select",
        "position": "R",
        "beforeTooltip": function () {
            $("div.ontology-browser-container a.tree").click();
            $("#xim-tour-tag").remove();
        }
    });


    textDescription = _("Tag 'Painting' from schema.org has been associated to your document.");
    arrayText.push(textDescription);
    config_action.push({
        "name": "#angular-content",
        "position": "R",
        "beforeTooltip": function () {
            var tabsScope = $("#angular-content").scope();
            tabsScope.setActiveTabById(ximTourDefaultNode + "_setmetadata");
            if ($(".ontology-close").length) {
                $(".ontology-close").click();
                $(".infobox").hide();
            }

            var $newTagHtml = $('<li id="xim-tour-tag" ng-repeat="tag in documentTags" class="xim-tagsinput-tag icon xim-tagsinput-type-structured"/>').html('<span data-tooltip="ontologies/json/SchemaOrg.json" class="xim-tagsinput-text ng-binding">Painting</span> 					<a target="_blank" class="ontology_link ng-binding" ng-href="ontologies/json/SchemaOrg.json" href="ontologies/json/SchemaOrg.json">OntologyBrowser</a>					<a ng-click="removeTag($index)" href="#" class="xim-tagsinput-tag-remove icon"> × </a>');

            $(".xim-tagsinput-list").append($newTagHtml);

        },
        "callback": function () {
        }
    });


//Last step
    textDescription = _("Now it's time to change your document. Let's go!");
    arrayText.push(textDescription);
    config_action.push({
        "name": "div.browser-action-view-content:not(.ng-hide) iframe.action_iframe",
        "position": "B",
        // "scope": "editor",
        "timeToWait": 100,
        "beforeTooltip": function () {
            var tabsScope = $("#angular-content").scope();
            tabsScope.setActiveTabById(ximTourDefaultNode + "_xmleditor2");
            $("#xim-tour-tag").remove();
        },
        "callback": function () {

            var frameCount = window.frames.length;
            var f = $("iframe#kupu-editor", window.frames[frameCount - 1].document)[0];
            var doc = f.contentWindow ? f.contentWindow.document :
                f.contentDocument ? f.contentDocument : f.document;
            $("div.item div.body p:first", doc).addClass("rng-element-selected");
            $("div.item div.body", doc).addClass("rng-parent-selected");
        }
    });


    /***** Steps execution *****/

    for (var i in config_action) {
        config_action[i].text = arrayText[i];
        config_action[i].title = arrayTitle[i];
    }

    X.getTourInstance().start(config_action, command, false);
}
