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


use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;

ModulesManager::file('/inc/model/orm/RelNewsBulletins_ORM.class.php', 'ximNEWS');


class RelNewsBulletins extends RelNewsBulletins_ORM
{

    /**
     *  Adds a row to RelNewsBulletins table.
     * @param int idBulletin
     * @param int idNew
     * @param int idColector
     * @return bool
     */

    public function add($idBulletin = null, $idNew = null , $idColector = null )
    {

        // If the new was in another bulletin of this collector, deleting relation

        $ximNewsBulletin = new XimNewsBulletin();
        $idBulletinWithNew = $ximNewsBulletin->getBulletinWithNew($idColector, $idNew);

        if ($idBulletinWithNew) $this->deleteRelation($idNew, $idBulletinWithNew);

        // If it was not in this bulletin, creating relation

        if (!$this->bulletinHasNew($idBulletin, $idNew)) {

            $this->set('IdBulletin', $idBulletin);
            $this->set('IdNew', $idNew);

            if (!parent::add()) {
                Logger::info(_('Error inserting RelNewsBulletin'));
                return false;
            }
        }

        return true;
    }

    /**
     *  Gets the rows from RelNewsBulletins which matching the value of IdNew.
     * @param int idNew
     * @return array|null
     */

    function getBulletinFromNew($idNew)
    {
        if (!($idNew > 0)) {
            return NULL;
        }

        $dbObj = new DB();
        $sql = sprintf("SELECT IdBulletin from RelNewsBulletins WHERE IdNew = %s",
            $dbObj->sqlEscapeString($idNew));
        $dbObj->Query($sql);

        $result = array();
        while (!$dbObj->EOF) {
            $result[] = $dbObj->GetValue('IdBulletin');
            $dbObj->Next();
        }
        return !empty($result) ? $result : NULL;
    }

    /**
     *  Gets the rows from RelNewsBulletins which matching the value of IdBulletin.
     * @param int idBulletin
     * @return array|null
     */

    function GetNewsByBulletin($idBulletin)
    {

        $nodeType = new NodeType('XimNewsBulletin');
        $idNodeType = $nodeType->get('IdNodeType');

        $node = new Node($idBulletin);

        $array_bulletins = array();
        if ($node->GetNodeType() == $idNodeType) {
            $array_bulletins = $node->GetChildren();
        } else {
            $array_bulletins[] = $idBulletin;
        }

        $dbObj = new DB();
        $resultado = array();
        foreach ($array_bulletins as $idBulletin) {
            $query = sprintf("SELECT IdNew FROM RelNewsBulletins WHERE IdBulletin = %s", $dbObj->sqlEscapeString($idBulletin));

            $dbObj->Query($query);
            while (!$dbObj->EOF) {
                $resultado[] = $dbObj->GetValue('IdNew');
                $dbObj->Next();
            }
        }
        return $resultado;
    }

    /**
     *  Deletes the rows from RelNewsBulletins which matching the value of IdNew.
     * @param int idNew
     * @return bool
     */

    function deleteByNew($idNew)
    {
        $dbObj = new DB();
        $query = sprintf("DELETE from RelNewsBulletins WHERE IdNew = %s", $dbObj->sqlEscapeString($idNew));
        return $dbObj->Execute($query);
    }

    /**
     *  Deletes the rows from RelNewsBulletins which matching the value of IdBulletin.
     * @param int idBulletin
     * @return bool
     */

    function deleteByBulletin($idBulletin)
    {
        $dbObj = new DB();
        $query = sprintf("DELETE from RelNewsBulletins WHERE IdBulletin = %s", $dbObj->sqlEscapeString($idBulletin));
        return $dbObj->Execute($query);
    }

    /**
     *  Deletes the rows from RelNewsBulletins which matching the values of IdBulletin and IdNew.
     * @param int idBulletin
     * @param int idNews
     * @return bool
     */

    function deleteRelation($idNews, $idBulletin)
    {
        $dbObj = new DB();
        $query = sprintf("DELETE from RelNewsBulletins WHERE IdNew = %s AND IdBulletin = %s",
            $dbObj->sqlEscapeString($idNews),
            $dbObj->sqlEscapeString($idBulletin));
        return $dbObj->Execute($query);
    }

    /**
     *  Gets the number of rows from RelNewsBulletins which matching the value of IdBulletin.
     * @param int idBulletin
     * @return int
     */

    function countNewsByBulletin($idBulletin)
    {
        return count($this->GetNewsByBulletin($idBulletin));
    }

    /**
     *  Checks if exist a row from RelNewsBulletins which matching the values of IdBulletin and IdNew.
     * @param int IdBulletin
     * @param int IdNew
     * @return bool
     */

    function bulletinHasNew($idBulletin, $idNews)
    {
        $dbObj = new DB();
        $query = sprintf("SELECT IdRel from RelNewsBulletins WHERE IdNew = %s AND IdBulletin = %s",
            $dbObj->sqlEscapeString($idNews),
            $dbObj->sqlEscapeString($idBulletin));

        $dbObj->Query($query);

        return ($dbObj->numRows > 0);
    }

}

?>