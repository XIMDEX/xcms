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


class Action_filedownload_multiple extends ActionAbstract {

	public function index() {
		$nodes = $this->request->getParam('nodes');
		$tmpFolder = FsUtils::getUniqueFolder(
			Config::getValue('AppRoot') . Config::getValue('TempRoot'), '', 'export_'
		);
    		
		if (!FsUtils::mkdir($tmpFolder)) {
    			$this->messages->add(_('A temporal directory to export could not be created'), MSG_TYPE_ERROR);
    			$this->render(array($this->messages), NULL, 'messages.tpl');
    			return;
    		}
		$children = array();
		
		if(!empty($nodes)){
			foreach ($nodes as $nodeid) {
				$node = new Node($nodeid);
	    			if (!$node->get('IdNode')) {
					continue;
				}

				$children = $node->getChildren();
                $numChildren = count($children);
                if($numChildren>0){
    				$folder = $tmpFolder . '/' . $node->get('Name');
	    			$errors = $this->copyContents($folder, $children);
	            }
			}

			$tarFile = $this->tarContents($tmpFolder);
			$this->deleteContents($tmpFolder);
		}

		if (!is_file($tarFile)) {
            XMD_Log::error('All selected documents could not be exported. Do you have zip installed?');
		}

		$tarFile = preg_replace(sprintf('#^%s#', Config::getValue('AppRoot')), Config::getValue('UrlRoot'), $tarFile);

		$values = array(
			'nodeName' => basename($tarFile),
			'tarFile' => $tarFile,
            'numChildren' => $numChildren
		);

		$this->addJs('/actions/filedownload_multiple/resources/js/index.js');
		$this->render($values, '', 'default-3.0.tpl');
    }

    private function copyContents($folder, $nodes) {
    	if (!FsUtils::mkdir($folder)) {
    		return false;
    	}

    	$errors = array();

	    if(!empty($nodes)){
    		foreach ($nodes as $nodeid) {
	    		$node = new Node($nodeid);
	    		if (!$node->get('IdNode')) {
				    continue;
			    }
			    $fileName = $node->get('Name');
			    $filePath = $folder . '/' . $fileName;
			    if($node->GetNodeType()==NodetypeService::COMMON_FOLDER ||
			    	$node->GetNodeType()==NodetypeService::CSS_FOLDER ||
			    	$node->GetNodeType()==NodetypeService::IMAGES_FOLDER){
			    	if(!$this->copyContents($filePath, $node->GetChildren())){
			    		$errors[] = $fileName;
			    	}
			    }elseif (!FsUtils::file_put_contents($filePath, $node->getContent())) {
				    $errors[] = $fileName;
			    }
   		    }
	    }

    	if (count($errors) == 0) {
    		$errors = false;
    	}
		return $errors;
    }

    private function deleteContents($tmpFolder) {
       	$ret = FsUtils::deltree($tmpFolder);
	    if (!$ret) {
    		XMD_Log::info(_("Directory could not be deleted ").$tmpFolder);
    	}
    }

    private function tarContents($folderToTar) {
    	$tarName = sprintf('%s/%s.zip', dirname($folderToTar), basename($folderToTar));
    	exec(sprintf('cd %s && zip -r %s %s', $folderToTar, $tarName, '*'));
    	return $tarName;
    }
}
?>