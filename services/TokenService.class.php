<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
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

/**
 * <p>Service responsible of generate and validate tokens</p>
 *
 */
class TokenService {
 
    /**
     * <p>Default Token Time-To-Live in minutes</p>
     */
    const DEFAULT_TTL = 5;
    
    /**
     * <p>Default constructor</p>
     */
    public function __construct() {
        
    }
    
    /**
     * <p>Generates a new token for the given user</p>
     * @param string $user the username which generate a token for
     * @param int $ttl Optional parameter indicating the lifetime of the new token
     */
    public function getToken($user, $ttl = TokenService::DEFAULT_TTL) {
        $now = time();
        $tokenTTL = intval($ttl);
        
        $validTo = $now + ($tokenTTL * 60);

        $token = array('user' => $user, 'created' => time(), 'validTo' => $validTo);
        $token = json_encode($token);
        $token = base64_encode(Crypto::encryptAES($token, \App::getValue( 'ApiKey'), \App::getValue( 'ApiIV')));
        return $token;
    }
    
    /**
     * <p>Validates the token given as parameter</p>
     * @param string $token the token to be validated
     * @return boolean indicating whether the token is valid or not
     */
    public function validateToken($token) {
        $decryptedToken = json_decode(Crypto::decryptAES(base64_decode($token), \App::getValue( 'ApiKey'), \App::getValue( 'ApiIV')), true);

        if ($decryptedToken == null)
            return false;
        
        if (!($decryptedToken['validTo'] > time()))
            return false;
            
        return true;
    }

    /**
     * <p>Decrypts the given token obtaining and associative array containing the token information</p>
     * @param string $token the encrypted token
     * @param string $key the key used to decrypt the token
     * @param string $iv the initialization vector used to decrypt the token
     * @return array the decrypted token
     */
    public function decryptToken($token, $key, $iv) {
        $decryptedToken = json_decode(Crypto::decryptAES(base64_decode($token), $key, $iv), true);
        return $decryptedToken;
    }
    
    /**
     * <p>Determines whether the given token has expired or not using a decrypted token</p>
     * @param array $token the decrypted token
     * @return boolean the token has expired or not
     */
    public function hasExpired($token) {
        if(!isset($token['validTo']))
            return true;
        
        if (!($token['validTo'] > time())) {
            return true;
        }
        
        return false;
    }
}

?>