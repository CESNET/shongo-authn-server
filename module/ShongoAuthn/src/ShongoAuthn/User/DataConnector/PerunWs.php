<?php

namespace ShongoAuthn\User\DataConnector;

use Zend\Http;
use Zend\Json;
use ShongoAuthn\User\User;
use InoOicServer\User\Validator\Exception\InvalidUserException;
use InoOicServer\User\DataConnector\AbstractDataConnector;
use InoOicServer\User\UserInterface;


class PerunWs extends AbstractDataConnector implements ShongoDataConnectorInterface
{

    const OPT_BASE_URL = 'base_url';

    const OPT_SECRET = 'secret';

    const OPT_USERS_HANDLER = 'users_handler';

    const OPT_HTTP_CLIENT_CONFIG = 'http_client_config';

    protected $fieldMap = array(
        'phone' => User::FIELD_PHONE_NUMBER,
        'organization' => User::FIELD_ORGANIZATION,
        'language' => User::FIELD_LOCALE,
        'timezone' => User::FIELD_ZONEINFO
    );


    /**
     * {@inhertidoc}
     * @see \InoOicServer\User\DataConnector\DataConnectorInterface::populateUser()
     */
    public function populateUser(UserInterface $user)
    {
        $this->populateShongoUser($user);
    }


    /**
     * {@inhertidoc}
     * @see \ShongoAuthn\User\DataConnector\ShongoDataConnectorInterface::populateShongoUser()
     */
    public function populateShongoUser(User $user)
    {
        $perunId = $user->getPerunId();
        if (! $perunId) {
            return;
        }
        
        $perunUserData = $this->getPerunUserData($perunId);
        
        $userData = array();
        foreach ($this->fieldMap as $perunField => $userField) {
            if (isset($perunUserData[$perunField])) {
                $userData[$userField] = $perunUserData[$perunField];
                $user->setValue($userField, $perunUserData[$perunField]);
            }
        }
        
        if (isset($perunUserData['_links']['self']['href'])) {
            $user->setPerunUrl($perunUserData['_links']['self']['href']);
        }
    }


    protected function getPerunUserData($perunId)
    {
        $requestUrl = $this->constructRequestUrl($perunId);
        $secret = $this->getSecret();
        
        $userData = $this->requestWs($requestUrl, $secret);
        return $userData;
    }


    protected function requestWs($requestUrl, $secret)
    {
        $httpClient = $this->initHttpClient();
        $httpRequest = $this->initHttpRequest($requestUrl, $secret);
        
        try {
            $httpResponse = $httpClient->send($httpRequest);
        } catch (\Exception $e) {
            throw new Exception\TransportException(
                sprintf("HTTP client exception: [%s] %s, request URL: %s", get_class($e), $e->getMessage(), $requestUrl), 
                null, $e);
        }
        
        $statusCode = $httpResponse->getStatusCode();
        $rawData = $httpResponse->getContent();
        
        if (200 !== $statusCode) {
            $message = sprintf("Error status code '%s'", $statusCode);
            try {
                $errorData = $this->decodeJsonData($rawData);
                $message .= sprintf(": [%s] %s", $errorData['title'], $errorData['detail']);
            } catch (\Exception $e) {}
            
            throw new Exception\ErrorServerResponse($message);
        }
        
        try {
            $userData = $this->decodeJsonData($rawData);
        } catch (\Exception $e) {
            throw new Exception\InvalidServerDataException(
                sprintf("Error decoding JSON: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
        
        return $userData;
    }


    protected function constructRequestUrl($perunId)
    {
        $baseUrl = $this->getOption(self::OPT_BASE_URL);
        if (! $baseUrl) {
            throw new Exception\MissingOptionException(self::OPT_BASE_URL);
        }
        
        $usersHandler = $this->getOption(self::OPT_USERS_HANDLER, 'users/');
        
        return $baseUrl . $usersHandler . $perunId;
    }


    protected function getSecret()
    {
        $secret = $this->getOption(self::OPT_SECRET);
        if (! $secret) {
            throw new Exception\MissingOptionException(self::OPT_SECRET);
        }
        
        return $secret;
    }


    protected function initHttpClient()
    {
        $httpClientConfig = $this->getOption(self::OPT_HTTP_CLIENT_CONFIG, array());
        $httpClient = new Http\Client(null, $httpClientConfig);
        return $httpClient;
    }


    protected function initHttpRequest($requestUrl, $secret)
    {
        $httpRequest = new Http\Request();
        $httpRequest->setUri($requestUrl);
        $httpRequest->setMethod('get');
        $httpRequest->getHeaders()->addHeaders(
            array(
                'Accept' => 'application/json',
                'Authorization' => $secret
            ));
        
        return $httpRequest;
    }


    protected function decodeJsonData($jsonData)
    {
        return Json\Json::decode($jsonData, Json\Json::TYPE_ARRAY);
    }
}