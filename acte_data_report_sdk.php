<?php

class ReportApi
{
    private $base_url;
    private $token;
    private $is_connected;

    function __construct($base_url)
    {
        $this->base_url = $base_url;
        $this->token = null;
        $this->is_connected = false;
    }

    function connect($username, $password)
    {
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];
        $params = array("username" => $username, "password" => $password);

        $this->token = null;

        try {
            $response = $this->post($this->base_url . "/api/token", $headers, $params);
            $data = json_decode($response, true);
            $this->token = $data['access_token'];
            $this->is_connected = true;
        } catch (Exception $e) {
            $this->is_connected = false;
            throw new Exception($e);
        }
    }

    function check_connection()
    {
        if (!$this->token) {
            throw new Exception("No token found, please connect first");
        }

        if ($this->is_connected) {
            return true;
        } else {
            throw new Exception("Not connected. please connect first");
        }
    }

    function getDatabases()
    {
        $this->check_connection();
        $headers = [
            'Authorization: Bearer ' . $this->token,
        ];

        try {
            $response = $this->get($this->base_url . "/api/users/me", $headers);
            $data = json_decode($response, true);
            $databases = $data['databases'];
            if (count($databases) == 0) {
                throw new Exception("No authorized databases found, please contact your administrator to grant access");
            }

            return $databases;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    function getDevices($db)
    {
        $this->check_connection();
        $headers = [
            'Authorization: Bearer ' . $this->token,
        ];

        try {
            $url = $this->base_url . "/api/v3/" . $db . "/devices";
            $response = $this->get($url, $headers);

            $devices = json_decode($response, true);
            return $devices;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    function getKeys($db, $device)
    {
        $this->check_connection();
        $headers = [
            'Authorization: Bearer ' . $this->token,
        ];
        $params = ["device" => $device];

        try {
            $url = $this->base_url . "/api/v3/" . $db . "/keys";
            $response = $this->get($url, $headers, $params);
            $keys = json_decode($response, true);
            return $keys;
        } catch (Exception $e) {
            echo $response['content'];
            throw new Exception($e);
        }
    }

    function getTelemetry($db, $device, $key, $start, $end)
    {
        $this->check_connection();
        $headers = [
            'Authorization: Bearer ' . $this->token,
        ];
        $params = [
            "device" => $device,
            "key" => $key,
            "from_date" => $start,
            "to_date" => $end
        ];

        try {
            $url = $this->base_url . "/api/v3/" . $db . "/telemetry";
            $response = $this->get($url, $headers, $params);
            $telemetry = json_decode($response, true);
            return $telemetry;
        } catch (Exception $e) {
            echo $response['content'];
            throw new Exception($e);
        }
    }

    private function get($url, $headers, $params = null)
    {
        if ($params) {
            $query = http_build_query($params);
            $url .= '?' . $query;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    private function post($url, $headers, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
