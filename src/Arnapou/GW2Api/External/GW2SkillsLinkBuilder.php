<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\External;

use Arnapou\GW2Api\Core\Curl;
use Arnapou\GW2Api\Model\Character;
use Arnapou\GW2Api\Model\Item;
use Arnapou\GW2Api\Model\InventorySlot;

class GW2SkillsLinkBuilder {

    protected $mapStatsWeapons  = [
        "Exotic.Berserker's"    => 77, // berserker
        "Exotic.Soldier's"      => 78, // soldier
        "Exotic.Valkyrie"       => 79, // valkyrie
        "Exotic.Rampager's"     => 80, // rampager
        "Exotic.Knight's"       => 81, // knight
        "Exotic.Settler's"      => 82, // settler
        "Exotic.Shaman's"       => 83, // shaman
        "Exotic.Carrion"        => 84, // carrion
        "Exotic.Rabid"          => 85, // rabid
        "Exotic.Giver's"        => 86, // giver
        "Exotic.Cleric's"       => 87, // cleric
        "Exotic.Magi's"         => 88, // magi
        "Exotic.Apothecary's"   => 89, // apothecary
        "Exotic.Assassin's"     => 90, // assassin
        "Exotic.Dire"           => 91, // dire
        "Exotic.Zealot's"       => 92, // zealot
        "Exotic.Nomad's"        => 93, // nomad
        "Exotic.Sinister"       => 94, // sinister
        "Exotic.Sentinel's"     => 95, // sentinel
        "Exotic.Celestial"      => 96, // celestial
        "Exotic.Cavalier's"     => 97, // cavalier
        "Ascended.Berserker's"  => 98, // berserker
        "Ascended.Soldier's"    => 99, // soldier
        "Ascended.Valkyrie"     => 100, // valkyrie
        "Ascended.Rampager's"   => 101, // rampager
        "Ascended.Knight's"     => 102, // knight
        "Ascended.Settler's"    => 103, // settler
        "Ascended.Shaman's"     => 104, // shaman
        "Ascended.Carrion"      => 105, // carrion
        "Ascended.Rabid"        => 106, // rabid
        "Ascended.Giver's"      => 107, // giver
        "Ascended.Cleric's"     => 108, // cleric
        "Ascended.Magi's"       => 109, // magi
        "Ascended.Apothecary's" => 110, // apothecary
        "Ascended.Assassin's"   => 111, // assassin
        "Ascended.Dire"         => 112, // dire
        "Ascended.Zealot's"     => 113, // zealot
        "Ascended.Nomad's"      => 114, // nomad
        "Ascended.Sinister"     => 115, // sinister
        "Ascended.Sentinel's"   => 116, // sentinel
        "Ascended.Celestial"    => 117, // celestial
        "Ascended.Cavalier's"   => 118, // cavalier
    ];
    protected $mapStatsTrinkets = [
        "Exotic.Berserker's"              => 160, // berserker
        "Exotic.Soldier's"                => 161, // soldier
        "Exotic.Valkyrie"                 => 162, // valkyrie
        "Exotic.Rampager's"               => 163, // rampager
        "Exotic.Knight's"                 => 164, // knight
        "Exotic.Cavalier's"               => 165, // cavalier
        "Exotic.Settler's"                => 166, // settler
        "Exotic.Shaman's"                 => 167, // shaman
        "Exotic.Sentinel's"               => 168, // sentinel
        "Exotic.Carrion"                  => 169, // carrion
        "Exotic.Rabid"                    => 170, // rabid
        "Exotic.Giver's"                  => 171, // giver
        "Exotic.Cleric's"                 => 172, // cleric
        "Exotic.Magi's"                   => 173, // magi
        "Exotic.Apothecary's"             => 174, // apothecary
        "Exotic.Assassin's"               => 175, // assassin
        "Exotic.Zealot's"                 => 176, // zealot
        "Exotic.Nomad's"                  => 177, // nomad
        "Exotic.Sinister"                 => 178, // sinister
        "Exotic.Dire"                     => 179, // dire
        "Ascended.Berserker's"            => 184, // berserker
        "Ascended.Berserker's + Valkyrie" => 185, // bers_valk
        "Ascended.Soldier's"              => 186, // soldier
        "Ascended.Captain's"              => 187, // captain
        "Ascended.Rampager's"             => 188, // rampager
        "Ascended.Knight's"               => 189, // knight
        "Ascended.Cavalier's"             => 190, // cavalier
        "Ascended.Rabid"                  => 191, // rabid
        "Ascended.Zealot's"               => 192, // zealot
        "Ascended.Dire + Rabid"           => 193, // alter_rabid
        "Ascended.Rabid + Apothecary's"   => 194, // rabid_apo
        "Ascended.Cleric's"               => 195, // cleric
        "Ascended.Apothecary's"           => 196, // apothecary
        "Ascended.Assassin's"             => 198, // assassin
        "Ascended.Celestial"              => 199, // celestial
        "Ascended.Sinister"               => 200, // sinister
        "Ascended.Nomad's"                => 201, // nomad
        "Ascended.Sentinel's"             => 202, // sentinel
        "Ascended.Carrion"                => 203, // carrion
        "Ascended.Magi's"                 => 204, // magi
    ];
    protected $mapStatsArmors   = [
        "Exotic.Berserker's"    => 119, // berserker
        "Exotic.Soldier's"      => 120, // soldier
        "Exotic.Valkyrie"       => 121, // valkyrie
        "Exotic.Rampager's"     => 122, // rampager
        "Exotic.Knight's"       => 123, // knight
        "Exotic.Settler's"      => 124, // settler
        "Exotic.Shaman's"       => 125, // shaman
        "Exotic.Sentinel's"     => 126, // sentinel
        "Exotic.Carrion"        => 127, // carrion
        "Exotic.Rabid"          => 128, // rabid
        "Exotic.Giver's"        => 129, // giver
        "Exotic.Cavalier's"     => 130, // cavalier
        "Exotic.Cleric's"       => 131, // cleric
        "Exotic.Magi's"         => 132, // magi
        "Exotic.Apothecary's"   => 133, // apothecary
        "Exotic.Assassin's"     => 134, // assassin
        "Exotic.Zealot's"       => 135, // zealot
        "Exotic.Nomad's"        => 136, // nomad
        "Exotic.Sinister"       => 137, // sinister
        "Exotic.Celestial"      => 138, // celestial
        "Exotic.Dire"           => 139, // dire
        "Ascended.Berserker's"  => 140, // berserker
        "Ascended.Soldier's"    => 141, // soldier
        "Ascended.Valkyrie"     => 142, // valkyrie
        "Ascended.Rampager's"   => 143, // rampager
        "Ascended.Knight's"     => 144, // knight
        "Ascended.Settler's"    => 145, // settler
        "Ascended.Shaman's"     => 146, // shaman
        "Ascended.Sentinel's"   => 147, // sentinel
        "Ascended.Carrion"      => 148, // carrion
        "Ascended.Rabid"        => 149, // rabid
        "Ascended.Cavalier's"   => 150, // cavalier
        "Ascended.Cleric's"     => 151, // cleric
        "Ascended.Magi's"       => 152, // magi
        "Ascended.Apothecary's" => 153, // apothecary
        "Ascended.Assassin's"   => 154, // assassin
        "Ascended.Zealot's"     => 155, // zealot
        "Ascended.Nomad's"      => 156, // nomad
        "Ascended.Sinister"     => 157, // sinister
        "Ascended.Celestial"    => 158, // celestial
        "Ascended.Dire"         => 159, // dire
    ];
    protected $mapUpgrades      = [
        24524 => 1, // Rare: Crest of the Soldier
        24533 => 2, // Rare: Crest of the Shaman
        24532 => 3, // Rare: Crest of the Rabid
        24518 => 4, // Rare: Crest of the Magi
        24514 => 5, // Rare: Crest of the Assassin
        24508 => 6, // Rare: Ruby Orb
        24520 => 7, // Rare: Beryl Orb
        24515 => 8, // Rare: Emerald Orb
        24510 => 9, // Rare: Coral Orb
        24512 => 10, // Rare: Chrysocola Orb
        24516 => 11, // Rare: Sapphire Orb
        24522 => 12, // Rare: Opal Orb
        42010 => 14, // Rare: Azurite Orb
        24498 => 15, // Exotic: Exquisite Ruby Jewel
        24543 => 16, // Exotic: Exquisite Beryl Jewel
        24497 => 17, // Exotic: Exquisite Emerald Jewel
        24544 => 18, // Exotic: Exquisite Coral Jewel
        24496 => 19, // Exotic: Exquisite Chrysocola Jewel
        24499 => 20, // Exotic: Exquisite Sapphire Jewel
        24545 => 21, // Exotic: Exquisite Opal Jewel
        42008 => 22, // Exotic: Exquisite Azurite Jewel
        38143 => 23, // Exotic: Exquisite Snowflake
        43866 => 24, // Exotic: Exquisite Charged Quartz Jewel
        49823 => 25, // Exotic: Exquisite Watchwork Sprocket
        66671 => 26, // Exotic: Exquisite Ambrite Jewel
        67914 => 27, // Exotic: Exquisite Charged Ambrite Jewel
        37906 => 28, // Exotic: Exquisite Passion Flower
        24618 => 4, // Exotic: Superior Sigil of Accuracy
        24612 => 1, // Exotic: Superior Sigil of Agony
        24554 => 26, // Exotic: Superior Sigil of Air
        24601 => 40, // Exotic: Superior Sigil of Battle
        67913 => 48, // Exotic: Superior Sigil of Blight
        24570 => 30, // Exotic: Superior Sigil of Blood
        24575 => 19, // Exotic: Superior Sigil of Bloodlust
        44944 => 7, // Exotic: Superior Sigil of Bursting
        24630 => 9, // Exotic: Superior Sigil of Chilling
        67340 => 46, // Exotic: Superior Sigil of Cleansing
        24865 => 24, // Exotic: Superior Sigil of Celerity
        24578 => 14, // Exotic: Superior Sigil of Corruption
        67341 => 47, // Exotic: Superior Sigil of Cruelty
        24636 => 13, // Exotic: Superior Sigil of Debility
        24583 => 23, // Exotic: Superior Sigil of Demon Summoning
        24609 => 35, // Exotic: Superior Sigil of Doom
        24560 => 27, // Exotic: Superior Sigil of Earth
        24607 => 37, // Exotic: Superior Sigil of Energy
        24548 => 28, // Exotic: Superior Sigil of Fire
        24615 => 5, // Exotic: Superior Sigil of Force
        24567 => 32, // Exotic: Superior Sigil of Frailty
        38294 => 43, // Exotic: Superior Sigil of Generosity
        24605 => 36, // Exotic: Superior Sigil of Geomancy
        24627 => 3, // Exotic: Superior Sigil of Hobbling
        24597 => 38, // Exotic: Superior Sigil of Hydromancy
        24555 => 29, // Exotic: Superior Sigil of Ice
        24868 => 44, // Exotic: Superior Sigil of Impact
        67343 => 50, // Exotic: Superior Sigil of Incapacitation
        24600 => 34, // Exotic: Superior Sigil of Intelligence
        24599 => 39, // Exotic: Superior Sigil of Leeching
        24582 => 16, // Exotic: Superior Sigil of Life
        24591 => 22, // Exotic: Superior Sigil of Luck
        44950 => 8, // Exotic: Superior Sigil of Malice
        68436 => 49, // Exotic: Superior Sigil of Mischief
        49457 => 21, // Exotic: Superior Sigil of Momentum
        36053 => 6, // Exotic: Superior Sigil of the Night
        24572 => 51, // Exotic: Superior Sigil of Nullification
        24639 => 11, // Exotic: Superior Sigil of Paralyzation
        24580 => 20, // Exotic: Superior Sigil of Perception
        24621 => 12, // Exotic: Superior Sigil of Peril
        24571 => 52, // Exotic: Superior Sigil of Purity
        24561 => 42, // Exotic: Superior Sigil of Rage
        44947 => 41, // Exotic: Superior Sigil of Renewal
        24594 => 17, // Exotic: Superior Sigil of Restoration
        24584 => 18, // Exotic: Superior Sigil of Benevolence
        24624 => 2, // Exotic: Superior Sigil of Smoldering
        24589 => 25, // Exotic: Superior Sigil of Speed
        24592 => 15, // Exotic: Superior Sigil of Stamina
        24562 => 31, // Exotic: Superior Sigil of Strength
        48911 => 45, // Exotic: Superior Sigil of Torment
        24632 => 10, // Exotic: Superior Sigil of Venom
        24551 => 33, // Exotic: Superior Sigil of Water
        24830 => 105, // Exotic: Superior Rune of the Adventurer
        24687 => 63, // Exotic: Superior Rune of the Afflicted
        24750 => 64, // Exotic: Superior Rune of the Air
        38206 => 120, // Exotic: Superior Rune of Altruism
        48907 => 132, // Exotic: Superior Rune of Antitoxin
        24854 => 112, // Exotic: Superior Rune of the Baelfire
        24765 => 65, // Exotic: Superior Rune of Balthazar
        24788 => 66, // Exotic: Superior Rune of the Centaur
        24741 => 67, // Exotic: Superior Rune of the Citadel
        67912 => 131, // Exotic: Superior Rune of the Defender
        24732 => 68, // Exotic: Superior Rune of Divinity
        24699 => 69, // Exotic: Superior Rune of the Dolyak
        24768 => 70, // Exotic: Superior Rune of Dwayna
        24723 => 71, // Exotic: Superior Rune of the Eagle
        24744 => 74, // Exotic: Superior Rune of the Earth
        24800 => 100, // Exotic: Superior Rune of the Elementalist
        24812 => 98, // Exotic: Superior Rune of the Engineer
        67344 => 127, // Exotic: Superior Rune of Evasion
        44951 => 115, // Exotic: Superior Rune of Exuberance
        24833 => 106, // Exotic: Superior Rune of the Brawler
        24747 => 73, // Exotic: Superior Rune of the Fire
        24797 => 75, // Exotic: Superior Rune of the Flame Legion
        24696 => 76, // Exotic: Superior Rune of the Flock
        24851 => 111, // Exotic: Superior Rune of the Forgeman
        24785 => 124, // Exotic: Superior Rune of the Golemancer
        24779 => 78, // Exotic: Superior Rune of Grenth
        24735 => 79, // Exotic: Superior Rune of the Grove
        24824 => 103, // Exotic: Superior Rune of the Guardian
        24729 => 80, // Exotic: Superior Rune of Hoelbrak
        24753 => 81, // Exotic: Superior Rune of the Ice
        24703 => 82, // Exotic: Superior Rune of Infiltration
        24762 => 77, // Exotic: Superior Rune of the Krait
        24688 => 83, // Exotic: Superior Rune of the Lich
        24776 => 84, // Exotic: Superior Rune of Lyssa
        36044 => 118, // Exotic: Superior Rune of the Mad King
        24771 => 85, // Exotic: Superior Rune of Melandru
        24708 => 86, // Exotic: Superior Rune of Mercy
        24803 => 97, // Exotic: Superior Rune of the Mesmer
        24842 => 109, // Exotic: Superior Rune of the Monk
        24806 => 96, // Exotic: Superior Rune of the Necromancer
        24848 => 110, // Exotic: Superior Rune of the Nightmare
        24845 => 121, // Exotic: Superior Rune of the Aristocracy
        24756 => 87, // Exotic: Superior Rune of the Ogre
        24860 => 114, // Exotic: Superior Rune of Orr
        24702 => 88, // Exotic: Superior Rune of the Pack
        44957 => 116, // Exotic: Superior Rune of Perplexity
        24782 => 123, // Exotic: Superior Rune of the Privateer
        67342 => 129, // Exotic: Superior Rune of Radiance
        24717 => 89, // Exotic: Superior Rune of Rage
        24815 => 99, // Exotic: Superior Rune of the Ranger
        24726 => 90, // Exotic: Superior Rune of the Rata Sum
        49460 => 126, // Exotic: Superior Rune of Resistance
        24857 => 113, // Exotic: Superior Rune of Sanctuary
        24738 => 119, // Exotic: Superior Rune of Scavenging
        24836 => 107, // Exotic: Superior Rune of the Scholar
        68437 => 130, // Exotic: Superior Rune of Snowfall
        24827 => 104, // Exotic: Superior Rune of the Trooper
        24720 => 91, // Exotic: Superior Rune of Speed
        24714 => 72, // Exotic: Superior Rune of Strength
        47908 => 125, // Exotic: Superior Rune of the Sunless
        24794 => 95, // Exotic: Superior Rune of Svanir
        24818 => 101, // Exotic: Superior Rune of the Thief
        44956 => 117, // Exotic: Superior Rune of Tormenting
        67339 => 128, // Exotic: Superior Rune of the Trapper
        24691 => 122, // Exotic: Superior Rune of the Traveler
        24757 => 92, // Exotic: Superior Rune of the Undead
        24711 => 93, // Exotic: Superior Rune of Vampirism
        24821 => 102, // Exotic: Superior Rune of the Warrior
        24839 => 108, // Exotic: Superior Rune of the Water
        24791 => 94, // Exotic: Superior Rune of the Wurm
        24829 => 225, // Rare: Major Rune of the Adventurer
        24686 => 183, // Rare: Major Rune of the Afflicted
        24749 => 184, // Rare: Major Rune of the Air
        38205 => 240, // Rare: Major Rune of Altruism
        24853 => 232, // Rare: Major Rune of the Baelfire
        24764 => 185, // Rare: Major Rune of Balthazar
        24787 => 186, // Rare: Major Rune of the Centaur
        24740 => 187, // Rare: Major Rune of the Citadel
        24731 => 188, // Rare: Major Rune of Divinity
        24698 => 189, // Rare: Major Rune of the Dolyak
        24767 => 190, // Rare: Major Rune of Dwayna
        24722 => 191, // Rare: Major Rune of the Eagle
        24743 => 194, // Rare: Major Rune of the Earth
        24799 => 220, // Rare: Major Rune of the Elementalist
        24811 => 218, // Rare: Major Rune of the Engineer
        44952 => 235, // Rare: Major Rune of Exuberance
        24832 => 226, // Rare: Major Rune of the Brawler
        24746 => 193, // Rare: Major Rune of the Fire
        24796 => 195, // Rare: Major Rune of the Flame Legion
        24695 => 196, // Rare: Major Rune of the Flock
        24850 => 231, // Rare: Major Rune of the Forgeman
        24784 => 244, // Rare: Major Rune of the Golemancer
        24778 => 198, // Rare: Major Rune of Grenth
        24734 => 199, // Rare: Major Rune of the Grove
        24823 => 223, // Rare: Major Rune of the Guardian
        24728 => 200, // Rare: Major Rune of Hoelbrak
        24752 => 201, // Rare: Major Rune of the Ice
        24704 => 202, // Rare: Major Rune of Infiltration
        24761 => 197, // Rare: Major Rune of the Krait
        24690 => 203, // Rare: Major Rune of the Lich
        24775 => 204, // Rare: Major Rune of Lyssa
        36043 => 238, // Rare: Major Rune of the Mad King
        24770 => 205, // Rare: Major Rune of Melandru
        24707 => 206, // Rare: Major Rune of Mercy
        24802 => 217, // Rare: Major Rune of the Mesmer
        24841 => 229, // Rare: Major Rune of the Monk
        24805 => 216, // Rare: Major Rune of the Necromancer
        24847 => 230, // Rare: Major Rune of the Nightmare
        24844 => 241, // Rare: Major Rune of the Aristocracy
        24755 => 207, // Rare: Major Rune of the Ogre
        24859 => 234, // Rare: Major Rune of Orr
        24701 => 208, // Rare: Major Rune of the Pack
        44958 => 236, // Rare: Major Rune of Perplexity
        24781 => 243, // Rare: Major Rune of the Privateer
        24716 => 209, // Rare: Major Rune of Rage
        24814 => 219, // Rare: Major Rune of the Ranger
        // not found UpDB[210]=["rune","",4,"the Rata Sum","the Rata Sum","major/rata_sum",6,"","",2]
        24856 => 233, // Rare: Major Rune of Sanctuary
        24737 => 239, // Rare: Major Rune of Scavenging
        24835 => 227, // Rare: Major Rune of the Scholar
        68435 => 245, // Rare: Major Rune of Snowfall
        24826 => 224, // Rare: Major Rune of the Trooper
        24719 => 211, // Rare: Major Rune of Speed
        24713 => 192, // Rare: Major Rune of Strength
        24793 => 215, // Rare: Major Rune of Svanir
        24817 => 221, // Rare: Major Rune of the Thief
        44955 => 237, // Rare: Major Rune of Tormenting
        24692 => 242, // Rare: Major Rune of the Traveler
        24758 => 212, // Rare: Major Rune of the Undead
        24710 => 213, // Rare: Major Rune of Vampirism
        24820 => 222, // Rare: Major Rune of the Warrior
        24838 => 228, // Rare: Major Rune of the Water
        24790 => 214, // Rare: Major Rune of the Wurm
        39330 => 170, // Fine: Experienced Infusion
        39331 => 168, // Fine: Gilded Infusion
        37123 => 162, // Fine: Healing Infusion
        43250 => 165, // Fine: Healing WvW Infusion
        39332 => 171, // Fine: Karmic Infusion
        39333 => 169, // Fine: Magical Infusion
        37129 => 153, // Fine: Malign Infusion
        43253 => 156, // Fine: Malign WvW Infusion
        37127 => 154, // Fine: Mighty Infusion
        43254 => 157, // Fine: Mighty WvW Infusion
        37128 => 155, // Fine: Precise Infusion
        43255 => 158, // Fine: Precise WvW Infusion
        37133 => 163, // Fine: Resilient Infusion
        43251 => 166, // Fine: Resilient WvW Infusion
        37125 => 176, // Fine: Versatile Healing Infusion
        37130 => 173, // Fine: Versatile Malign Infusion
        37131 => 174, // Fine: Versatile Mighty Infusion
        37132 => 175, // Fine: Versatile Precise Infusion
        37135 => 177, // Fine: Versatile Resilient Infusion
        37136 => 178, // Fine: Versatile Vital Infusion
        37134 => 164, // Fine: Vital Infusion
        43252 => 167, // Fine: Vital WvW Infusion
        39340 => 159, // Basic: Healing Infusion
        39337 => 150, // Basic: Malign Infusion
        39336 => 151, // Basic: Mighty Infusion
        39335 => 152, // Basic: Precise Infusion
        39339 => 160, // Basic: Resilient Infusion
        37138 => 172, // Basic: Versatile Simple Infusion
        39338 => 161, // Basic: Vital Infusion
    ];
    protected $mapRaces         = [
        Character::RACE_HUMAN   => '1',
        Character::RACE_CHARR   => '2',
        Character::RACE_NORN    => '3',
        Character::RACE_ASURA   => '4',
        Character::RACE_SYLVARI => '5',
    ];
    protected $mapProfessions   = [
        Character::PROFESSION_ELEMENTALIST => '1',
        Character::PROFESSION_WARRIOR      => '2',
        Character::PROFESSION_RANGER       => '3',
        Character::PROFESSION_NECROMANCER  => '4',
        Character::PROFESSION_GUARDIAN     => '5',
        Character::PROFESSION_THIEF        => '6',
        Character::PROFESSION_ENGINEER     => '7',
        Character::PROFESSION_MESMER       => '8',
        Character::PROFESSION_REVENANT     => '9',
    ];
    protected $mapWeapons       = [
        Item::SUBTYPE_WEAPON_AXE        => '7',
        Item::SUBTYPE_WEAPON_DAGGER     => '8',
        Item::SUBTYPE_WEAPON_MACE       => '9',
        Item::SUBTYPE_WEAPON_PISTOL     => '10',
        Item::SUBTYPE_WEAPON_SCEPTER    => '11',
        Item::SUBTYPE_WEAPON_SWORD      => '12',
        Item::SUBTYPE_WEAPON_FOCUS      => '13',
        Item::SUBTYPE_WEAPON_SHIELD     => '14',
        Item::SUBTYPE_WEAPON_TORCH      => '15',
        Item::SUBTYPE_WEAPON_WARHORN    => '16',
        Item::SUBTYPE_WEAPON_GREATSWORD => '1',
        Item::SUBTYPE_WEAPON_HAMMER     => '2',
        Item::SUBTYPE_WEAPON_LONGBOW    => '3',
        Item::SUBTYPE_WEAPON_SHORTBOW   => '5',
        Item::SUBTYPE_WEAPON_RIFLE      => '4',
        Item::SUBTYPE_WEAPON_STAFF      => '6',
    ];

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getRace(Character $character) {
        if (isset($this->mapRaces[$character->getRace()])) {
            return $this->mapRaces[$character->getRace()];
        }
        return '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getProfession(Character $character) {
        if (isset($this->mapProfessions[$character->getProfession()])) {
            return $this->mapProfessions[$character->getProfession()];
        }
        return '0';
    }

