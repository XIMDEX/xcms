<?php
/**
 * Created by PhpStorm.
 * User: darroyo
 * Date: 2/03/18
 * Time: 10:36
 */

namespace XimdexApi\core;

use Ximdex\Runtime\App;

class Token
{
    /**
     * <p>Default Token Time-To-Live in minutes</p>
     */
    const DEFAULT_TTL = 5;

    const ALG_AES_128_CBC = "aes-128-cbc";
    const EXPIRATION_ENABLE = false; // TODO provisional

    /**
     * <p>Default constructor</p>
     */
    public function __construct()
    {
    }

    /**
     * <p>Generates a new token for the given user</p>
     * @param string $user the username which generate a token for
     * @param int $ttl Optional parameter indicating the lifetime of the new token
     *
     * @return string;
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
     * <p>Validates the token given as parameter</p>
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
     * <p>Decrypts the given token obtaining and associative array containing the token information</p>
     * @param string $token the encrypted token
     * @param string $key the key used to decrypt the token
     * @param string $iv the initialization vector used to decrypt the token
     * @return array the decrypted token
     */
    public function decryptToken($token, $key, $iv)
    {
        $decryptedToken = json_decode(static::decryptAES(base64_decode($token), $key, $iv), true);
        return $decryptedToken;
    }

    /**
     * <p>Determines whether the given token has expired or not using a decrypted token</p>
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
     * <p>Encrypts a text using the given key and iv (Initialization Vector) parameters
     * @param string $plaintext the text to encrypt
     * @param string $key the key to be used to encrypt
     * @param string $iv the initialization vector to be used to encrypt
     * @return string the encrypted text
     */
    public static function encryptAES($plaintext, $key, $iv)
    {
        /*   Key and IV generated with the command
         * openssl enc -aes-128-cbc -k "MY_SECRET_PHRASE" -P -md sha1
         */
        return \openssl_encrypt($plaintext, self::ALG_AES_128_CBC, $key, 0, $iv);
    }

    /**
     * <p>Decrypts the text using the given key and iv (Initialization Vector) parameters</p>
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