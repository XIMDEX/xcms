<?php
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



require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryHandler_TOL_DOCSEARCH.class.php');

class QueryHandler_TOL_SIMPLESEARCH extends QueryHandler_TOL_DOCSEARCH {

	protected function createQuery(&$options) {

		$term = $options['filters'][0]['content'];
		
		$byId = '';
		if (is_numeric($term)) {
			$byId = sprintf('or docid = %s', $term);
		}

		$query = sprintf(
			"select d.docid, d.path, d.titulo, d.tipoid, dt.nombre as nombretipo, d.publicado, d.fechaalta, d.fecharevision, d.fechadocumento from TOL.DOCUMENTO d, TOL.DOCTIPO dt where dt.tipoid = d.tipoid and (regexp_like(substr(d.path, 32), '%s') %s) %%userFilter%% order by d.path",
			$term, $byId
		);
		
		return $query;
	}

}

?>