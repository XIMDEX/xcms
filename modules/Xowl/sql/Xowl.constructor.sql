/**
# *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
# *
# *  Ximdex a Semantic Content Management System (CMS)
# *
# *  This program is free software: you can redistribute it and/or modify
# *  it under the terms of the GNU Affero General Public License as published
# *  by the Free Software Foundation, either version 3 of the License, or
# *  (at your option) any later version.
# *
# *  This program is distributed in the hope that it will be useful,
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# *  GNU Affero General Public License for more details.
# *
# *  See the Affero GNU General Public License for more details.
# *  You should have received a copy of the Affero GNU General Public License
# *  version 3 along with Ximdex (see LICENSE file).
# *
# *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
# *
# *  @author Ximdex DevTeam <dev@ximdex.com>
# *  @version $Revision$
# */

-- Key set to empty from Config table

INSERT INTO Config VALUES (NULL,'Xowl_location','http://extra.ximdex.net:8080/engines/');

INSERT INTO Namespaces (service, type, nemo, uri, recursive, category, isSemantic)
VALUES
("Xowl","ZemantaImage","zImage","http://<ximdex_local_url>/service/rdfs/ZemantaImage", 0, "image",0),
("Xowl","ZemantaLink","zLink","http://<ximdex_local_url>/service/rdfs/ZemantaLink", 0, "link",0),
("Xowl","ZemantaArticle","zArticle","http://<ximdex_local_url>/service/rdfs/ZemantaArticle", 0, "article",0),
("Xowl","DbpediaPerson","dPerson","http://dbpedia.org/ontology/person", 0, "person",1),
("Xowl","DbpediaPlace","dPlace","http://dbpedia.org/ontology/place", 0, "place",1),
("Xowl","DbpediaOrganisation","dOrganisation","http://dbpedia.org/ontology/organisation", 0, "organisation",1);
