<?php
class SatoClient
{
    private $username;
    private $api_key;
    private $api_url = 'https://www.satoexchange.com/api/v2/';
    private $id;

    private $mode = 'file_get_contents';
    private static $modes = ['curl' => 'curl', 'file_get_contents' => 'file_get_contents'];
    function __construct($username, $api_key)
    {
        $this->username = $username;
        $this->api_key = $api_key;
        $this->id = 0;
    }

    //RPC Methods
    function account_orders($params = [])
    {
        return $this->request('account_orders', $params, $this->id);
    }
    function account_trade_history($params = [])
    {
        return $this->request('account_trade_history', $params, $this->id);
    }
    function balances($params = [])
    {
        return $this->request('balances', $params, $this->id);
    }
    function deposit($params = [])
    {
        return $this->request('deposit', $params, $this->id);
    }
    function get_currencies($params = [])
    {
        return $this->request('get_currencies', $params, $this->id);
    }
    function get_market_history($params = [])
    {
        return $this->request('get_market_history', $params, $this->id);
    }
    function get_market_orders($params = [])
    {
        return $this->request('get_market_orders', $params, $this->id);
    }
    function get_markets($params = [])
    {
        return $this->request('get_markets', $params, $this->id);
    }
    function order($params = [])
    {
        return $this->request('order', $params, $this->id);
    }

    function buy($params = [])
    {
        $params['action'] = 'buy';
        return $this->request('order', $params, $this->id);
    }
    function sell($params = [])
    {
        $params['action'] = 'sell';
        return $this->request('order', $params, $this->id);
    }

    //Utility Methods
    function auth($method, $timestamp)
    {
        return hash('sha256', $this->username . $this->api_key . $method . $timestamp);
    }
    function request($method, $params, $id = 1)
    {
        if ($id < 1) {
            $id = 1;
        }
        $this->id = $id + 1;
        $timestamp = time();
        $params['username'] = $this->username;
        $params['timestamp'] = $timestamp;
        $params['auth'] = $this->auth($method, $timestamp);

        $body = ['jsonrpc' => '2.0', 'id' => $id, 'method' => $method, 'params' => $params];
        $data = [];
        if ($this->mode == 'file_get_contents') {
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($body),
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($this->api_url, false, $context);
            $data = json_decode($result, true);
        } elseif ($this->mode == 'curl') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

            $result = curl_exec($ch);
            $data = json_decode($result, true);
        }
        return $data;
    }

    function set_mode($mode)
    {
        if (in_array($mode, self::$modes)) $this->mode = $mode;
    }
    function get_mode()
    {
        return $this->mode;
    }
}
