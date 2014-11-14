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



class CliReader {

	/**
	 * 
	 * @param $continueOptions
	 * @param $continueMessage
	 * @param $abortOptions
	 * @param $abortMessage
	 * @return unknown_type
	 */
	function alert($continueOptions, $continueMessage, $abortOptions = NULL, $abortMessage = NULL) {
		if (is_null($abortOptions)) {
			$abortOptions = array();
		}
		$stdin = fopen('php://stdin', 'r');
		echo $continueMessage;
		$options = array_merge($continueOptions, $abortOptions);
		
		do	{
			$readed = fread($stdin, 1);
			if (ord($readed) > 32) {
				$string = $readed;
			}
		} while(!in_array($string, $options));
		fclose($stdin);	
		
		if (in_array($string,$abortOptions)) {
			echo $abortMessage;
			return false;
		}
		
		return $string;
	}
	
	/**
	 * 
	 * @param $continueMessage
	 * @param $hiddenInput
	 * @return unknown_type
	 */
	function getString($continueMessage, $hiddenInput = false) {
		$stdin = fopen('php://stdin', 'r');
		echo $continueMessage;
		if ($hiddenInput) {
			exec('stty -echo');
		}
		$string = '';
		do	{
			$readed = fgetc($stdin);
			if (ord($readed) == 10) {
				break;
			}
			$string .= $readed;
		} while(true);
		fclose($stdin);	

		return $string;
	}
}

?>