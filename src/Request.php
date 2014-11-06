<?php
namespace DeezerAPI;

class Request
{
    private $returnAssoc = false;

    const ACCOUNT_URL = 'https://connect.deezer.com';
    const API_URL = 'http://api.deezer.com';

    /**
     * Make a request to the "api" endpoint.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param array $parameters Optional. Query parameters.
     * @param array $headers Optional. HTTP headers.
     *
     * @return array
     */
    public function api($type, $uri, $parameters = array(), $headers = array())
    {
        return $this->send($type, self::API_URL . $uri, $parameters, $headers);
    }

    /**
     * Make a request to Deezer.
     * You'll probably want to use one of the convenience methods instead.
     *
     * @param string $method The HTTP method to use.
     * @param string $url The URL to request.
     * @param array $parameters Optional. Query parameters.
     * @param array $headers Optional. HTTP headers.
     *
     * @return array
     */
    public function send($type, $url, $parameters = array(), $headers = array())
    {

        $options = array(
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true
        );

        $url = rtrim($url, '/');
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';

        switch($type) {
            case 'POST':
                $parameters['request_method'] = 'post';
                break;
            case 'DELETE':
                $parameters['request_method'] = 'delete';
                break;
            default:
                break;
        }

        $parameters = http_build_query($parameters);

        if ($parameters) {
            $url .= '/?' . $parameters;
        }

        $options[CURLOPT_URL] = $url;

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        list($headers, $body) = explode("\r\n\r\n", $response, 2);

        $body = json_decode($body, $this->returnAssoc);

        if (isset($body->error)) {
            $error = $body->error;

            // These properties only exist on API calls, not auth calls
            if (isset($error->message) && isset($error->code) && isset($error->type)) {
                $exception = 'DeezerAPI\Exception\\'.$error->type;
                throw new $exception($error->message, $error->code);
            } elseif (isset($error->message) && isset($error->code)) {
                throw new Exception\DeezerAPIException($body->message, $error->code);
            } elseif (isset($error->message)) {
                throw new Exception\DeezerAPIException($error->message);
            }
        }

        return array(
            'body' => $body,
            'headers' => $headers,
            'status' => $status
        );
    }

    /**
     * Set the return type for the body element
     * If unset or set to false it will return a stdObject, but
     * if set to true it will return an associative array.
     *
     * @param bool $returnAssoc Whether to return an associative array or not.
     *
     * @return void
     */
    public function setReturnAssoc($returnAssoc)
    {
        $this->returnAssoc = $returnAssoc;
    }

    /**
     * Returns true if this class returns the body as an
     * associative array, and false if it returns the body
     * as a stdObject.
     *
     * @return bool true if body is returned as an array, else false.
     */
    public function getReturnAssoc()
    {
        return $this->returnAssoc;
    }
}
