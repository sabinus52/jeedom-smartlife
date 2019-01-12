<?php

class TokenManager
{

    private $session;

    private $client;

	private $tokenAccess;

    private $tokenRefresh;

    private $expireTime;


    public function __construct($session, $client = null)
    {
        $this->session = $session;
        $this->client = ($client) ? $client : new HTTPClient();
    }


    public function getToken()
    {
        if ( !$this->hasToken() ) {
            $this->createToken();
            $this->session->getPlatform()->setRegionFromToken($this->tokenAccess);
        }
        if ( !$this->isValidToken() ) {
            $this->refreshAccessToken();
        }
        return $this->tokenAccess;
    }


	public function createToken()
    {
        //$uri = UriResolver::resolve($this->getBaseUrl($this->session), new Uri('homeassistant/auth.do'));
        $url = $this->session->getBaseUrl('/homeassistant/auth.do');
        $response = $this->client->postForm($url, array(
            'userName'    => $this->session->getUsername(),
            'password'    => $this->session->getPassword(),
            'countryCode' => $this->session->getCountry(),
            'bizType'     => $this->session->getPlatform()->getBizType(),
            'from'        => 'tuya',
        ));
        print 'CREATE : '.$response."\n";
        $response = json_decode($response, true); // TODO gestion erreur
        $this->setTokenFromArray($response);
    }


    private function refreshToken()
    {
        $url = $this->session->getBaseUrl('/homeassistant/access.do');
        $response = $this->client->getQuery($url, array(
            'grant_type'    => 'refresh_token',
            'refresh_token' => $this->tokenManager->getToken()->getRefreshToken(),
        ));
        print 'REFRESH : '.$response."\n";
        $response = json_decode($response, true); // TODO gestion erreur
        $this->setTokenFromArray($response);
    }

    private function setTokenFromArray(array $data)
    {
        $this->tokenAccess = $data['access_token'];
        $this->tokenRefresh = $data['refresh_token'];
        $this->expireTime = $data['expires_in'];
    }


    private function hasToken()
    {
        if ( $this->tokenAccess && $this->tokenRefresh && $this->expireTime )
            return true;
        else
            return false;
    }

    private function isValidToken()
    {
        return time() + $this->expireTime > time();
    }


        private function checkAccessToken()
    {
        if (!$this->tokenManager->hasToken()) {
            $token = $this->getAccessToken();
            $this->tokenManager->setToken($token);
            $this->session->setRegion(Region::fromAccessToken($token));
        }
        if (!$this->tokenManager->isValidToken()) {
            $this->tokenManager->setToken($this->refreshAccessToken());
        }
    }

}