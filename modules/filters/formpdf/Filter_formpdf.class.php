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




ModulesManager::file('/inc/filters/Filter.class.php');
ModulesManager::component('/formpdf/parser/parser.class.php', 'filters');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::file('/inc/helper/Languages.class.php');

class Filter_formpdf extends Filter {

	function Filter_formpdf() {
	}

	/**
	 * This method load the configuration
	 * passes a name which is the key of associative array of configurations
	 */
	function loadConfig($configName) {
		ModulesManager::component('/formpdf/Filter_persistence/Config.class.php.php', 'filters');
		$this->config->set($configName, $formpdf_config[$configName]);
	}

	function initObject() {
		parent::initObject();

		// Default init actions for this class.
		//$this->loadConfig('DIN-A4');
	}

	/**
	 * Print format configuration
	 */
	function printConfig() {
		$this->config->display();
	}

	function _getPackagesList($packagesBaseDir) {

		$folders[] = $packagesBaseDir;
		$handler = opendir($packagesBaseDir);
		while (($file = readdir($handler)) !== false) {
			$fileCompletePath = sprintf('%s%s', $packagesBaseDir, $file);
			if (is_dir($fileCompletePath) && $file[0] != '.') {
				$folders[] = $fileCompletePath;
				$folders[] = $this->_getPackagesList($fileCompletePath . '/');
			}
		}
		return implode(':', $folders);
	}

	function filter($input, $output, &$header) {

		putenv("TEXMFOUTPUT=/tmp");
		$texInputs = sprintf("TEXINPUTS=/tmp/:%s", $this->_getPackagesList(XIMDEX_ROOT_PATH."/extensions/latex/"));
		putenv($texInputs);
		$parser = new parser();
		$parser->set_input($input);
		$parser->set_output($output);
		$parser->build();

		// delete // with tests from ximdex
		//$this->delete_files($exception);

		$header["Content-type"] = "application/pdf";
		$header["Content-Length"] = filesize($output);
		$header["Content-Disposition"] = "attachment; filename=$output";
		$header["Accept-Ranges"] = strlen(ltrim($output));
	}

	function delete_files($exception = NULL) {
		$sourcedir = opendir("/tmp");
		while (false !== ($filename = readdir($sourcedir))) {
			if (is_file("/tmp/".$filename) && preg_match("/filter/",$filename) > 0) {
				if (("/tmp/".$filename) != $exception) {
					FsUtils::delete("/tmp/".$filename);
				}
			}
		}
		FsUtils::delete("/tmp/command.tex");
		closedir($sourcedir);
	}
}

?>