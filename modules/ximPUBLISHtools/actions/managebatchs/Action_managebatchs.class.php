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

use Ximdex\Logger;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Utils\Serializer;
use Ximdex\Runtime\Session;
use Ximdex\Models\Node;

\Ximdex\Modules\Manager::file('/actions/FilterParameters.php', 'ximPUBLISHtools');
\Ximdex\Modules\Manager::file('/inc/model/PublishingReport.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/Batch.class.php', 'ximSYNC');

class Action_managebatchs extends ActionAbstract
{
    private $params = array();
    

    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $acceso = true;
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        
        // Initializing variables
        $userID = Session::get('userID');
        $user = new User();
        $user->SetID($userID);
        if (!$user->HasPermission("view_publication_resume")) {
            $acceso = false;
            $errorMsg = "You have not access to this report. Consult an administrator.";
        } else {
            $errorMsg = '';
        }
        $jsFiles = array(
            App::getValue('UrlRoot') . \Ximdex\Modules\Manager::path('ximPUBLISHtools') . '/actions/managebatchs/resources/js/index.js',
            App::getUrl('/assets/js/ximtimer.js')
        );
        $cssFiles = array(
            App::getValue('UrlRoot') . \Ximdex\Modules\Manager::path('ximPUBLISHtools') . '/actions/managebatchs/resources/css/index.css'
        );
        $arrValores = array(
            'acceso' => $acceso,
            'errorBox' => $errorMsg,
            'js_files' => $jsFiles,
            'node_Type' => $node->nodeType->GetName(),
            'css_files' => $cssFiles
        );
        $this->render($arrValores, null, 'default-3.0.tpl');
    }

    private function filterParams()
    {
        $this->params['idNode'] = FilterParameters::filterInteger($this->request->getParam("nodeid"));
        $this->params['idBatch'] = FilterParameters::filterInteger($this->request->getParam("idBatch"));
        $this->params['dateFrom'] = FilterParameters::filterInteger($this->request->getParam("dateFrom"));
        $this->params['dateTo'] = FilterParameters::filterInteger($this->request->getParam("dateTo"));
        $this->params['finished'] = FilterParameters::filterBool($this->request->getParam("finished"));
        $this->params['searchText'] = FilterParameters::filterText($this->request->getParam("searchText"));
    }

    private function retrieveFrameList()
    {
        $pr = new PublishingReport();
        $frames = $pr->getReports($this->params);
        $json = Serializer::encode(SZR_JSON, $frames);
        return array(
            'result' => $json
        );
    }

    public function getFrameList()
    {
        $this->filterParams();
        $values = $this->retrieveFrameList();
        $this->render($values, null, "only_template.tpl");
    }

    public function stopBatch()
    {
        $success = false;
        if (!$_POST['frm_deactivate_batch'] !== "yes") {
            if (!$result = $this->doDeactivateBatch($_POST['frm_id_batch'])) {
                $errorMsg = "An error occurred while deactivate batch.";
            } else {
                $success = true;
                if ($_POST['frm_id_batch'] == "all") {
                    $errorMsg = "All batches have been deactivated.";
                } else {
                    $errorMsg = "Batch #" . $_POST['frm_id_batch'] . " has been deactivated.";
                }
            }
            Logger::info("PUBLISH results: $errorMsg");
        }
        $json = Serializer::encode(SZR_JSON, array('success' => $success));
        $this->render(array('result' => $json), null, "only_template.tpl");
    }

    public function startBatch()
    {
        if (!$_POST['frm_activate_batch'] !== "yes") {
            if (!$result = $this->doActivateBatch($_POST['frm_id_batch'])) {
                $errorMsg = "An error occurred while activating batch.";
            } else {
                if ($_POST['frm_id_batch'] == "all") {
                    $errorMsg = "All batches have been activated.";
                } else {
                    $errorMsg = "Batch #" . $_POST['frm_id_batch'] . " has been activated.";
                }
            }
        }
        $json = Serializer::encode(SZR_JSON, array('success' => true));
        $this->render(array('result' => $json), null, "only_template.tpl");
    }

    public function changeBatchPriority()
    {
        $mode = 'up';
        if (isset($_POST['frm_increase']) && $_POST['frm_increase'] == "yes") {
            Logger::info('PUBLISH pre doPrioritizeBatch');
        } elseif (isset($_POST['frm_decrease']) && $_POST['frm_decrease'] == "yes") {
            Logger::info('PUBLISH pre doUnprioritizeBatch');
            $mode = 'down';
        }
        if (!$result = $this->doPrioritizeBatch($_POST['frm_id_batch'], $mode)) {
            Logger::error("An error occurred while changing batch priority ($mode).");
        } else {
            Logger::info("Batch #" . $_POST['frm_id_batch'] . " priority has been changed ($mode).");
        }
        $json = Serializer::encode(SZR_JSON, array('success' => true));
        $this->render(array('result' => $json), null, "only_template.tpl");
    }

    private function doActivateBatch($idBatch)
    {
        if ($idBatch != "all") {
            $batchObj = new Batch();
            return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 1);
        } else {
            $batchManagerObj = new BatchManager();
            return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 1);
        }
    }

    private function doDeactivateBatch($idBatch)
    {
        if ($idBatch !== "all") {
            $idBatch = (int) $idBatch;
            $batchObj = new Batch();
            return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 0);
        } else {
            $batchManagerObj = new BatchManager();
            return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 0);
        }
    }

    private function doPrioritizeBatch($idBatch, $mode = 'up')
    {
        $batch = new Batch();
        $hasChanged = $batch->prioritizeBatch($idBatch, $mode);
        return $hasChanged;
    }
}
