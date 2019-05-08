<?php

require __DIR__.'/HTTPClient.class.php';
require __DIR__.'/Platform.class.php';
require __DIR__.'/Session.class.php';
require __DIR__.'/TokenManager.class.php';
require __DIR__.'/DeviceEvent.php';
require __DIR__.'/SwitchDevice.php';



class SmartLifeClient
{

	/**
     * @var Session
     */
    private $session;

    private $client;

    private $token;


    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->client = new HTTPClient();
        $this->token = new TokenManager($session);
    }


    public function discoverDevices()
    {
        $response = $this->request('Discovery', 'discovery');
        if (!$response) return null;
        $devices = array();
        foreach ($response['payload']['devices'] as $datas) {
            $device = null;
            switch ($datas['dev_type']) {
                case 'switch':
                    $device = new SwitchDevice($datas['id'], $datas['name']);
                    $device->setOnline($datas['data']['online']);
                    $device->setState($datas['data']['state']);
                    break;
            }
            if ($device) $devices[] = $device;
        }
        return $devices;
    }


    public function sendEvent(DeviceEvent $event, $namespace = 'control')
    {
        $payload = $event->getPayload();
        $this->request($event->getAction(), $namespace, $payload);
    }


    private function request($name, $namespace, array $payload = [])
    {
        $token = $this->token->getToken();
        //var_dump($this->token);
        if (!$token) return null;
        $url = $this->session->getBaseUrl('/homeassistant/skill');
        $response = $this->client->postJSON($url, array(
            'header' => array(
                'name'           => $name,
                'namespace'      => $namespace,
                'payloadVersion' => 1,
            ),
            'payload' => $payload + array(
                'accessToken'    => $token,
            ),
        ));
        //print_r(json_decode($response));
        return json_decode($response, true);


    }

   

}
