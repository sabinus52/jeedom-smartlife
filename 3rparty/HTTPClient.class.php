<?php

class HTTPClient
{

    const STRING = 'string';
    const XML  = 'xml';
    const JSON = 'json';


    private function get($url)
    {
        //print 
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    private function post($url, $body)
    {
        //print 
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: text/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    public function postJSON($url, array $datas)
    {
        //return $this->post($url, json_encode($datas));
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: text/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datas));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function postForm($url, array $datas)
    {
        return $this->post($url, $datas);
    }

    public function getQuery($url, array $datas)
    {
        $usrl.= '?';
        foreach ($datas as $key => $value) {
            $url.= "$key=$value&";
        }
        return $this->get($url);
    }
}