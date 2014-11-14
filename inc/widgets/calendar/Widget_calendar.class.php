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



require_once (XIMDEX_ROOT_PATH . '/inc/widgets/Widget_Abstract.class.php');

class Widget_calendar extends Widget_Abstract {



	/**
	*Default method for widget params
        * 	
	*@param $params: Current params in Smarty
	*@return array with js_files, css_files and attributes
	*/
	public function process($params) {

		//Format for responsed value
		if(!array_key_exists("format", $params)) {
				$params['format'] = _('m-d-Y H:i:s');
		}

		//Format for displayed value 
		if(!array_key_exists("format_display", $params)) {
			$params["format_display"] = _('mm-dd-yy H:i');
		}
                
                $formatDisplayPieces = explode(' ', $params['format_display']);
                $dateFormatDisplay = $formatDisplayPieces[0];
		$params["date_format_display"] = $dateFormatDisplay;
                $params["server_timestamp"] = time()*1000;
                

                if ($params["type"] == "interval"){
                    $this->buildIntervalParams($params);
                }		

                    
                
		//
		$format = str_replace("dd", "d", $params["format_display"]);
		$format = str_replace("mm", "m", $format);
		$format = str_replace("yy", "Y", $format);

		//Exploding in date and time
		$formatPieces = explode(' ', $params['format']);
		$dateFormat = $formatPieces[0];
		$timeFormat = $formatPieces[1];

		//By default, from calendar. 
		if (!array_key_exists("type", $params) || is_null($params['type']) || $params['type'] != 'to' ) {
			//$params['type'] = 'from';
			$params['widget_label'] = _('Validity start');
		}else {
			$params['widget_label'] = _('Validity end');
		}

		//if from type, the default value is the param timestamp or now time.
		if('from' == $params['type']) {
			 $params['timestamp_value'] = (!array_key_exists('timestamp', $params) || is_null($params['timestamp']) )? time() : (int) $params['timestamp'];
			 $params['timestamp_name'] = 'publishDate';
			 $params['label_button'] = _('Now');
		}else {
			 $params['timestamp_value'] = (!array_key_exists("timestamp", $params) || is_null($params['timestamp']) )? '' : (int) $params['timestamp'];
			 $params['timestamp_name'] = 'unpublishDate';
			 $params['label_button'] = _('Undetermined');
		}


		//CCalendarName?
		if(array_key_exists("cname", $params) &&  !is_null($params['cname'] ) ) {
		  $params['timestamp_name'] = $params['cname'];
		}

		//Set the server time.
		$params['timestamp_current'] = date($dateFormat);
		if( $params['timestamp_value']) {
			$params['datevalue'] = date($format, $params['timestamp_value']);
			/*
			$params['hourvalue'] = date($hourFormat, $params['timestamp_value']);
			$params['minvalue'] =  date($minFormat, $params['timestamp_value']);
			$params['secvalue'] =  date($secFormat, $params['timestamp_value']);
			*/
		}else {
			$params['datevalue'] = '00-00-0000 00:00:00';
			/*
			$params['hourvalue'] = '00';
			$params['minvalue'] =  '00';
			$params['secvalue'] =  '00';
			*/
		}


		$params["timezone"] = XIMDEX_TIMEZONE;

		return parent::process($params);
	}
        
        /**
         * Build parameters for interval calendar
         * @param array $params reference to params array.
         */
        private function buildIntervalParams(& $params){
            $params["first_date_text"] = $params["first_now_text"] = _("Now");
            $arrayDateFormat = explode(" ", $params["format"]);
            $params["first_time_text"] = date($arrayDateFormat[0], time());
            $params["last_date_text"] = $params["last_now_text"] = _("No caducate");
            $params["last_time_text"] = "--/--/----";
            
        }


	function date_system() {
		echo time();
	}

}

?>