    /**
     * 
     * @param Item $weapon
     * @return string
     */
    protected function getWeapon(Item $weapon = null) {
        if (empty($weapon)) {
            return '0';
        }
        if (isset($this->mapWeapons[$weapon->getSubType()])) {
            return $this->mapWeapons[$weapon->getSubType()];
        }
        return '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getWeapons(Character $character) {
        return $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_A1)) .
            '.' . $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_A2)) .
            ':' . $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_B1)) .
            '.' . $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_B2))
        ;
    }

    /**
     * 
     * @param Item $infusion
     * @return string
     */
    protected function getInfusion(Item $infusion = null) {
        if (empty($infusion)) {
            return '0';
        }
        if (isset($this->mapUpgrades[$infusion->getId()])) {
            return $this->mapUpgrades[$infusion->getId()];
        }
        return '0';
    }

    /**
     * 
     * @param InventorySlot $object
     * @return string
     */
    protected function getInfusionObject(InventorySlot $object = null) {
        if (empty($object)) {
            return '0';
        }
        $infusions = $object->getInfusions();
        return $this->getInfusion(isset($infusions[0]) ? $infusions[0] : null);
    }

    /**
     * 
     * @param InventorySlot $weapon
     * @return string
     */
    protected function getInfusionWeapon(InventorySlot $weapon = null) {
        if (empty($weapon)) {
            return '0.0';
        }
        $infusions = $weapon->getInfusions();
        return $this->getInfusion(isset($infusions[0]) ? $infusions[0] : null) .
            '.' . $this->getInfusion(isset($infusions[1]) ? $infusions[1] : null)
        ;
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getInfusions(Character $character) {
        return $this->getInfusionWeapon($character->getEquipment(Character::SLOT_WEAPON_A1)) .
            '.' . $this->getInfusionWeapon($character->getEquipment(Character::SLOT_WEAPON_A2)) .
            '.' . $this->getInfusionWeapon($character->getEquipment(Character::SLOT_WEAPON_B1)) .
            '.' . $this->getInfusionWeapon($character->getEquipment(Character::SLOT_WEAPON_B2)) .
            '.' . $this->getInfusionWeapon($character->getEquipment(Character::SLOT_WEAPON_AQUATIC_A)) .
            '.' . $this->getInfusionWeapon($character->getEquipment(Character::SLOT_WEAPON_AQUATIC_B)) .
            ':' . $this->getInfusionObject($character->getEquipment(Character::SLOT_HELM)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_SHOULDERS)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_COAT)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_GLOVES)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_LEGGINGS)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_BOOTS)) .
            ':' . $this->getInfusionObject($character->getEquipment(Character::SLOT_AMULET)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_RING1)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_RING2)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_ACCESSORY1)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_ACCESSORY2)) .
            '.' . $this->getInfusionObject($character->getEquipment(Character::SLOT_BACKPACK))
        ;
    }

    /**
     * 
     * @param InventorySlot $armor
     * @return string
     */
    protected function getUpgradeArmor(InventorySlot $armor = null) {
        if (empty($armor)) {
            return '0.0.0.0';
        }
        $code1 = '0';
        $code2 = '0';
        $code3 = '1';
        $code4 = '0';

        $upgrades = $armor->getUpgrades();
        if (isset($upgrades[0])) {
            if ($upgrades[0]->getSubType() != Item::SUBTYPE_UPGRADE_COMPONENT_RUNE) {
                $code1 = '1';
            }
            if (isset($this->mapUpgrades[$upgrades[0]->getId()])) {
                $code2 = $this->mapUpgrades[$upgrades[0]->getId()];
            }
        }
        $attributes = $armor->getAttributes();
        if (!empty($attributes)) {
            $key = $armor->getRarity() . '.' . $attributes['stats_name'];
            if (isset($this->mapStatsArmors[$key])) {
                $code4 = $this->mapStatsArmors[$key];
            }
        }
        return "$code1.$code2.$code3.$code4";
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getUpgradesArmor(Character $character) {
        return $this->getUpgradeArmor($character->getEquipment(Character::SLOT_HELM)) .
            ':' . $this->getUpgradeArmor($character->getEquipment(Character::SLOT_SHOULDERS)) .
            ':' . $this->getUpgradeArmor($character->getEquipment(Character::SLOT_COAT)) .
            ':' . $this->getUpgradeArmor($character->getEquipment(Character::SLOT_GLOVES)) .
            ':' . $this->getUpgradeArmor($character->getEquipment(Character::SLOT_LEGGINGS)) .
            ':' . $this->getUpgradeArmor($character->getEquipment(Character::SLOT_BOOTS))
        ;
    }

    /**
     * 
     * @param InventorySlot $trinket
     * @return string
     */
    protected function getUpgradeTrinket(InventorySlot $trinket = null) {
        if (empty($trinket)) {
            return '0.0.0.0';
        }
        $code1 = '1';
        $code2 = '0';
        $code3 = '1';
        $code4 = '0';

        $upgrades = $trinket->getUpgrades();
        if (isset($upgrades[0])) {
            if (isset($this->mapUpgrades[$upgrades[0]->getId()])) {
                $code2 = $this->mapUpgrades[$upgrades[0]->getId()];
            }
        }
        $attributes = $trinket->getAttributes();
        if (!empty($attributes)) {
            $key = $trinket->getRarity() . '.' . $attributes['stats_name'];
            if (isset($this->mapStatsTrinkets[$key])) {
                $code4 = $this->mapStatsTrinkets[$key];
            }
        }
        return "$code1.$code2.$code3.$code4";
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getUpgradesTrinkets(Character $character) {
        return $this->getUpgradeTrinket($character->getEquipment(Character::SLOT_AMULET)) .
            ':' . $this->getUpgradeTrinket($character->getEquipment(Character::SLOT_RING1)) .
            ':' . $this->getUpgradeTrinket($character->getEquipment(Character::SLOT_RING2)) .
            ':' . $this->getUpgradeTrinket($character->getEquipment(Character::SLOT_ACCESSORY1)) .
            ':' . $this->getUpgradeTrinket($character->getEquipment(Character::SLOT_ACCESSORY2)) .
            ':' . $this->getUpgradeTrinket($character->getEquipment(Character::SLOT_BACKPACK))
        ;
    }

    /**
     * 
     * @param InventorySlot $weapon
     * @return string
     */
    protected function getUpgradesWeapon(InventorySlot $weapon = null) {
        if (empty($weapon)) {
            return '0.0.0.0.0.0';
        }
        $code1 = '0';
        $code2 = '0';
        $code3 = '1';
        $code4 = '0';
        $code5 = '0';
        $code6 = '0';

        $upgrades = $weapon->getUpgrades();
        if (isset($upgrades[0])) {
            if ($upgrades[0]->getSubType() != Item::SUBTYPE_UPGRADE_COMPONENT_SIGIL) {
                $code1 = '1';
            }
            if (isset($this->mapUpgrades[$upgrades[0]->getId()])) {
                $code2 = $this->mapUpgrades[$upgrades[0]->getId()];
            }
        }
        if (isset($upgrades[1])) {
            if ($upgrades[1]->getSubType() != Item::SUBTYPE_UPGRADE_COMPONENT_SIGIL) {
                $code5 = '1';
            }
            if (isset($this->mapUpgrades[$upgrades[0]->getId()])) {
                $code6 = $this->mapUpgrades[$upgrades[0]->getId()];
            }
        }
        $attributes = $weapon->getAttributes();
        if (!empty($attributes)) {
            $key = $weapon->getRarity() . '.' . $attributes['stats_name'];
            if (isset($this->mapStatsWeapons[$key])) {
                $code4 = $this->mapStatsWeapons[$key];
            }
        }
        return "$code1.$code2.$code3.$code4.$code5.$code6";
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getUpgradesWeapons(Character $character) {
        return $this->getUpgradesWeapon($character->getEquipment(Character::SLOT_WEAPON_A1)) .
            ':' . $this->getUpgradesWeapon($character->getEquipment(Character::SLOT_WEAPON_A2)) .
            ':' . $this->getUpgradesWeapon($character->getEquipment(Character::SLOT_WEAPON_B1)) .
            ':' . $this->getUpgradesWeapon($character->getEquipment(Character::SLOT_WEAPON_B2)) .
            ':' . $this->getUpgradesWeapon($character->getEquipment(Character::SLOT_WEAPON_AQUATIC_A)) .
            ':' . $this->getUpgradesWeapon($character->getEquipment(Character::SLOT_WEAPON_AQUATIC_B))
        ;
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    public function getLink(Character $character) {
        try {
            $client   = $character->getClient();
            $lang     = $client->getLang();
            $cache    = $client->getClientV2()->getRequestManager()->getCache();
            $cacheKey = 'gw2skills-link/' . $lang . '/' . $character->getName();
            if ($cache) {
                $url = $cache->get($cacheKey);
                if ($url) {
                    return $url;
                }
            }

            $data = [
                'bf'   => '0.0',
                'inf'  => $this->getInfusions($character),
                'mode' => '1', // PvE
                'p'    => $this->getProfession($character),
                'pet'  => '0.0.0.0',
                'r'    => $this->getRace($character),
                's'    => '0.0.0.0.0',
                'sa'   => '0.0.0.0.0',
                't'    => '',
                'up_a' => $this->getUpgradesTrinkets($character),
                'up_b' => $this->getUpgradesArmor($character),
                'up_w' => $this->getUpgradesWeapons($character),
                'w'    => $this->getWeapons($character),
            ];

            $curl     = new Curl();
            $curl->setUrl('http://en.gw2skills.net/ajax/buildTcode/');
            $curl->setPost($data);
            $response = $curl->execute();
            $content  = $response->getContent();
            if (preg_match('!^.*?\n(.*?-e)!si', $content, $m)) {
                $url = 'http://' . $lang . '.gw2skills.net/editor/?' . trim($m[1]);
                if ($cache) {
                    $cache->set($cacheKey, $url, 900); // 15 min
                }
                return $url;
            }
        }
        catch (Exception $e) {
            
        }
        return null;
    }

}
