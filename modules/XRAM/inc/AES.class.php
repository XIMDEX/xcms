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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

/**
 * <p>AES encryption class</p>
 * <p>Performs encryption and decryption using AES cipher algorithm</p>
 */
class AES
{

    const M_CBC = 'cbc';
    const M_CFB = 'cfb';
    const M_ECB = 'ecb';
    const M_NOFB = 'nofb';
    const M_OFB = 'ofb';
    const M_STREAM = 'stream';

    protected $key;
    protected $cipher;
    protected $data;
    protected $mode;
    protected $IV;

    /**
     * <p>Creates a new AES instance using the provided data, key, block size and mode</p>
     * @param type $data
     * @param type $key
     * @param type $blockSize
     * @param type $mode
     */
    function __construct($data = null, $key = null, $blockSize = null, $mode = null)
    {
        $this->setData($data);
        $this->setKey($key);
        $this->setBlockSize($blockSize);
        $this->setMode($mode);
        $this->setIV("");
    }

    /**
     * <p>Sets the data to be encrypted</p>
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * <p>Sets the key used for encryption and decryption</p>
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * <p>Sets the block size to be used</p>
     * <p>The cipher algorithm to be used depends on the block size provided</p>
     * <p>Allowed values for block size are:
     *   <ul>
     *      <li>128 -> RIJNDAEL 128 bits algorithm</li>
     *      <li>192 -> RIJNDAEL 192 bits algorithm</li>
     *      <li>256 -> RIJNDAEL 256 bits algorithm</li>
     *   </ul>
     * </p>
     * 
     * @param int $blockSize The block size. Default is 128
     */
    public function setBlockSize($blockSize)
    {
        switch ($blockSize) {
            case 128:
                $this->cipher = MCRYPT_RIJNDAEL_128;
                break;
            case 192:
                $this->cipher = MCRYPT_RIJNDAEL_192;
                break;
            case 256:
                $this->cipher = MCRYPT_RIJNDAEL_256;
                break;
            default:
                error_log("default entering cipher");
                $this->cipher = MCRYPT_RIJNDAEL_128;
        }
    }

    /**
     * <p>Sets the ciphering mode</p>
     * <p>The AES class constants can be used for this parameter. Allowed values are:
     *  <ul>
     *      <li>cbc or AES::M_CBC</li>
     *      <li>cfb or AES::M_CFB</li>
     *      <li>ecb or AES::M_ECB</li>
     *      <li>nofb or AES::M_NOFB</li>
     *      <li>ofb or AES::M_OFB</li>
     *      <li>stream or AES::M_STREAM</li>
     *  </ul>
     * </p>
    * @param string $mode The ciphering mode. Default is AES::M_ECB
     */
    public function setMode($mode)
    {
        switch ($mode) {
            case AES::M_CBC:
                $this->mode = MCRYPT_MODE_CBC;
                break;
            case AES::M_CFB:
                $this->mode = MCRYPT_MODE_CFB;
                break;
            case AES::M_ECB:
                $this->mode = MCRYPT_MODE_ECB;
                break;
            case AES::M_NOFB:
                $this->mode = MCRYPT_MODE_NOFB;
                break;
            case AES::M_OFB:
                $this->mode = MCRYPT_MODE_OFB;
                break;
            case AES::M_STREAM:
                $this->mode = MCRYPT_MODE_STREAM;
                break;
            default:
                $this->mode = MCRYPT_MODE_ECB;
                break;
        }
    }

    /**
     * <p>Validates the mandatory parameters</p>
     * @return boolean if the parameters are valid. false otherwise
     */
    public function validateParams()
    {
        if ($this->data != null &&
                $this->key != null &&
                $this->cipher != null) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * <p>Sets the Initialization Vector (IV) for the cipher algorithm</p>
     * @param string $IV
     */
    public function setIV($IV)
    {
        $this->IV = $IV;
    }

    /**
     * <p>Gets the current initialization vector</p>
     * <p>If IV has not been set yet, a new IV is created and returned</p>
     * 
     * @return string the Initialization Vector
     */
    protected function getIV()
    {
        if ($this->IV == "") {
            $this->IV = mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_RAND);
        }
        return $this->IV;
    }

    /**
     * <p>Encrypts the data with the configured key using the selected cipher algorithm (by the block size) and the cipher mode</p>
     * @return The encrypted data encoded in base 64 or false if an error ocurred while encrypting or encoding the data
     * @throws Exception if the provided information is not valid
     */
    public function encrypt()
    {
        if ($this->validateParams()) {
            return trim(base64_encode(
                            mcrypt_encrypt(
                                    $this->cipher, $this->key, $this->data, $this->mode, $this->getIV())));
        } else {
            throw new Exception('Invalid params!');
        }
    }

    /**
     * <p>Decrypts the data (encoded in base 64) with the configured key using the selected cipher algorithm (by the block size) and the ciphering mode</p> 
     * @return The decrypted data or false if an error ocurred while decrypting or decoding the data
     * @throws Exception if the provided information is not valid
     */
    public function decrypt()
    {
        if ($this->validateParams()) {
            return trim(mcrypt_decrypt(
                            $this->cipher, $this->key, base64_decode($this->data), $this->mode, $this->getIV()));
        } else {
            throw new Exception('Invalid params!');
        }
    }

}

?>
