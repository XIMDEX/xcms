<?php

/**
 *  \details &copy; 2014  Open Ximdex Evolution SL [http://www.ximdex.org]
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
if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../"));
}

//
ModulesManager::file('/inc/IndexerLifecycle.iface.php', 'XRAM');
ModulesManager::file('/inc/AES.class.php', 'XRAM');

class AESProcessor implements IndexerLifecycle
{
    const CIPHER_KEY_PARAM = "AESCipherKey";

    private $key;

    /**
     * <p>Creates an instance of AESProcessor using the given key to be used for ciphering</p>
     * <p>If no key is provided, the processor will try to get it from configuration, creating a new random one if no key exists in the configuration</p>
     */
    public function __construct($cipherKey = NULL)
    {
        if ($cipherKey != NULL) {
            $this->key = $cipherKey;
        } else {
            $this->checkAndInitKey();
        }
    }

    /**
     * <p>Check if the key exists in the configuration in order to be used. Otherwise a new random key is created</p>
     */
    private function checkAndInitKey()
    {
        $cipherKey = \App::getValue(self::CIPHER_KEY_PARAM, null);

        if (!is_null($cipherKey)) {
            $this->key = $cipherKey;
        } else {
            $key = $this->createRandomString();
            \App::setValue(self::CIPHER_KEY_PARAM, $key, true );
            $this->key = $key;
        }
    }

    /**
     * <p>Creates a new random string</p>
     * @param int $length The length of the string to be created. Default is 10
     */
    private function createRandomString($length = 10)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    public function afterIndex($document)
    {
    }

    public function afterRetrieve($document)
    {
        $aes = new AES($document['content'], $this->key);
        $document['content'] = $aes->decrypt();
        return $document;
    }

    public function beforeIndex($document)
    {
        $aes = new AES($document['content'], $this->key);
        $document['content'] = $aes->encrypt();
        return $document;
    }

    public function beforeRetrieve()
    {
    }

    public function beforeDelete($id)
    {

    }

    public function afterDelete($id)
    {
    }
}
