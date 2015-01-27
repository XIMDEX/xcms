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

ModulesManager::file('/inc/manager/ServerFrameManager.class.php');
ModulesManager::file('/inc/model/PublishingReport.class.php', 'ximSYNC');

class Action_managebatchs extends ActionAbstract {

    private $params = array();

    // Main method: shows initial form
    function index() {
        $acceso = true;
        // Initializing variables.
        $userID = Session::get('userID');

        $user = new User();
        $user->SetID($userID);

        if (!$user->HasPermission("view_publication_resume")) {
            $acceso = false;
            $errorMsg = "You have not access to this report. Consult an administrator.";
        }


        $jsFiles = array(
            App::getValue('UrlRoot') . ModulesManager::path('ximPUBLISHtools') . '/actions/managebatchs/resources/js/index.js',
            App::getValue('UrlRoot') . '/inc/js/ximtimer.js'
        );

        $cssFiles = array(
            App::getValue('UrlRoot') . ModulesManager::path('ximPUBLISHtools') . '/actions/managebatchs/resources/css/index.css'
        );

        $arrValores = array(
            'acceso' => $acceso,
            'errorBox' => $errorMsg,
            'js_files' => $jsFiles,
            'css_files' => $cssFiles
        );

        $this->render($arrValores, NULL, 'default-3.0.tpl');
    }

    // TODO implement filterParameter service
    private function filterInteger($var) {
        if (preg_match('/^\d+$/', $var)) {
            return intval($var);
        }
        return null;
    }

    private function filterBool($var) {
        if (preg_match('/^[0|1]?$/', $var)) {
            return (bool) $var;
        }
        return null;
    }

    private function filterText($var) {
        if (preg_match('/^[\w|_]+$/', $var)) {
            return trim($var);
        }
        return null;
    }

    private function filterParams() {
        $this->params['idNode'] = $this->filterInteger($this->request->getParam("nodeid"));
        $this->params['idBatch'] = $this->filterInteger($this->request->getParam("idBatch"));
        $this->params['dateFrom'] = $this->filterInteger($this->request->getParam("dateFrom"));
        $this->params['dateTo'] = $this->filterInteger($this->request->getParam("dateTo"));
//        $this->params['user'] = $this->filterText($this->request->getParam("user"));
        $this->params['finished'] = $this->filterBool($this->request->getParam("finished"));
        $this->params['searchText'] = $this->filterText($this->request->getParam("searchText"));
    }

    private function retrieveFrameList() {
        XMD_Log::info('PUBLISH getReports');
        $pr = new PublishingReport();
        $frames = $pr->getReports($this->params);
        XMD_Log::info("PUBLISH frames count: " . count($frames));
        $json = Serializer::encode(SZR_JSON, $frames);

        return array(
            'result' => $json
        );
    }

    public function getFrameList() {
        $this->filterParams();
        XMD_Log::error('PUBLISH getFrameList');
        $values = $this->retrieveFrameList();
        $this->render($values, NULL, "only_template.tpl");
    }

    function stopBatch() {
        XMD_Log::error('PUBLISH stopBatch');
        $success = false;
        if (!$_POST['frm_deactivate_batch'] !== "yes") {
            XMD_Log::error('PUBLISH stopBatch2');

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

            XMD_Log::error("PUBLISH results: $errorMsg");
        }

        $json = Serializer::encode(SZR_JSON, array('success' => $success));
        $this->render(array('result' => $json), NULL, "only_template.tpl");
    }

    function startBatch() {
        XMD_Log::error('PUBLISH startBatch');
        if (!$_POST['frm_activate_batch'] !== "yes") {
            XMD_Log::error('PUBLISH startBatch2');

            if (!$result = $this->doActivateBatch($_POST['frm_id_batch'])) {
                $errorMsg = "An error occurred while activating batch.";
            } else {
                if ($_POST['frm_id_batch'] == "all") {
                    $errorMsg = "All batches have been activated.";
                } else {
                    $errorMsg = "Batch #" . $_POST['frm_id_batch'] . " has been activated.";
                }
            }

            XMD_Log::error("PUBLISH results: $errorMsg");
        }

//        $values = $this->retrieveFrameList();
        $json = Serializer::encode(SZR_JSON, array('success' => true));
        $this->render(array('result' => $json), NULL, "only_template.tpl");
    }

    function changeBatchPriority() {
        XMD_Log::info('PUBLISH changeBatchPriority');

        $mode = 'up';
        if (isset($_POST['frm_increase']) && $_POST['frm_increase'] == "yes") {
            XMD_Log::info('PUBLISH pre doPrioritizeBatch');
        } else if (isset($_POST['frm_decrease']) && $_POST['frm_decrease'] == "yes") {
            XMD_Log::info('PUBLISH pre doUnprioritizeBatch');
            $mode = 'down';
        }

        if (!$result = $this->doPrioritizeBatch($_POST['frm_id_batch'], $mode)) {
            XMD_Log::error("An error occurred while changing batch priority ($mode).");
        } else {
            XMD_Log::error("Batch #" . $_POST['frm_id_batch'] . " priority has been changed ($mode).");
        }

        $json = Serializer::encode(SZR_JSON, array('success' => true));
        $this->render(array('result' => $json), NULL, "only_template.tpl");
    }

    function doActivateBatch($idBatch) {
        if ($idBatch != "all") {
            $batchObj = new Batch();
            return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 1);
        } else {

            $batchManagerObj = new BatchManager();
            return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 1);
        }
    }

    function doDeactivateBatch($idBatch) {
        XMD_Log::error('PUBLISH doDeactivateBatch');
        if ($idBatch !== "all") {
            $idBatch = (int) $idBatch;
            $batchObj = new Batch();
            return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 0);
        } else {
            $batchManagerObj = new BatchManager();
            return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 0);
        }
    }

    function doPrioritizeBatch($idBatch, $mode = 'up') {
        $batch = new Batch();
        $hasChanged = $batch->prioritizeBatch($idBatch, $mode);
        return $hasChanged;
    }

}

?>
