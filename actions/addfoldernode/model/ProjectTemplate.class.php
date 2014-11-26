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
*  @version $Revision: 8735 $
*/

ModulesManager::file('/actions/addfoldernode/model/BuildParser.class.php');

/**
 * Manage the ProjectTemplate. 
 */
class ProjectTemplate{

	private $name=null;
    private $title=null;
    private $description=null;
	private $project = null;
    public $configurable=false;

	/**
	*Constructor method
	*@param string $name project name
	*@param string $version version of project
	*/
	public function __construct($name) {
		
		$this->name = $name;		

		$b = new BuildParser($name);
        //Init buildProject property
        $this->project = $b->getProject();
        $this->title = $this->project->__get("title");
        $this->description = $this->project->__get("description");
	}

	/**
	*Get all templates for default and specific projects
	*@return array Loader_ximfile .
	*/
	public function getTemplates(){

		$result = array();

		$templates = $this->project->getTemplates();
        foreach ($templates as $template){
            if ($template->__get("filename")){
                $result[$template->__get("filename")] = $template;
            }
        }
        return $result;
	}

	/**
    * Get the match Servers from default and specific project
    * Defaults are mandatory, but specific can overload it.
    * @return array with all Loader_Server objects.
    */
    public function getServers(){

        $result = array();
        $servers = $this->project->getServers();
        foreach ($servers as $server) {
            if ($server->__get("name")){
                $result[$server->__get("name")] = $server;
            }
        }

        return $result;
    }

    /**
    *Get the match Schemes from default and specific project
    *Defaults are mandatory, but specific can overload it.
    *@return array with all Loader_XimFile objects.
    */
    public function getSchemes(){
        $result = array();

        $schemes = $this->project->getSchemes();
        foreach ($schemes as $scheme){
            if ($scheme->__get("filename")){
                $result[$scheme->__get("filename")] = $scheme;
            }
        }

        return $result;
    }

    public function setProjectId($idProject){
    	$this->project->$projectid=$idProject;
    }

    public function getBuildProject(){
        return $this->project;
    }

    /**
	*Static method Get all Project Templates under project folder
    *@return array With all the Project Templates. array[projectName] = Project Templates;
	*/
	public static function getAllProjectTemplates(){
		
		//Returned array if everything is ok.
		$result = array();
		
		$rootThemesFolder = \App::getValue( "AppRoot").THEMES_FOLDER;
		//Getting all theme folders
		$themesFolders = FsUtils::readFolder($rootThemesFolder,false);

		  //For every project
		foreach ($themesFolders as $themeFolder ) {

			$result[$themeFolder] = new ProjectTemplate($themeFolder);			
		}
		return $result;
	}

    public function __get($prop) {
        return $this->$prop;
        }
        
    public function __set($prop, $val) {
        $this->$prop = $val;
        }
}