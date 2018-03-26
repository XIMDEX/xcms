<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 19/02/16
 * Time: 14:39
 */

namespace XimdexApi\core;


class Response
{
    private $status = 0;
    private $response = null;
    private $message = '';

    public function __construct()
    {

    }

    /**
     * Sets the status code
     *
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Sets the message
     *
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Sets the response
     *
     * @param $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Sends reponse and exists
     * @param array $headers
     * @return string
     */
    public function send($headers = null)
    {
        if (!is_null($headers)) {
            foreach ($headers as $key => $value) {
                header($key . ":" . $value);
            }
            echo $this->response;
        } else {
            $data = [
                'status' => $this->status,
                'message' => $this->message,
                'response' => $this->response,
            ];

            // TODO: Check CORS and filters
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Authorization");
            header("Access-Control-Allow-Credentials: true");
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        exit;

    }
}