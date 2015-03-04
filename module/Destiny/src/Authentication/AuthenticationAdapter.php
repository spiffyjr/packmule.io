<?php

namespace Destiny\Authentication;

use Destiny\Client\Client;
use GuzzleHttp\Cookie\CookieJar;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class AuthenticationAdapter implements AdapterInterface
{
    const BUNGIE_USER_SIGNIN = 'https://www.bungie.net/en/User/SignIn/%s/';
    const PSN_LOGIN = 'https://auth.api.sonyentertainmentnetwork.com/login.do';
    const XBOX_LOGIN = 'https://login.live.com/login.srf?wa=wsignin1.0&rpsnv=12&ct={ct}&rver=6.1.6206.0&wp=LBI&wreply=https:%2F%2Fwww.bungie.net%2Fen%2FUser%2FSignIn%2FWlid%3Fbru%3D%25252f&lc=1033&id=42917&pcexp=false';

    /** @var string */
    private $identity;
    /** @var string */
    private $credential;
    /** @var string */
    private $platform = 'Psnid';
    /** @var Client */
    private $client;
    /** @var array */
    private $membershipTypeMap = [
        'wlid' => 1, // xbox
        'psnid' => 2 // psn
    ];

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @param string $credential
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    /**
     * @param string $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate()
    {
        if (!$this->identity || !$this->credential) {
            throw new \RuntimeException('Missing identity or credential');
        }

        $jar = new CookieJar();
        $guzzle = $this->client->getGuzzle();

        $result = $guzzle
            ->get(
                sprintf(self::BUNGIE_USER_SIGNIN, $this->platform),
                [
                    'cookies' => $jar
                ]
            )
            ->getBody()
            ->getContents();

        switch(strtolower($this->platform)) {
            case 'psnid':
                $result = $this->authenticatePsn($result, $jar);
                break;
            case 'wlid':
                $result = $this->authenticateXbox($result, $jar);
                break;
            default:
                return new Result(Result::FAILURE, null, ['Invalid platform']);
        }

        if ($result instanceof Result) {
            return $result;
        }

        $identity = $this->createIdentity($jar);

        if (!$identity instanceof DestinyIdentity) {
            return new Result(Result::FAILURE, null, ['Login succeeded but failed to create identity.']);
        }

        return new Result(Result::SUCCESS, $identity);
    }

    private function authenticateXbox($bungieLoginResult, CookieJar $jar)
    {
        $guzzle = $this->client->getGuzzle();

        if (!preg_match('@name="PPFT"[\d\s\w=\-:_."]+value="([\w=!*]+)"@', $bungieLoginResult, $matches)) {
            return new Result(RESULT::FAILURE, null, ['Failed to connect to login service.']);
        }

        $ppft = $matches[1];
        $result = $guzzle
            ->get(str_replace('{ct}', time(), self::XBOX_LOGIN))
            ->getBody()
            ->getContents();

        if (!preg_match("@urlPost:'(https://login.live.com/[^']+)'@", $result, $matches)) {
            return new Result(RESULT::FAILURE, null, ['Failed to get post url.']);
        }

        $result = $guzzle
            ->post(
                $matches[1],
                [
                    'cookies' => $jar,
                    'body' => [
                        'PPFT' => $ppft,
                        'login' => $this->identity,
                        'passwd' => $this->credential
                    ]
                ]
            )
            ->getBody()
            ->getContents();

        if (!preg_match('@action="([^"]+)"@', $result, $matches)) {
            return new Result(RESULT::FAILURE, null, ['Login failed: unknown error']);
        }

        $action = $matches[1];
        preg_match_all('@type="hidden" name="([^"]+)" id="(\1)" value="([^"]+)"@', $result, $matches);

        if (empty($matches[0])) {
            return new Result(RESULT::FAILURE, null, ['Login failed: unknown error']);
        }

        $body = [];
        foreach ($matches[1] as $key => $name) {
            $value = $matches[3][$key];
            $body[$name] = $value;
        }

        $guzzle
            ->post(
                $action,
                [
                    'cookies' => $jar,
                    'body' => $body
                ]
            );

        return $jar;
    }

    private function authenticatePsn($bungieResult, CookieJar $jar)
    {
        $guzzle = $this->client->getGuzzle();

        if (!preg_match('@id="brandingParams"[\s\w="]+value="([\w=]+)"@', $bungieResult, $matches)) {
            return new Result(RESULT::FAILURE, null, ['Failed to connect to login service.']);
        }

        $guzzle
            ->post(
                self::PSN_LOGIN,
                [
                    'cookies' => $jar,
                    'body' => [
                        'params' => $matches[1],
                        'j_username' => $this->identity,
                        'j_password' => $this->credential
                    ]
                ]
            );

        $valid = false;
        foreach ($jar as $cookie) {
            if ($cookie->getName() == 'bunglesony') {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            return new Result(Result::FAILURE, null, ['The information you supplied was incorrect.']);
        }

        return $jar;
    }

    /**
     * @param CookieJar $jar
     * @return DestinyIdentity
     */
    private function createIdentity(CookieJar $jar)
    {
        $this->client->setJar($jar);
        $user = $this->client->getCurrentUser()['Response']['user'];

        $platform = strtolower($this->platform);
        $membershipType = isset($this->membershipTypeMap[$platform]) ? $this->membershipTypeMap[$platform] : null;

        if (null === $membershipType) {
            throw new \RuntimeException('Error determining member platform');
        }

        $ids = array_flip($this->client->getUserMembershipIds()['Response']);
        $membershipId = isset($ids[$membershipType]) ? $ids[$membershipType] : null;

        $identity = new DestinyIdentity();
        $identity->setMembershipType($membershipType);
        $identity->setMembershipId($membershipId);
        $identity->setUniqueName($user['uniqueName']);
        $identity->setDisplayName($user['displayName']);
        $identity->setFirstAccess($user['firstAccess']);
        $identity->setLastUpdate($user['lastUpdate']);
        $identity->setLocale($user['locale']);
        $identity->setProfilePicturePath($user['profilePicturePath']);
        $identity->setUserTitleDisplay($user['userTitleDisplay']);
        $identity->setJar($jar);

        return $identity;
    }
}
