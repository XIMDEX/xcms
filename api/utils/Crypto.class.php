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
 * <p>Utility class providing utility methods to encrypt and decrypt texts</p>
 *
 */
class Crypto {
    const ALGO_AES_128_CBC = "aes-128-cbc";
    
    /**
     * <p>Encrypts a text using the given key and iv (Initialization Vector) parameters
     * @param string $plaintext the text to encrypt
     * @param string $key the key to be used to encrypt
     * @param string $iv the initialization vector to be used to encrypt
     * @return string the encrypted text
     */
    public static function encryptAES($plaintext, $key, $iv) {
        /*   Key and IV generated with the command
         * openssl enc -aes-128-cbc -k "MY_SECRET_PHRASE" -P -md sha1
         */
        return openssl_encrypt($plaintext, Crypto::ALGO_AES_128_CBC, $key, 0, $iv);
        
    }
    
    /**
     * <p>Decrypts the text using the given key and iv (Initialization Vector) parameters</p>
     * @param string $encryptedtext the text to decrypt
     * @param string $key the key to be used to decrypt
     * @param string $iv the initialization vector to be used to decrypt
     * @return string the decrypted text
     */
    public static function decryptAES($encryptedtext, $key, $iv) {
        return openssl_decrypt($encryptedtext, Crypto::ALGO_AES_128_CBC, $key, 0, $iv);
    }
    
}

?>
