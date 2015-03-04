<?php

namespace Destiny\Common;

abstract class Constants
{
    const PLATFORM_XBOX = 1;
    const PLATFORM_PLAYSTATION = 2;

    const MATCH_MODE_STORY = 2;
    const MATCH_MODE_STRIKE = 3;
    const MATCH_MODE_RAID = 4;
    const MATCH_MODE_ALL_PVP = 5;
    const MATCH_MODE_PATROL = 6;
    const MATCH_MODE_ALL_PVE = 7;
    const MATCH_MODE_THREE_VS_THREE = 9;
    const MATCH_MODE_CONTROL = 10;
    const MATCH_MODE_TEAM = 12;
    const MATCH_MODE_FREE_FOR_ALL = 13;

    const STATS_PERIOD_DAILY = 1;
    const STATS_PERIOD_MONTHLY = 2;

    const CLASS_HUNTER = '671679327';
    const CLASS_TITAN = '3655393761';
    const CLASS_WARLOCK = '2271682572';

    const GENDER_MALE = '3111576190';
    const GENDER_FEMALE = '2204441813';

    const RACE_AWOKEN = '2803282938';
    const RACE_EXO = '898834093';
    const RACE_HUMAN = '3887404748';

    private static $platformHashMap = [
        self::PLATFORM_XBOX => 'xbox',
        self::PLATFORM_PLAYSTATION => 'playstation'
    ];
    /** @var array */
    private static $classHashMap = [
        self::CLASS_HUNTER => 'Hunter',
        self::CLASS_TITAN => 'Titan',
        self::CLASS_WARLOCK => 'Warlock'
    ];
    /** @var array */
    private static $genderHashMap = [
        self::GENDER_FEMALE => 'Female',
        self::GENDER_MALE => 'Male',
    ];
    /** @var array */
    private static $raceHashMap = [
        self::RACE_AWOKEN => 'Awoken',
        self::RACE_EXO => 'Exo',
        self::RACE_HUMAN => 'Human'
    ];
    /** @var array */
    private static $modeToNameMap = [
        'control' => 'Control',
        'strike' => 'Strike',
        'team' => 'Clash',
        'freeForAll' => 'Rumble',
        'threeVsThree' => 'Skirmish',
    ];

    /**
     * @return array
     */
    public static function getModeNameMap()
    {
        return self::$modeToNameMap;
    }

    /**
     * @param string $hash
     * @return string
     */
    public static function convertPlatformToName($hash)
    {
        return self::$platformHashMap[(string) $hash];
    }

    /**
     * @param string $mode
     * @return string
     */
    public static function convertModeToName($mode)
    {
        return self::$modeToNameMap[$mode];
    }

    /**
     * @param string $hash
     * @return string
     */
    public static function convertClassHash($hash)
    {
        return self::$classHashMap[(string) $hash];
    }

    /**
     * @param string $hash
     * @return string
     */
    public static function convertGenderHash($hash)
    {
        return self::$genderHashMap[(string) $hash];
    }

    /**
     * @param string $hash
     * @return string
     */
    public static function convertRaceHash($hash)
    {
        return self::$raceHashMap[(string) $hash];
    }

    /**
     * @param string $stat
     * @return string
     */
    public static function transformLeaderboardStat($stat)
    {
        $callback = function ($matches) {
            return $matches[1] . ' ' . $matches[2];
        };

        return ucfirst(preg_replace_callback('@([a-z])([A-Z])@', $callback, $stat));
    }
}
