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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


use Ximdex\Runtime\App;

include_once '../../../bootstrap.php';

ModulesManager::file('/inc/i18n/I18N.class.php');

\Ximdex\Runtime\Session::check();

$locale = \Ximdex\Runtime\Session::get('locale');
// Check coherence with HTTP_ACCEPT_LANGUAGE
I18N::setup($locale);
$userID = \Ximdex\Runtime\Session::get('userID');

header('Content-type: application/javascript');

echo "renderer = '" . \Ximdex\Runtime\Session::get("renderer") . "';";
echo "\nurl_host = '" . App::getValue('UrlHost') . "';";
echo "\nurl_root = '" . App::getValue('UrlRoot') . "';";
echo "\nximdex_root = '" . XIMDEX_ROOT_PATH . "';";
echo "\nbase_action = '" . \Ximdex\Runtime\Session::get("base_action") . "';";
echo "\nuser_id = '" . \Ximdex\Runtime\Session::get('userID') . "';";
echo "\nlocale = '" . \Ximdex\Runtime\Session::get('locale') . "';";
?>
function NodeTypes()
{
<?php
    $nodeType = new \Ximdex\NodeTypes\NodeType;
    $reflect = new ReflectionClass($nodeType);
    $constants = $reflect->getConstants();
    foreach ($constants as $constant => $value) {
?>
	this.<?php echo $constant; ?> = '<?php echo $value; ?>';
<?php } ?>
}
var nodeTypes = new NodeTypes();