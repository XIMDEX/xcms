<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace XimdexApi\core;

use Ximdex\Runtime\App;

class Token
{
    /**
     * Default Token Time-To-Live in minutes
     */
    const DEFAULT_TTL = 5;
    const ALG_AES_128_CBC = "aes-128-cbc";
    const EXPIRATION_ENABLE = false;

    /**
     * Generates a new token for the given user
     *
     * @param string $user the username which generate a token for
     * @param int $ttl Optional parameter indicating the lifetime of the new token
     * @return string
     */
    public static function getToken($user, $ttl = self::DEFAULT_TTL)
    {
        $now = time();
        $tokenTTL = intval($ttl);
        $validTo = $now + ($tokenTTL * 60);
        $token = array('user' => $user, 'created' => time(), 'validTo' => $validTo);
        $token = json_encode($token);
        $token = base64_encode(static::encryptAES($token, App::GetValue('ApiKey'), App::GetValue('ApiIV')));
        return $token;
    }

    /**
     * Validates the token given as parameter
     *
     * @param string $token the token to be validated
     * @return boolean indicating whether the token is valid or not
     */
    public static function validateToken($token)
    {
        $decryptedToken = json_decode(static::decryptAES(base64_decode($token), App::GetValue('ApiKey'), App::GetValue('ApiIV')), true);
        if ($decryptedToken == null)
            return false;
        if (static::EXPIRATION_ENABLE && !($decryptedToken['validTo'] > time()))
            return false;
        return true;
    }

    /**
     * Decrypts the given token obtaining and associative array containing the token information
     * 
     * @param string $token the encrypted token
     * @return array the decrypted token
     */
    public static function decryptToken(string $token)
    {
        return json_decode(static::decryptAES(base64_decode($token), App::GetValue('ApiKey'), App::GetValue('ApiIV')), true);
    }

    /**
     * Determines whether the given token has expired or not using a decrypted token
     *
     * @param array $token the decrypted token
     * @return boolean the token has expired or not
     */
    public function hasExpired($token)
    {
        if (!isset($token['validTo']))
            return true;
        if (!($token['validTo'] > time())) {
            return true;
        }
        return false;
    }

    /**
     * Encrypts a text using the given key and iv (Initialization Vector) parameters
     *
     * @param string $plaintext the text to encrypt
     * @param string $key the key to be used to encrypt
     * @param string $iv the initialization vector to be used to encrypt
     * @return string the encrypted text
     */
    public static function encryptAES($plaintext, $key, $iv)
    {
        /*
		Key and IV generated with the command
        openssl enc -aes-128-cbc -k "MY_SECRET_PHRASE" -P -md sha1
        */
        return @openssl_encrypt($plaintext, self::ALG_AES_128_CBC, $key, 0, $iv);
    }

    /**
     * Decrypts the text using the given key and iv (Initialization Vector) parameters
     *
     * @param string $encryptedtext the text to decrypt
     * @param string $key the key to be used to decrypt
     * @param string $iv the initialization vector to be used to decrypt
     * @return string the decrypted text
     */
    public static function decryptAES($encryptedtext, $key, $iv)
    {
        return @openssl_decrypt($encryptedtext, self::ALG_AES_128_CBC, $key, 0, $iv);
    }
}
