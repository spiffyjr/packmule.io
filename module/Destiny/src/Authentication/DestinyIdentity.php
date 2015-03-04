<?php

namespace Destiny\Authentication;

use DateTime;
use GuzzleHttp\Cookie\CookieJar;

class DestinyIdentity
{
    /** @var int */
    private $membershipType;
    /** @var string */
    private $membershipId;
    /** @var string */
    private $uniqueName;
    /** @var string */
    private $displayName;
    /** @var string */
    private $firstAccess;
    /** @var string */
    private $lastUpdate;
    /** @var string */
    private $locale;
    /** @var string */
    private $profilePicturePath;
    /** @var string */
    private $userTitleDisplay;
    /** @var CookieJar */
    private $jar;

    /**
     * @return string
     */
    public function getUniqueName()
    {
        return $this->uniqueName;
    }

    /**
     * @param string $uniqueName
     */
    public function setUniqueName($uniqueName)
    {
        $this->uniqueName = $uniqueName;
    }

    /**
     * @return int
     */
    public function getMembershipType()
    {
        return $this->membershipType;
    }

    /**
     * @param int $membershipType
     */
    public function setMembershipType($membershipType)
    {
        $this->membershipType = $membershipType;
    }

    /**
     * @return string
     */
    public function getMembershipId()
    {
        return $this->membershipId;
    }

    /**
     * @param string $membershipId
     */
    public function setMembershipId($membershipId)
    {
        $this->membershipId = $membershipId;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getFirstAccess()
    {
        return $this->firstAccess;
    }

    /**
     * @param string $firstAccess
     */
    public function setFirstAccess($firstAccess)
    {
        $this->firstAccess = new DateTime($firstAccess);
    }

    /**
     * @return string
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param string $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = new DateTime($lastUpdate);
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getProfilePicturePath()
    {
        return $this->profilePicturePath;
    }

    /**
     * @param string $profilePicturePath
     */
    public function setProfilePicturePath($profilePicturePath)
    {
        $this->profilePicturePath = $profilePicturePath;
    }

    /**
     * @return string
     */
    public function getUserTitleDisplay()
    {
        return $this->userTitleDisplay;
    }

    /**
     * @param string $userTitleDisplay
     */
    public function setUserTitleDisplay($userTitleDisplay)
    {
        $this->userTitleDisplay = $userTitleDisplay;
    }

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
}
