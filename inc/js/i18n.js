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


(function(X) {

	X.i18nStrings = {
		'es': {
			'actions': {
				'checkstatus': {
					'publications': {
						'published':{
							'title': 'Documentos publicados'
						},
						'unpublished':{
							'title': 'Documentos en cola de publicación'
						}
					}
				}
			},
			'test propouse string': 'cadena de testeo',
		},
		'en': {
			'actions': {
				'checkstatus': {
					'publications': {
						'published':{
							'title': 'Published documents'
						},
						'unpublished':{
							'title': 'Documents in publication queue'
						}
					}
				}
			},
			'test propouse string': 'cadena de testeo',
		}	
	};

})(com.ximdex);

var _  = function(input) {	
	console.log("ammigo");
	return X.i18nStrings[window.document.documentElement.lang]? X.i18nStrings[window.document.documentElement.lang][input] || input : input
};