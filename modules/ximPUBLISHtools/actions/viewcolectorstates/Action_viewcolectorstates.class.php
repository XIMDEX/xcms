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



use Ximdex\Utils\Session;
ModulesManager::file('/inc/model/node.inc');
ModulesManager::file('/inc/ximNEWS_Adapter.php', 'ximNEWS');
ModulesManager::file('/inc/serializer/Serializer.class.php');
ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColectorUsers.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsColectorUsers.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelColectorUsersBatchs.php', 'ximNEWS');

class Action_viewcolectorstates extends ActionAbstract {

    function index() {
		$idNode	= (int) $this->request->getParam("nodeid");
		
		$node = new Node($idNode);
		$idSection = $node->GetSection();
		$sectionNode = new Node($idSection);
		$sectionName = $sectionNode->getNodeName();
		
		$colectors = $this->getColectorsData($idSection);
		
		$jsFiles = array(
			//App::getValue('UrlRoot') . '/xmd/js/lib/prototype/prototype.js',
			App::getValue('UrlRoot') .ModulesManager::path('ximPUBLISHtools'). '/actions/viewcolectorstates/resources/js/index.js'
		);
		
		$cssFiles = array(
			App::getValue('UrlRoot') .ModulesManager::path('ximPUBLISHtools'). '/actions/viewcolectorstates/resources/css/index.css'
		);
		
		$query = App::get('QueryManager');
		$getDataUrl = $query->getPage() . $query->buildWith(array('method' => 'getData'));

		$values = array(
			'id_node' => $idNode,
			'js_files' => $jsFiles,
			'css_files' => $cssFiles,
			'colectors' => $colectors,
			'section_name' => $sectionName,
			'get_data_url' => $getDataUrl,
			'id_page' => 1
		);

		$this->render($values, NULL, 'default-3.0.tpl');
    }
    
    function getData () {
		$idNode	= (int) $this->request->getParam("nodeid");
		$pag = $this->request->getParam("pag");
		
		$node = new Node($idNode);
		$idSection = $node->GetSection();
		$sectionNode = new Node($idSection);
		$sectionName = $sectionNode->getNodeName();
		$colectorsData = $this->getColectorsData($idSection, $pag);
		
		$json = Serializer::encode(SZR_JSON, $colectorsData);
		
		$values = array(
			'result' => $json
		);
		$this->render($values, NULL, "only_template.tpl");
    }
    
    function getColectorsData ($idSection, $pag = array()) {
		
		$states = array(
			0 => 'Gener�ndose',
			1 => 'En espera de generaci�n total',
			2 => 'En espera de generaci�n parcial',
			3 => 'Generado',
			4 => 'Generado y publicado',
			5 => 'Generado y public�ndose',
		);
		
		$idUser = Session::get('userID');
		$user = new User();
		$user->SetID($idUser);
		$groups = $user->GetGroupList();
		
		$ximNewsColector = new XimNewsColector();
		$relNewsColectorUsers = new RelNewsColectorUsers();
		$relNewsColector = new RelNewsColector();
		$ximNewsColectorUsers = new XimNewsColectorUsers();
		$relColectorUsersBatchs = new RelcolectorUsersBatchs();
		$colectors = array();
		if($colectorsBySection = $ximNewsColector->getColectors($idSection, $groups)) {
			foreach($colectorsBySection as $idColector => $colector) {
				$pendingRelations = $relNewsColectorUsers->getPendingRelations(null, $idColector);
				$generationsData = $ximNewsColectorUsers->getColectorGenerationsData($idColector);
				$progressPublicationData = $relColectorUsersBatchs->getPublicationProgress($generationsData[0]['Id']);
				
				$page = (is_array($pag) && isset($pag[$idColector])) ? $pag[$idColector] : 1;
				//$newsAssociated = $relNewsColector->getAllNewsFromColector($idColector, ($page - 1) * 10, 10);
				$col = new XimNewsColector($idColector);
				
				$showGenerationStamps = false;
				$modifier = 0.8;
				if($generationsData && $generationsData[0]['State'] != 'published') {
					switch($generationsData[0]['State']) {
						case 'generating':
							$colectorStateId = 0;
							break;
						case 'publishing': 
							$colectorStateId = 0;
							break;
					}
					$showGenerationStamps = true;
					$colectorProgress = floor($generationsData[0]['Progress'] * $modifier);
				} elseif($col->get('ForceTotalGeneration') == 1) {
					$colectorStateId = 1;
					$colectorProgress = 0;
				} elseif($pendingRelations) {
					$colectorStateId = 2;
					$colectorProgress = 0;
				} else {
					$showGenerationStamps = true;
					$colectorStateId = 3;
					$colectorProgress = 100;
					if(count($progressPublicationData) > 0) {
						$colectorProgress = 80;
						$publicationProgressPercent = floor(($progressPublicationData['ended'] * 100) / ($progressPublicationData['ended'] + $progressPublicationData['pending']));
						if($publicationProgressPercent == 100) {
							$colectorProgress = 100;
							$colectorStateId = 4;
						} else {
							$publicationProgress = floor($publicationProgressPercent * (1 - $modifier));
							$colectorProgress += $publicationProgress;
							$colectorStateId = 5;
						}
					}
				}
				
				if($generationsData[0]['IdUser'] != 0) {
					$generatorUser = new User($generationsData[0]['IdUser']);
					$userName = $generatorUser->get('Name');
				} else {
					$userName = 'Sistema';
				}
				
				$colectorState = $states[$colectorStateId];
				
				$colectors[] = array(
					'pending_relations' => $pendingRelations,
					/*'news_associated' => $newsAssociated,*/
					'id_colector' => $col->get('IdColector'),
					'name' => $col->get('Name'),
					'last_generation' => $col->get('LastGeneration'),
					'locked' => $col->get('Locked'),
					'force_total_generation' => $col->get('ForceTotalGeneration'),
					'state' => $colectorState,
					'state_id' => $colectorStateId,
					'progress' => $colectorProgress,
					'user_name' => $userName,
					'start_generation' => $showGenerationStamps ? $generationsData[0]['StartGenerationTime']: null,
					'end_generation' => $showGenerationStamps ? $generationsData[0]['EndGenerationTime'] : null,
					'end_publication' => $showGenerationStamps ? $generationsData[0]['EndPublicationTime'] : null
				);
			}
		}
		
		return $colectors;
    }
}
?>
