<?php

namespace Destiny\Client;

use GuzzleHttp\Cookie\CookieJar;

class Client extends AbstractClient
{
    // Bungie's website API key taken from their request headers on the live site.
    const BUNGIE_API_KEY = '10E792629C2A47E19356B8A79EEFA640';

    const ACCOUNT = 'Platform/Destiny/{membershipType}/Account/{membershipId}/?definitions={definitions}';
    const CHARACTER_COMPLETE = 'Platform/Destiny/{membershipType}/Account/{membershipId}/Character/{characterId}/Complete/';
    const CHARACTER_INVENTORY = 'Platform/Destiny/{membershipType}/Account/{membershipId}/Character/{characterId}/Inventory/?definitions={definitions}';
    const CHARACTER_SUMMARY = 'Platform/Destiny/{membershipType}/Account/{membershipId}/Character/{characterId}/';
    const MY_ACCOUNT_VAULT = 'Platform/Destiny/{membershipType}/MyAccount/Vault/?definitions={definitions}';
    const EQUIP_ITEM = 'Platform/Destiny/EquipItem/';
    const TRANSFER_ITEM = 'Platform/Destiny/TransferItem/';
    const USER_GET_MEMBERSHIP_IDS = 'Platform/User/GetMembershipIds/';
    const USER_GET_BUNGIE_NET_USER= 'Platform/User/GetBungieNetUser/';

    /** @var CookieJar */
    private $jar;

    /**
     * @return CookieJar
     */
    public function getJar()
    {
        return $this->jar;
    }

    /**
     * @param CookieJar $jar
     */
    public function setJar($jar)
    {
        $this->jar = $jar;
    }

    /**
     * @return array
     */
    public function getCurrentUser()
    {
        return $this->getAuthenticated(self::USER_GET_BUNGIE_NET_USER);
    }

    /**
     * @return array
     */
    public function getUserMembershipIds()
    {
        return $this->getAuthenticated(self::USER_GET_MEMBERSHIP_IDS);
    }

    /**
     * @param array $params
     * @return array
     */
    public function equipItem(array $params)
    {
        return $this->postAuthenticated(
            self::EQUIP_ITEM,
            [],
            [
                'headers' => ['content-type' => 'application/json'],
                'body' => json_encode($params)
            ]
        );
    }

    /**
     * @param array $params
     * @return array
     */
    public function transferItem(array $params)
    {
        return $this->postAuthenticated(
            self::TRANSFER_ITEM,
            [],
            [
                'headers' => ['content-type' => 'application/json'],
                'body' => json_encode($params)
            ]
        );
    }

    /**
     * @param string $membershipType
     * @param string $definitions
     * @return array
     */
    public function getAccountVault($membershipType, $definitions = 'false')
    {
        return $this
            ->getAuthenticated(
                self::MY_ACCOUNT_VAULT,
                [
                    'membershipType' => $membershipType,
                    'definitions' => $definitions
                ]
            );
    }

    /**
     * @param int $membershipType
     * @param string $membershipId
     * @param string $definitions
     * @return array
     */
    public function getAccount($membershipType, $membershipId, $definitions = 'false')
    {
        return $this
            ->get(
                self::ACCOUNT,
                [
                    'membershipType' => $membershipType,
                    'membershipId' => $membershipId,
                    'definitions' => $definitions
                ]
            );
    }

    /**
     * @param int $membershipType
     * @param string $membershipId
     * @param string $characterId
     * @param string $definitions
     * @return array
     */
    public function getCharacterInventory($membershipType, $membershipId, $characterId, $definitions = 'false')
    {
        return $this
            ->getAuthenticated(
                self::CHARACTER_INVENTORY,
                [
                    'membershipType' => $membershipType,
                    'membershipId' => $membershipId,
                    'characterId' => $characterId,
                    'definitions' => $definitions
                ]
            );
    }

    /**
     * @param int $membershipType
     * @param string $membershipId
     * @param string $characterId
     * @return array
     */
    public function getCharacterComplete($membershipType, $membershipId, $characterId)
    {
        return $this
            ->getAuthenticated(
                self::CHARACTER_COMPLETE,
                [
                    'membershipType' => $membershipType,
                    'membershipId' => $membershipId,
                    'characterId' => $characterId
                ]
            );
    }

    /**
     * @param int $membershipType
     * @param string $membershipId
     * @param string $characterId
     * @return array
     */
    public function getCharacterSummary($membershipType, $membershipId, $characterId)
    {
        return $this
            ->get(
                self::CHARACTER_SUMMARY,
                [
                    'membershipType' => $membershipType,
                    'membershipId' => $membershipId,
                    'characterId' => $characterId
                ]
            );
    }

    /**
     * @param string $method
     * @param string|null $url
     * @param array $options
     * @return \GuzzleHttp\Message\Request|\GuzzleHttp\Message\RequestInterface
     */
    private function createAuthenticatedRequest($method, $url = null, array $options = [])
    {
        /** @var \GuzzleHttp\Cookie\SetCookie $cookie */
        $csrf = null;
        foreach ($this->jar as $cookie) {
            if ($cookie->getName() == 'bungled') {
                $csrf = $cookie->getValue();
                break;
            }
        }

        if (null === $csrf) {
            throw new \RuntimeException('Unable to find CSRF: invalid cookie jar');
        }

        $options = array_merge_recursive($options, [
            'cookies' => $this->jar,
            'headers' => [
                'X-API-KEY' => self::BUNGIE_API_KEY,
                'X-CSRF' => $csrf,
                'X-REQUESTED-WITH' => 'XMLHttpRequest'
            ]
        ]);

        return $this->getGuzzle()->createRequest($method, $url, $options);
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param array $options
     * @return array
     */
    private function get($endpoint, array $params = [], array $options = [])
    {
        $uri = $this->createUriFromEndpoint($endpoint, $params);
        return $this->getGuzzle()->get($uri, $options)->json();
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param array $options
     * @return array
     */
    private function getAuthenticated($endpoint, array $params = [], array $options = [])
    {
        $uri = $this->createUriFromEndpoint($endpoint, $params);
        $request = $this->createAuthenticatedRequest('GET', $uri, $options);
        return $this->getGuzzle()->send($request)->json();
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param array $options
     * @return array
     */
    private function postAuthenticated($endpoint, array $params = [], array $options = [])
    {
        $uri = $this->createUriFromEndpoint($endpoint, $params);
        $request = $this->createAuthenticatedRequest('POST', $uri, $options);
        return $this->getGuzzle()->send($request)->json();
    }
}
