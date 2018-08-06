<?php
/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\DeltaConnected;

use Arnapou\GW2Api\Environment;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Model\Character;
use Arnapou\GW2Api\Model\Specialization;

/**
 *
 * @doc https://www.deltaconnected.com/arcdps/x64/buildtemplates/README.txt
 */
class BuildTemplate
{
    /**
     *
     * @var Environment
     */
    protected $environment;

    /**
     * @var array
     */
    protected $classToId = [
        Character::PROFESSION_GUARDIAN     => 1,
        Character::PROFESSION_WARRIOR      => 2,
        Character::PROFESSION_ENGINEER     => 3,
        Character::PROFESSION_RANGER       => 4,
        Character::PROFESSION_THIEF        => 5,
        Character::PROFESSION_ELEMENTALIST => 6,
        Character::PROFESSION_MESMER       => 7,
        Character::PROFESSION_NECROMANCER  => 8,
        Character::PROFESSION_REVENANT     => 9,
    ];

    /**
     * BuildTemplate constructor.
     * @param Environment $env
     */
    public function __construct(Environment $env)
    {
        $this->environment = $env;
    }

    /**
     *
     * @param Character $character
     * @param int       $mode
     * @return string
     * @throws Exception
     */
    public function getTraitsFromCharacter(Character $character, $mode)
    {
        if (!in_array($mode, ['pve', 'pvp', 'wvw'])) {
            throw new Exception('Mode not supported');
        }

        $build      = $character->getBuild($mode);
        $speIds     = [0, 0, 0];
        $traitIds   = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        $profession = $character->getProfession(true)->getId();

        if ($build) {
            foreach ($build->getSpecializations() as $i => $spe) {
                $speIds[$i] = $spe->getId();
            }

            foreach ($build->getSpecializations() as $i => $spe) {
                foreach ($spe->getMajorTraitsSelected() as $j => $trait) {
                    $traitIds[$i * 3 + $j] = $trait->getId();
                }
            }
        }

        return $this->getTraits($profession, $speIds, $traitIds);
    }

    /**
     * trait template code:
     *    [*base64].
     *    byte 0     = 't' (0x74).
     *    byte 1     = u16 prof id.
     *    byte 3-8   = u16[3] specialization line1, line2, line3.
     *    byte 9-14  = u16[3] line1adept, line1master, line1grandmaster.
     *    byte 15-20 = u16[3] line2adept, line2master, line2grandmaster.
     *    byte 21-26 = u16[3] line3adept, line3master, line3grandmaster.
     *
     * @param string $profession
     * @param array  $speIds
     * @param array  $traitIds
     * @return string
     * @throws Exception
     */
    public function getTraits($profession, array $speIds, array $traitIds)
    {
        if (!isset($this->classToId[$profession])) {
            throw new Exception('Profession not found');
        }
        if (count($speIds) !== 3) {
            throw new Exception('Param $speIds should be an array int[3]');
        }
        if (count($traitIds) !== 9) {
            throw new Exception('Param $traitIds should be an array int[9]');
        }
        $professionId = $this->classToId[$profession];

        $speIds   = array_map('intval', $speIds);
        $traitIds = array_map('intval', $traitIds);

        $hex = '74' . $this->dechex($professionId, 2);
        foreach ($speIds as $id) {
            $hex .= $this->dechex($id, 2);
        }
        foreach ($traitIds as $id) {
            $hex .= $this->dechex($id, 2);
        }

        $base64 = base64_encode(pack('H*', $hex));
        return "[*$base64]";
    }

    /**
     *
     * @param Character $character
     * @param int       $mode
     * @return string
     * @throws Exception
     */
    public function getSkillsFromCharacter(Character $character, $mode)
    {
        if (!in_array($mode, ['pve', 'pvp', 'wvw'])) {
            throw new Exception('Mode not supported');
        }

        $build      = $character->getBuild($mode);
        $landIds    = [0, 0, 0, 0, 0];
        $waterIds   = [0, 0, 0, 0, 0];
        $profession = $character->getProfession(true)->getId();

        if ($build) {
            foreach ([6, 7, 8, 9, 0] as $index => $number) {
                $skill = $build->getSkill($number);
                if ($skill) {
                    $landIds[$index] = $skill->getId();
                }
            }
        }

        return $this->getSkills($profession, $landIds, $waterIds);
    }

    /**
     * skill template code:
     *    [*base64].
     *    byte 0     = 's' (0x73). to use skilldef ids (from web api), replace byte0 with 'k' (0x6b) - experimental.
     *    byte 1     = u16 prof id.
     *    byte 3-12  = u16[5] landheal, landutil1, landutil2, landutil3, landelite
     *    byte 13-22 = u16[5] waterheal, waterutil1, wateruril2, waterutil3, waterelite
     *
     * @param string $profession
     * @param array  $landIds
     * @param array  $waterIds
     * @return string
     * @throws Exception
     */
    public function getSkills($profession, array $landIds, array $waterIds)
    {
        if (!isset($this->classToId[$profession])) {
            throw new Exception('Profession not found');
        }
        if (count($landIds) !== 5) {
            throw new Exception('Param $landIds should be an array int[5]');
        }
        if (count($waterIds) !== 5) {
            throw new Exception('Param $waterIds should be an array int[5]');
        }
        $professionId = $this->classToId[$profession];

        $landIds  = array_map('intval', $landIds);
        $waterIds = array_map('intval', $waterIds);

        $hex = '6b' . $this->dechex($professionId, 2);
        foreach ($landIds as $id) {
            $hex .= $this->dechex($id, 2);
        }
        foreach ($waterIds as $id) {
            $hex .= $this->dechex($id, 2);
        }

        $base64 = base64_encode(pack('H*', $hex));
        return "[*$base64]";
    }

    /**
     * @param $int
     * @param $octets
     * @return string
     */
    private function dechex($int, $octets)
    {
        $hex    = dechex($int);
        $length = 2 * $octets;
        $nbchar = strlen($hex);
        if ($nbchar < $length) {
            $hex = str_repeat('0', $length - $nbchar) . $hex;
        }
        $reversed = '';
        for ($i = 0; $i < $octets; $i++) {
            $reversed = $hex[2 * $i] . $hex[2 * $i + 1] . $reversed;
        }
        return $reversed;
    }
}
