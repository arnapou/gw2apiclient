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

use Arnapou\GW2Api\Core\Curl;
use Arnapou\GW2Api\Environment;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Model\Bag;
use Arnapou\GW2Api\Model\Build;
use Arnapou\GW2Api\Model\Character;
use Arnapou\GW2Api\Model\Item;
use Arnapou\GW2Api\Model\Legend;
use Arnapou\GW2Api\Model\InventorySlot;
use Arnapou\GW2Api\Model\Specialization;
use Arnapou\GW2Api\Model\SpecializationLine;
use Arnapou\GW2Api\Model\SpecializationTrait;

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
     * trait template code:
     *    [*base64].
     *    byte 0     = 't' (0x74).
     *    byte 1     = u16 prof id.
     *    byte 3-8   = u16[3] specialization line1, line2, line3.
     *    byte 9-14  = u16[3] line1adept, line1master, line1grandmaster.
     *    byte 15-20 = u16[3] line2adept, line2master, line2grandmaster.
     *    byte 21-26 = u16[3] line3adept, line3master, line3grandmaster.
     *
     * @param Character $character
     * @param int       $mode
     * @return string
     * @throws Exception
     */
    public function getTraits(Character $character, $mode)
    {
        if (!in_array($mode, ['pve', 'pvp', 'wvw'])) {
            throw new Exception('Mode not supported');
        }
        $build      = $character->getBuild($mode);
        $profession = $character->getProfession(true);
        if (!isset($this->classToId[$profession->getId()])) {
            throw new Exception('Profession not found');
        }
        $professionId = $this->classToId[$profession->getId()];

        $speIds   = [0, 0, 0];
        $traitIds = [0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach ($build->getSpecializations() as $i => $spe) {
            $speIds[$i] = $spe->getId();
        }

        foreach ($build->getSpecializations() as $i => $spe) {
            foreach ($spe->getMajorTraitsSelected() as $j => $trait) {
                $traitIds[$i * 3 + $j] = $trait->getId();
            }
        }

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
