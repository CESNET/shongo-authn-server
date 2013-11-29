<?php

namespace ShongoAuthn\User\DataConnector;

use Zend\Http;
use Zend\Json;
use ShongoAuthn\User\User;
use InoOicServer\User\Validator\Exception\InvalidUserException;
use InoOicServer\User\DataConnector\AbstractDataConnector;
use InoOicServer\User\UserInterface;


/**
 * Data connector for fetching user information from Perun through a web service.
 * 
 * @todo Implement Perun API calls as a separate object (Perun REST Client).
 */
class PerunWs extends AbstractDataConnector implements ShongoDataConnectorInterface
{

    const OPT_BASE_URL = 'base_url';

    const OPT_CLIENT_ID = 'client_id';

    const OPT_CLIENT_SECRET = 'client_secret';

    const OPT_USERS_RESOURCE = 'users_resource';

    const OPT_PRINCIPAL_RESOURCE = 'principal_resource';

    const OPT_HTTP_CLIENT_CONFIG = 'http_client_config';

    /**
     * @var array
     */
    protected $fieldMap = array(
        'id' => User::FIELD_PERUN_ID,
        'display_name' => User::FIELD_NAME,
        'first_name' => User::FIELD_GIVEN_NAME,
        'last_name' => User::FIELD_FAMILY_NAME,
        'mail' => User::FIELD_EMAIL,
        'phone' => User::FIELD_PHONE_NUMBER,
        'organization' => User::FIELD_ORGANIZATION,
        'language' => User::FIELD_LOCALE,
        'timezone' => User::FIELD_ZONEINFO,
        'principal_names' => User::FIELD_PRINCIPAL_NAMES
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
        $perunUserData = $this->getPerunUserData($user);
        if (null === $perunUserData) {
            return;
        }
        
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


    protected function getPerunUserData(User $user)
    {
        $perunId = $this->getUserPerunIdByPrincipalName($user->getId());
        try {
            $userData = $this->getPerunUserDataByPerunId($perunId);
        } catch (Exception\ErrorServerResponse $e) {
            if (404 === $e->getCode()) {
                $userData = null;
            }
        }
        
        return $userData;
    }


    protected function getUserPerunIdByPrincipalName($principalName)
    {
        $principalResourceName = $this->getOption(self::OPT_PRINCIPAL_RESOURCE, 'principal');
        $requestUrl = $this->constructRequestUrl($principalResourceName, $principalName);
        
        $userData = $this->requestWs($requestUrl);
        if (! isset($userData['id']) || 0 === intval($userData['id'])) {
            throw new Exception\InvalidServerDataException(sprintf("The principal resource request returned no perun ID for principal name '%s'", $principalName));
        }
        
        return $userData['id'];
    }


    protected function getPerunUserDataByPerunId($perunId)
    {
        $usersResourceName = $this->getOption(self::OPT_USERS_RESOURCE, 'users');
        $requestUrl = $this->constructRequestUrl($usersResourceName, $perunId);
        
        $userData = $this->requestWs($requestUrl);
        return $userData;
    }


    protected function requestWs($requestUrl, $clientId = null, $clientSecret = null)
    {
        if (null === $clientId) {
            $clientId = $this->getClientId();
        }
        
        if (null === $clientSecret) {
            $clientSecret = $this->getClientSecret();
        }
        
        $httpClient = $this->initHttpClient();
        $httpRequest = $this->initHttpRequest($requestUrl, $clientId, $clientSecret);
        
        try {
            $httpResponse = $httpClient->send($httpRequest);
        } catch (\Exception $e) {
            throw new Exception\TransportException(sprintf("HTTP client exception: [%s] %s, request URL: %s", get_class($e), $e->getMessage(), $requestUrl), null, $e);
        }
        
        $statusCode = $httpResponse->getStatusCode();
        $rawData = $httpResponse->getContent();
        
        if (200 !== $statusCode) {
            $message = sprintf("Error status code '%s'", $statusCode);
            try {
                $errorData = $this->decodeJsonData($rawData);
                $message .= sprintf(": [%s] %s", $errorData['title'], $errorData['detail']);
            } catch (\Exception $e) {}
            
            throw new Exception\ErrorServerResponse($message, $statusCode);
        }
        
        try {
            $userData = $this->decodeJsonData($rawData);
        } catch (\Exception $e) {
            throw new Exception\InvalidServerDataException(sprintf("Error decoding JSON: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
        
        return $userData;
    }


    protected function constructRequestUrl($resourceName, $resourceId)
    {
        $baseUrl = $this->getOption(self::OPT_BASE_URL);
        if (! $baseUrl) {
            throw new Exception\MissingOptionException(self::OPT_BASE_URL);
        }
        
        return $baseUrl . $resourceName . '/' . $resourceId . '?fresh=1';
    }


    protected function getClientId()
    {
        $id = $this->getOption(self::OPT_CLIENT_ID);
        if (! $id) {
            throw new Exception\MissingOptionException(self::OPT_CLIENT_ID);
        }
        
        return $id;
    }


    protected function getClientSecret()
    {
        $secret = $this->getOption(self::OPT_CLIENT_SECRET);
        if (! $secret) {
            throw new Exception\MissingOptionException(self::OPT_CLIENT_SECRET);
        }
        
        return $secret;
    }


    protected function initHttpClient()
    {
        $httpClientConfig = $this->getOption(self::OPT_HTTP_CLIENT_CONFIG, array());
        $httpClient = new Http\Client(null, $httpClientConfig);
        return $httpClient;
    }


    protected function initHttpRequest($requestUrl, $clientId, $clientSecret)
    {
        $httpRequest = new Http\Request();
        $httpRequest->setUri($requestUrl);
        $httpRequest->setMethod('get');
        $httpRequest->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret")
        ));
        
        return $httpRequest;
    }


    protected function decodeJsonData($jsonData)
    {
        return Json\Json::decode($jsonData, Json\Json::TYPE_ARRAY);
    }
}