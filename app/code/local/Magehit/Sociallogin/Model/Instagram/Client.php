<?php

class Magehit_Sociallogin_Model_Instagram_Client
{
    const REDIRECT_URI_ROUTE = 'mhsociallogin/instagram/connect';

    const XML_PATH_ENABLED = 'magehit/magehit_sociallogin_instagram/enabled';
    const XML_PATH_CLIENT_ID = 'magehit/magehit_sociallogin_instagram/client_id';
    const XML_PATH_CLIENT_SECRET = 'magehit/magehit_sociallogin_instagram/client_secret';

    const OAUTH2_SERVICE_URI = 'https://api.instagram.com/v1/';
    const OAUTH2_AUTH_URI = 'https://api.instagram.com/oauth/authorize/';
    const OAUTH2_TOKEN_URI = 'https://api.instagram.com/oauth/access_token';

    protected $clientId = null;
    protected $clientSecret = null;
    protected $redirectUri = null;
    protected $state = '';
    protected $scope = array('likes', 'comments');

    protected $token = null;
    protected $data = array();

    public function __construct($params = array())
    {
        if(($this->isEnabled = $this->_isEnabled())) {
            $this->clientId = $this->_getClientId();
            $this->clientSecret = $this->_getClientSecret();
            $this->redirectUri = Mage::getModel('core/url')->sessionUrlVar(
                Mage::getUrl(self::REDIRECT_URI_ROUTE)
            );

            if(!empty($params['scope'])) {
                $this->scope = $params['scope'];
            }

            if(!empty($params['state'])) {
                $this->state = $params['state'];
            }
        }
    }
    public function setData($data){
        return $this->data = $data;
    }
    
    public function getData(){
        return $this->data;
    }

    public function isEnabled()
    {
        return (bool) $this->isEnabled;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function setAccessToken($token)
    {
        $this->token = ($token);
        
    }

    public function getAccessToken()
    {
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }

        return json_encode($this->token);
    }

    public function createAuthUrl()
    {

        $url =
        self::OAUTH2_AUTH_URI.'?'.
            http_build_query(
                array(
                    'client_id' => $this->clientId,
                    'redirect_uri' => $this->redirectUri,
                    //'scope' => implode('+', $this->scope),
                    'response_type' => 'code'
                    )
            );
        return $url;
    }

    public function getOAuthToken($code, $token = false)
    {
        $apiData = array(
            'client_id'     => $this->_getClientId(),
            'client_secret' => $this->_getClientSecret(),
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $this->getRedirectUri(),
            'code'          => $code
        );
        
        $result = $this->_makeOAuthCall($apiData);

        return (false === $token) ? $result : $result->access_token;
    }
    
    private function _makeOAuthCall($apiData)
    {      
        $apiHost = self::OAUTH2_TOKEN_URI;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiHost);
        curl_setopt($ch, CURLOPT_POST, count($apiData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $jsonData = curl_exec($ch);
        if (!$jsonData) {
            throw new InstagramException('Error: _makeOAuthCall() - cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return json_decode($jsonData);
    }

    protected function fetchAccessToken()
    {
        if(empty($_REQUEST['code'])) {
            throw new Exception(
                Mage::helper('sociallogin')
                    ->__('Unable to retrieve access code.')
            );
        }

        $response = $this->_httpRequest(
            self::OAUTH2_TOKEN_URI,
            'POST',
            array(
                'code' => $_REQUEST['code'],
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code'
            )
        );

        $this->token = $response;
    }

    protected function _httpRequest($url, $method = 'GET', $params = array())
    {
        $client = new Zend_Http_Client($url, array('timeout' => 60));

        switch ($method) {
            case 'GET':
                $client->setParameterGet($params);
                break;
            case 'POST':
                $client->setParameterPost($params);
                break;
            case 'DELETE':
                $client->setParameterGet($params);
                break;
            default:
                throw new Exception(
                    Mage::helper('sociallogin')
                        ->__('Required HTTP method is not supported.')
                );
        }

        $response = $client->request($method);

        Mage::log($response->getStatus().' - '. $response->getBody());

        $decoded_response = json_decode($response->getBody());

       
        if(empty($decoded_response)) {
            $parsed_response = array();
            parse_str($response->getBody(), $parsed_response);

            $decoded_response = json_decode(json_encode($parsed_response));
        }

        if($response->isError()) {
            $status = $response->getStatus();
            if(($status == 400 || $status == 401)) {
                if(isset($decoded_response->error->message)) {
                    $message = $decoded_response->error->message;
                } else {
                    $message = Mage::helper('sociallogin')
                        ->__('Unspecified OAuth error occurred.');
                }

                throw new Magehit_Sociallogin_InstagramOAuthException($message);
            } else {
                $message = sprintf(
                    Mage::helper('sociallogin')
                        ->__('HTTP error %d occurred while issuing request.'),
                    $status
                );

                throw new Exception($message);
            }
        }

        return $decoded_response;
    }

    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }

    protected function _getClientId()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    protected function _getClientSecret()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_SECRET);
    }

    protected function _getStoreConfig($xmlPath)
    {
        return Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId());
    }
    
    private function _makeCall($function, $auth = false, $params = null)
    {
                     
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }
        
        if (false === $auth) {
            // if the call doesn't requires authentication
            $authMethod = '?client_id=' . $this->clientId;
        } else {
            // if the call needs a authenticated user
            if (true === isset($this->token)) {
                $authMethod = '?access_token=' . $this->token;
            } else {
                throw new Exeption("Error: _makeCall() | $function - This method requires an authenticated users access token.");
            }
        }

        if (isset($params) && is_array($params)) {
            $params = '&' . http_build_query($params);
        } else {
            $params = null;
        }

        $apiCall = self::OAUTH2_SERVICE_URI . $function . $authMethod . $params;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiCall);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $jsonData = curl_exec($ch);
        curl_close($ch);

        return json_decode($jsonData);
    }

}

class Magehit_Sociallogin_InstagramOAuthException extends Exception
{}