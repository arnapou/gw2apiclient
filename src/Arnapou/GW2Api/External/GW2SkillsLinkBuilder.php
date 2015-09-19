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
use Arnapou\GW2Api\Model\Build;
use Arnapou\GW2Api\Model\Character;
use Arnapou\GW2Api\Model\Item;
use Arnapou\GW2Api\Model\InventorySlot;
use Arnapou\GW2Api\Model\Specialization;
use Arnapou\GW2Api\Model\SpecializationTrait;

class GW2SkillsLinkBuilder {

    const MODE_PVP = 0;
    const MODE_PVE = 1;
    const MODE_WVW = 2;

    protected $mapSpecializations = [
        31 => 1, // elementalist: Fire
        41 => 2, // elementalist: Air
        26 => 3, // elementalist: Earth
        17 => 4, // elementalist: Water
        37 => 5, // elementalist: Arcane
        48 => 6, // elementalist: Tempest
        4  => 7, // warrior: Strength
        36 => 8, // warrior: Arms
        22 => 9, // warrior: Defense
        11 => 10, // warrior: Tactics
        51 => 11, // warrior: Discipline
        18 => 12, // warrior: Berserker
        8  => 13, // ranger: Marksmanship
        30 => 14, // ranger: Skirmishing
        33 => 15, // ranger: Wilderness Survival
        25 => 16, // ranger: Nature Magic
        32 => 17, // ranger: Beastmastery
        53 => 19, // necromancer: Spite
        39 => 20, // necromancer: Curses
        2  => 21, // necromancer: Death Magic
        19 => 22, // necromancer: Blood Magic
        50 => 23, // necromancer: Soul Reaping
        34 => 24, // necromancer: Reaper
        42 => 25, // guardian: Zeal
        16 => 26, // guardian: Radiance
        13 => 27, // guardian: Valor
        49 => 28, // guardian: Honor
        46 => 29, // guardian: Virtues
        27 => 30, // guardian: Dragonhunter
        28 => 31, // thief: Deadly Arts
        35 => 32, // thief: Critical Strikes
        20 => 33, // thief: Shadow Arts
        54 => 34, // thief: Acrobatics
        44 => 35, // thief: Trickery
        7  => 36, // thief: Daredevil
        6  => 37, // engineer: Explosives
        38 => 38, // engineer: Firearms
        47 => 39, // engineer: Inventions
        29 => 40, // engineer: Alchemy
        21 => 41, // engineer: Tools
        10 => 43, // mesmer: Domination
        1  => 44, // mesmer: Dueling
        45 => 45, // mesmer: Chaos
        23 => 46, // mesmer: Inspiration
        24 => 47, // mesmer: Illusions
        40 => 48, // mesmer: Chronomancer
        // not found TLinesDB['revenant'][1] = [49,"Corruption","Corruption",0]
        // not found TLinesDB['revenant'][2] = [50,"Retribution","Retribution",0]
        // not found TLinesDB['revenant'][3] = [51,"Salvation","Salvation",0]
        // not found TLinesDB['revenant'][4] = [52,"Invocation","Invocation",0]
        // not found TLinesDB['revenant'][5] = [53,"Devastation","Devastation",0]
        // not found TLinesDB['revenant'][6] = [54,"Herald","Herald",1]
    ];
    protected $mapTraits          = [
        320  => 3, // Elementalist: Fire, Empowering Flame
        318  => 4, // Elementalist: Fire, Sunspot
        319  => 5, // Elementalist: Fire, Burning Rage
        296  => 7, // Elementalist: Fire, Burning Precision
        328  => 8, // Elementalist: Fire, Conjurer
        335  => 11, // Elementalist: Fire, Burning Fire
        325  => 9, // Elementalist: Fire, Pyromancer's Training
        340  => 18, // Elementalist: Fire, One with Fire
        334  => 17, // Elementalist: Fire, Power Overwhelming
        1510 => 10, // Elementalist: Fire, Persisting Flames
        294  => 14, // Elementalist: Fire, Pyromancer's Puissance
        1675 => 15, // Elementalist: Fire, Blinding Ashes
        221  => 22, // Elementalist: Air, Zephyr's Speed
        222  => 23, // Elementalist: Air, Electric Discharge
        223  => 24, // Elementalist: Air, Weak Spot
        227  => 28, // Elementalist: Air, Zephyr's Boon
        224  => 37, // Elementalist: Air, One with Air
        232  => 38, // Elementalist: Air, Ferocious Winds
        229  => 34, // Elementalist: Air, Inscription
        214  => 26, // Elementalist: Air, Aeromancer's Training
        1502 => 32, // Elementalist: Air, Tempest Defense
        226  => 36, // Elementalist: Air, Bolt to the Heart
        1503 => 30, // Elementalist: Air, Fresh Air
        1672 => 25, // Elementalist: Air, Lightning Rod
        278  => 42, // Elementalist: Earth, Stone Flesh
        279  => 43, // Elementalist: Earth, Earthen Blast
        280  => 44, // Elementalist: Earth, Geomancer's Defense
        282  => 55, // Elementalist: Earth, Earth's Embrace
        1507 => 47, // Elementalist: Earth, Serrated Stones
        289  => 56, // Elementalist: Earth, Elemental Shielding
        275  => 53, // Elementalist: Earth, Strength of Stone
        281  => 57, // Elementalist: Earth, Rock Solid
        277  => 48, // Elementalist: Earth, Geomancer's Training
        1508 => 50, // Elementalist: Earth, Diamond Skin
        287  => 58, // Elementalist: Earth, Written in Stone
        1674 => 45, // Elementalist: Earth, Stone Heart
        350  => 61, // Elementalist: Water, Soothing Mist
        351  => 62, // Elementalist: Water, Healing Ripple
        1676 => 63, // Elementalist: Water, Aquatic Benevolence
        348  => 77, // Elementalist: Water, Soothing Ice
        363  => 69, // Elementalist: Water, Piercing Shards
        360  => 70, // Elementalist: Water, Stop, Drop, and Roll
        364  => 67, // Elementalist: Water, Soothing Disruption
        358  => 66, // Elementalist: Water, Cleansing Wave
        349  => 76, // Elementalist: Water, Aquamancer's Training
        362  => 73, // Elementalist: Water, Cleansing Water
        361  => 64, // Elementalist: Water, Powerful Aura
        2028 => 65, // Elementalist: Water, Soothing Power
        268  => 81, // Elementalist: Arcane, Arcane Fury
        2004 => 80, // Elementalist: Arcane, Elemental Enchantment
        264  => 82, // Elementalist: Arcane, Elemental Attunement
        253  => 86, // Elementalist: Arcane, Arcane Precision
        266  => 94, // Elementalist: Arcane, Renewing Stamina
        1487 => 87, // Elementalist: Arcane, Arcane Abatement
        265  => 85, // Elementalist: Arcane, Arcane Resurrection
        1673 => 83, // Elementalist: Arcane, Elemental Contingency
        257  => 93, // Elementalist: Arcane, Final Shielding
        238  => 90, // Elementalist: Arcane, Evasive Arcana
        263  => 89, // Elementalist: Arcane, Elemental Surge
        1511 => 91, // Elementalist: Arcane, Bountiful Power
        2025 => 98, // Elementalist: Tempest, Singularity
        1938 => 99, // Elementalist: Tempest, Speedy Conduit
        1948 => 100, // Elementalist: Tempest, Hardy Conduit
        1952 => 101, // Elementalist: Tempest, Gale Song
        1962 => 102, // Elementalist: Tempest, Latent Stamina
        1886 => 103, // Elementalist: Tempest, Unstable Conduit
        1891 => 104, // Elementalist: Tempest, Tempestuous Aria
        1902 => 105, // Elementalist: Tempest, Earthen Proxy
        2015 => 106, // Elementalist: Tempest, Harmonious Conduit
        1839 => 107, // Elementalist: Tempest, Imbued Melodies
        2033 => 108, // Elementalist: Tempest, Lucid Singularity
        1986 => 109, // Elementalist: Tempest, Elemental Bastion
        1446 => 112, // Warrior: Strength, Reckless Dodge
        1448 => 113, // Warrior: Strength, Building Momentum
        1453 => 114, // Warrior: Strength, Stick and Move
        1447 => 115, // Warrior: Strength, Death from Above
        1451 => 127, // Warrior: Strength, Restorative Strength
        1444 => 125, // Warrior: Strength, Peak Performance
        2000 => 117, // Warrior: Strength, Body Blow
        1338 => 116, // Warrior: Strength, Forceful Greatsword
        1449 => 120, // Warrior: Strength, Great Fortitude
        1437 => 126, // Warrior: Strength, Berserker's Power
        1454 => 119, // Warrior: Strength, Distracting Strikes
        1440 => 122, // Warrior: Strength, Axe Mastery
        1342 => 134, // Warrior: Arms, Precise Strikes
        1343 => 135, // Warrior: Arms, Rending Strikes
        1337 => 136, // Warrior: Arms, Bloodlust
        1455 => 137, // Warrior: Arms, Berserker's Fury
        1344 => 138, // Warrior: Arms, Signet Mastery
        1334 => 143, // Warrior: Arms, Opportunist
        1315 => 142, // Warrior: Arms, Unsuspecting Foe
        1316 => 147, // Warrior: Arms, Deep Strike
        1333 => 153, // Warrior: Arms, Blademaster
        1336 => 140, // Warrior: Arms, Burst Precision
        1346 => 149, // Warrior: Arms, Furious
        1707 => 139, // Warrior: Arms, Dual Wielding
        1350 => 156, // Warrior: Defense, Thick Skin
        1348 => 157, // Warrior: Defense, Adrenal Health
        1380 => 158, // Warrior: Defense, Spiked Armor
        1376 => 163, // Warrior: Defense, Shield Master
        1488 => 162, // Warrior: Defense, Dogged March
        1372 => 166, // Warrior: Defense, Cull the Weak
        1368 => 167, // Warrior: Defense, Defy Pain
        1379 => 168, // Warrior: Defense, Armored Attack
        1367 => 172, // Warrior: Defense, Sundering Mace
        1375 => 165, // Warrior: Defense, Last Stand
        1649 => 160, // Warrior: Defense, Cleansing Ire
        1708 => 159, // Warrior: Defense, Rousing Resilience
        1480 => 175, // Warrior: Tactics, Determined Revival
        1485 => 177, // Warrior: Tactics, Reviver's Might
        1481 => 176, // Warrior: Tactics, Inspiring Presence
        1469 => 181, // Warrior: Tactics, Leg Specialist
        1474 => 191, // Warrior: Tactics, Quick Breathing
        1471 => 184, // Warrior: Tactics, Empowered
        1486 => 189, // Warrior: Tactics, Shrug It Off
        1479 => 185, // Warrior: Tactics, Burning Arrows
        1482 => 183, // Warrior: Tactics, Empower Allies
        1667 => 179, // Warrior: Tactics, Powerful Synergy
        1470 => 188, // Warrior: Tactics, Vigorous Shouts
        1711 => 178, // Warrior: Tactics, Phalanx Strength
        1415 => 194, // Warrior: Discipline, Versatile Rage
        1416 => 195, // Warrior: Discipline, Fast Hands
        1417 => 196, // Warrior: Discipline, Versatile Power
        1329 => 197, // Warrior: Discipline, Crack Shot
        1413 => 208, // Warrior: Discipline, Warrior's Sprint
        1381 => 211, // Warrior: Discipline, Vengeful Return
        1484 => 198, // Warrior: Discipline, Inspiring Battle Standard
        1489 => 206, // Warrior: Discipline, Destruction of the Empowered
        1709 => 200, // Warrior: Discipline, Brawler's Recovery
        1369 => 199, // Warrior: Discipline, Merciless Hammer
        1317 => 201, // Warrior: Discipline, Heightened Focus
        1657 => 205, // Warrior: Discipline, Burst Mastery
        1831 => 213, // Warrior: Berserker, Primal Rage
        1993 => 214, // Warrior: Berserker, Always Angry
        2046 => 215, // Warrior: Berserker, Fatal Frenzy
        2049 => 216, // Warrior: Berserker, Smash Brawler
        2039 => 217, // Warrior: Berserker, Last Blaze
        1977 => 218, // Warrior: Berserker, Savage Instinct
        2011 => 219, // Warrior: Berserker, Blood Reaction
        2042 => 220, // Warrior: Berserker, Heat the Soul
        2002 => 221, // Warrior: Berserker, Dead or Alive
        1928 => 222, // Warrior: Berserker, Bloody Roar
        2038 => 223, // Warrior: Berserker, King of Fires
        2043 => 224, // Warrior: Berserker, Eternal Champion
        1010 => 227, // Ranger: Marksmanship, Opening Strike
        1009 => 228, // Ranger: Marksmanship, Alpha Training
        1011 => 229, // Ranger: Marksmanship, Precise Strike
        1021 => 230, // Ranger: Marksmanship, Enlargement
        1014 => 234, // Ranger: Marksmanship, Predator's Instinct
        986  => 231, // Ranger: Marksmanship, Clarion Bond
        1001 => 246, // Ranger: Marksmanship, Brutish Seals
        1000 => 236, // Ranger: Marksmanship, Steady Focus
        1070 => 232, // Ranger: Marksmanship, Moment of Clarity
        996  => 245, // Ranger: Marksmanship, Predator's Onslaught
        1015 => 240, // Ranger: Marksmanship, Remorseless
        1698 => 233, // Ranger: Marksmanship, Lead the Wind
        1080 => 249, // Ranger: Skirmishing, Tail Wind
        1083 => 250, // Ranger: Skirmishing, Furious Grip
        1068 => 251, // Ranger: Skirmishing, Hunter's Tactics
        1069 => 255, // Ranger: Skirmishing, Sharpened Edges
        1067 => 257, // Ranger: Skirmishing, Primal Reflexes
        1075 => 263, // Ranger: Skirmishing, Trapper's Expertise
        1016 => 252, // Ranger: Skirmishing, Spotter
        1700 => 253, // Ranger: Skirmishing, Strider's Defense
        1846 => 254, // Ranger: Skirmishing, Hidden Barbs
        1064 => 259, // Ranger: Skirmishing, Quick Draw
        1912 => 260, // Ranger: Skirmishing, Light on your Feet
        1888 => 261, // Ranger: Skirmishing, Most Dangerous Game
        1096 => 268, // Ranger: Wilderness Survival, Natural Vigor
        1090 => 269, // Ranger: Wilderness Survival, Companion's Defense
        1089 => 270, // Ranger: Wilderness Survival, Bark Skin
        1098 => 275, // Ranger: Wilderness Survival, Soften the Fall
        1086 => 279, // Ranger: Wilderness Survival, Oakheart Salve
        1099 => 276, // Ranger: Wilderness Survival, Expertise Training
        1101 => 278, // Ranger: Wilderness Survival, Ambidexterity
        2032 => 272, // Ranger: Wilderness Survival, Refined Toxins
        1100 => 284, // Ranger: Wilderness Survival, Shared Anguish
        1094 => 283, // Ranger: Wilderness Survival, Empathic Bond
        1699 => 287, // Ranger: Wilderness Survival, Wilderness Knowledge
        1701 => 271, // Ranger: Wilderness Survival, Poison Master
        1055 => 290, // Ranger: Nature Magic, Rejuvenation
        1056 => 291, // Ranger: Nature Magic, Fortifying Bond
        1059 => 292, // Ranger: Nature Magic, Lingering Magic
        1062 => 293, // Ranger: Nature Magic, Bountiful Hunter
        978  => 294, // Ranger: Nature Magic, Instinctive Reaction
        1060 => 295, // Ranger: Nature Magic, Allies' Aid
        1054 => 297, // Ranger: Nature Magic, Evasive Purity
        965  => 298, // Ranger: Nature Magic, Vigorous Training
        964  => 299, // Ranger: Nature Magic, Windborne Notes
        1038 => 305, // Ranger: Nature Magic, Nature's Vengeance
        1988 => 306, // Ranger: Nature Magic, Protective Ward
        1697 => 307, // Ranger: Nature Magic, Invigorating Bond
        1900 => 315, // Ranger: Beastmastery, Pack Alpha
        974  => 316, // Ranger: Beastmastery, Loud Whistle
        1065 => 317, // Ranger: Beastmastery, Pet's Prowess
        1861 => 321, // Ranger: Beastmastery, Go for the Eyes
        1072 => 322, // Ranger: Beastmastery, Companion's Might
        1606 => 339, // Ranger: Beastmastery, Resounding Timbre
        975  => 320, // Ranger: Beastmastery, Wilting Strike
        1047 => 323, // Ranger: Beastmastery, Two-Handed Training
        970  => 329, // Ranger: Beastmastery, Natural Healing
        1945 => 319, // Ranger: Beastmastery, Beastly Warden
        968  => 334, // Ranger: Beastmastery, Zephyr's Speed
        1066 => 318, // Ranger: Beastmastery, Honed Axes
        913  => 355, // Necromancer: Spite, Reaper's Might
        915  => 356, // Necromancer: Spite, Death's Embrace
        917  => 359, // Necromancer: Spite, Siphoned Power
        914  => 364, // Necromancer: Spite, Spiteful Talisman
        916  => 369, // Necromancer: Spite, Spiteful Renewal
        1863 => 370, // Necromancer: Spite, Bitter Chill
        899  => 363, // Necromancer: Spite, Chill of Death
        829  => 360, // Necromancer: Spite, Rending Shroud
        909  => 361, // Necromancer: Spite, Unholy Fervor
        919  => 368, // Necromancer: Spite, Signets of Suffering
        853  => 367, // Necromancer: Spite, Close to Death
        903  => 366, // Necromancer: Spite, Spiteful Spirit
        802  => 376, // Necromancer: Curses, Barbed Precision
        803  => 377, // Necromancer: Curses, Furious Demise
        810  => 378, // Necromancer: Curses, Target the Weak
        1883 => 381, // Necromancer: Curses, Terrifying Descent
        2013 => 382, // Necromancer: Curses, Plague Sending
        815  => 385, // Necromancer: Curses, Chilling Darkness
        816  => 386, // Necromancer: Curses, Master of Corruption
        1693 => 379, // Necromancer: Curses, Path of Corruption
        812  => 380, // Necromancer: Curses, Terror
        813  => 387, // Necromancer: Curses, Weakening Shroud
        1696 => 388, // Necromancer: Curses, Parasitic Contagion
        801  => 390, // Necromancer: Curses, Lingering Curse
        856  => 395, // Necromancer: Death Magic, Armored Shroud
        839  => 396, // Necromancer: Death Magic, Soul Comprehension
        1929 => 397, // Necromancer: Death Magic, Beyond the Veil
        820  => 408, // Necromancer: Death Magic, Flesh of the Master
        857  => 410, // Necromancer: Death Magic, Shrouded Removal
        1922 => 399, // Necromancer: Death Magic, Putrid Defense
        858  => 409, // Necromancer: Death Magic, Necromantic Corruption
        860  => 405, // Necromancer: Death Magic, Reaper's Protection
        855  => 398, // Necromancer: Death Magic, Deadly Strength
        842  => 407, // Necromancer: Death Magic, Death Nova
        1940 => 400, // Necromancer: Death Magic, Corrupter's Fervor
        1694 => 401, // Necromancer: Death Magic, Unholy Sanctuary
        792  => 415, // Necromancer: Blood Magic, Mark of Evasion
        783  => 417, // Necromancer: Blood Magic, Vampiric
        1931 => 418, // Necromancer: Blood Magic, Last Rites
        780  => 433, // Necromancer: Blood Magic, Ritual of Life
        788  => 424, // Necromancer: Blood Magic, Quickening Thirst
        1876 => 419, // Necromancer: Blood Magic, Blood Bond
        789  => 420, // Necromancer: Blood Magic, Life from Death
        799  => 421, // Necromancer: Blood Magic, Banshee's Wail
        1844 => 428, // Necromancer: Blood Magic, Vampiric Presence
        782  => 431, // Necromancer: Blood Magic, Vampiric Rituals
        1692 => 422, // Necromancer: Blood Magic, Unholy Martyr
        778  => 427, // Necromancer: Blood Magic, Transfusion
        887  => 436, // Necromancer: Soul Reaping, Gluttony
        891  => 437, // Necromancer: Soul Reaping, Last Gasp
        874  => 438, // Necromancer: Soul Reaping, Strength of Undeath
        875  => 448, // Necromancer: Soul Reaping, Unyielding Blast
        898  => 449, // Necromancer: Soul Reaping, Soul Marks
        888  => 442, // Necromancer: Soul Reaping, Speed of Shadows
        894  => 453, // Necromancer: Soul Reaping, Spectral Mastery
        861  => 451, // Necromancer: Soul Reaping, Vital Persistence
        892  => 447, // Necromancer: Soul Reaping, Fear of Death
        889  => 443, // Necromancer: Soul Reaping, Foot in the Grave
        893  => 444, // Necromancer: Soul Reaping, Death Perception
        905  => 439, // Necromancer: Soul Reaping, Dhuumfire
        1905 => 454, // Necromancer: Reaper, Shroud Knight
        1879 => 455, // Necromancer: Reaper, Shivers of Dread
        2018 => 456, // Necromancer: Reaper, Cold Shoulder
        1974 => 457, // Necromancer: Reaper, Augury of Death
        2020 => 458, // Necromancer: Reaper, Chilling Nova
        2026 => 459, // Necromancer: Reaper, Relentless Pursuit
        1969 => 460, // Necromancer: Reaper, Soul Eater
        2008 => 461, // Necromancer: Reaper, Chilling Victory
        2031 => 462, // Necromancer: Reaper, Decimate Defenses
        1932 => 463, // Necromancer: Reaper, Blighter's Boon
        1919 => 464, // Necromancer: Reaper, Deathly Chill
        2021 => 465, // Necromancer: Reaper, Reaper's Onslaught
        648  => 468, // Guardian: Zeal, Zealot's Speed
        646  => 469, // Guardian: Zeal, Symbolic Exposure
        649  => 470, // Guardian: Zeal, Symbolic Power
        563  => 480, // Guardian: Zeal, Wrathful Spirit
        634  => 471, // Guardian: Zeal, Fiery Wrath
        1925 => 481, // Guardian: Zeal, Zealous Scepter
        628  => 479, // Guardian: Zeal, Binding Jeopardy
        653  => 478, // Guardian: Zeal, Zealous Blade
        1556 => 477, // Guardian: Zeal, Kindled Zeal
        635  => 472, // Guardian: Zeal, Expeditious Spirit
        637  => 473, // Guardian: Zeal, Shattered Aegis
        2017 => 474, // Guardian: Zeal, Symbolic Avenger
        572  => 485, // Guardian: Radiance, Justice is Blind
        571  => 486, // Guardian: Radiance, Renewed Justice
        568  => 487, // Guardian: Radiance, Radiant Power
        577  => 494, // Guardian: Radiance, Inner Fire
        566  => 491, // Guardian: Radiance, Right-Hand Strength
        574  => 496, // Guardian: Radiance, Healer's Retribution
        578  => 497, // Guardian: Radiance, Wrath of Justice
        567  => 503, // Guardian: Radiance, Radiant Fire
        565  => 498, // Guardian: Radiance, Retribution
        1686 => 488, // Guardian: Radiance, Amplified Wrath
        579  => 500, // Guardian: Radiance, Perfect Inscriptions
        1683 => 489, // Guardian: Radiance, Radiant Retaliation
        582  => 506, // Guardian: Valor, Valorous Defense
        594  => 507, // Guardian: Valor, Courageous Return
        583  => 508, // Guardian: Valor, Might of the Protector
        588  => 512, // Guardian: Valor, Strength of the Fallen
        581  => 513, // Guardian: Valor, Smiter's Boon
        633  => 514, // Guardian: Valor, Focus Mastery
        580  => 520, // Guardian: Valor, Stalwart Defender
        584  => 510, // Guardian: Valor, Strength in Numbers
        1684 => 509, // Guardian: Valor, Communal Defenses
        585  => 515, // Guardian: Valor, Altruistic Healing
        586  => 516, // Guardian: Valor, Monk's Focus
        589  => 522, // Guardian: Valor, Retributive Armor
        564  => 525, // Guardian: Honor, Vigorous Precision
        551  => 526, // Guardian: Honor, Selfless Daring
        1685 => 527, // Guardian: Honor, Purity of Body
        1899 => 529, // Guardian: Honor, Invigorated Bulwark
        559  => 532, // Guardian: Honor, Protective Reviver
        654  => 533, // Guardian: Honor, Protector's Impact
        557  => 537, // Guardian: Honor, Honorable Staff
        549  => 531, // Guardian: Honor, Pure of Heart
        562  => 542, // Guardian: Honor, Empowering Might
        553  => 540, // Guardian: Honor, Pure of Voice
        558  => 541, // Guardian: Honor, Writ of Persistence
        1682 => 530, // Guardian: Honor, Force of Will
        621  => 547, // Guardian: Virtues, Inspired Virtue
        604  => 548, // Guardian: Virtues, Virtue of Retribution
        620  => 549, // Guardian: Virtues, Power of the Virtuous
        624  => 565, // Guardian: Virtues, Unscathed Contender
        625  => 560, // Guardian: Virtues, Retaliatory Subconscious
        617  => 551, // Guardian: Virtues, Master of Consecrations
        603  => 563, // Guardian: Virtues, Supreme Justice
        610  => 567, // Guardian: Virtues, Absolute Resolution
        587  => 568, // Guardian: Virtues, Glacial Heart
        622  => 555, // Guardian: Virtues, Permeating Wrath
        554  => 556, // Guardian: Virtues, Battle Presence
        612  => 554, // Guardian: Virtues, Indomitable Courage
        1848 => 571, // Guardian: Dragonhunter, Virtuous Action
        1896 => 572, // Guardian: Dragonhunter, Defender's Dogma
        1926 => 573, // Guardian: Dragonhunter, Pure of Sight
        1898 => 574, // Guardian: Dragonhunter, Piercing Light
        1835 => 575, // Guardian: Dragonhunter, Zealot's Aggression
        1911 => 576, // Guardian: Dragonhunter, Soaring Devastation
        2037 => 577, // Guardian: Dragonhunter, Hunter's Determination
        1943 => 578, // Guardian: Dragonhunter, Bulwark
        1983 => 579, // Guardian: Dragonhunter, Dulled Senses
        1908 => 580, // Guardian: Dragonhunter, Hunter's Fortification
        1963 => 581, // Guardian: Dragonhunter, Heavy Light
        1955 => 582, // Guardian: Dragonhunter, Big Game Hunter
        1279 => 585, // Thief: Deadly Arts, Serpent's Touch
        1280 => 586, // Thief: Deadly Arts, Lotus Poison
        1257 => 587, // Thief: Deadly Arts, Exposed Weakness
        1245 => 589, // Thief: Deadly Arts, Dagger Training
        1276 => 597, // Thief: Deadly Arts, Mug
        1164 => 598, // Thief: Deadly Arts, Trappers Respite
        1169 => 599, // Thief: Deadly Arts, Deadly Trapper
        1292 => 592, // Thief: Deadly Arts, Panic Strike
        1704 => 588, // Thief: Deadly Arts, Revealed Training
        1291 => 594, // Thief: Deadly Arts, Potent Poison
        1167 => 602, // Thief: Deadly Arts, Improvisation
        1269 => 600, // Thief: Deadly Arts, Executioner
        1281 => 607, // Thief: Critical Strikes, Keen Observer
        1210 => 608, // Thief: Critical Strikes, Unrelenting Strikes
        1282 => 609, // Thief: Critical Strikes, Ferocious Strikes
        1209 => 616, // Thief: Critical Strikes, Side Strike
        1267 => 618, // Thief: Critical Strikes, Signets of Power
        1268 => 611, // Thief: Critical Strikes, Flawless Strike
        1170 => 610, // Thief: Critical Strikes, Sundering Strikes
        1272 => 622, // Thief: Critical Strikes, Practiced Tolerance
        1299 => 619, // Thief: Critical Strikes, Ankle Shots
        1904 => 612, // Thief: Critical Strikes, No Quarter
        1215 => 624, // Thief: Critical Strikes, Hidden Killer
        1702 => 613, // Thief: Critical Strikes, Invigorating Precision
        1294 => 627, // Thief: Shadow Arts, Merciful Ambush
        1136 => 628, // Thief: Shadow Arts, Meld with Shadows
        1705 => 629, // Thief: Shadow Arts, Resilience of Shadows
        1160 => 631, // Thief: Shadow Arts, Last Refuge
        1293 => 632, // Thief: Shadow Arts, Concealed Defeat
        1284 => 643, // Thief: Shadow Arts, Shadow's Embrace
        1297 => 641, // Thief: Shadow Arts, Shadow Protector
        1130 => 636, // Thief: Shadow Arts, Hidden Thief
        1300 => 645, // Thief: Shadow Arts, Leeching Venoms
        1134 => 637, // Thief: Shadow Arts, Cloaked in Shadow
        1135 => 642, // Thief: Shadow Arts, Shadow's Rejuvenation
        1162 => 644, // Thief: Shadow Arts, Venomous Aura
        1240 => 648, // Thief: Acrobatics, Expeditious Dodger
        1234 => 649, // Thief: Acrobatics, Feline Grace
        1242 => 650, // Thief: Acrobatics, Endless Stamina
        1112 => 660, // Thief: Acrobatics, Fleet Shadow
        1289 => 665, // Thief: Acrobatics, Vigorous Recovery
        1237 => 663, // Thief: Acrobatics, Pain Response
        1241 => 652, // Thief: Acrobatics, Guarded Initiation
        1192 => 651, // Thief: Acrobatics, Swindler's Equilibrium
        1290 => 657, // Thief: Acrobatics, Hard to Catch
        1238 => 664, // Thief: Acrobatics, Assassin's Reward
        1295 => 659, // Thief: Acrobatics, Upper Hand
        1703 => 653, // Thief: Acrobatics, Don't Stop
        1137 => 668, // Thief: Trickery, Kleptomaniac
        1232 => 669, // Thief: Trickery, Preparedness
        1157 => 670, // Thief: Trickery, Lead Attacks
        1159 => 676, // Thief: Trickery, Uncatchable
        1252 => 677, // Thief: Trickery, Flanking Strikes
        1163 => 681, // Thief: Trickery, Thrill of the Crime
        1277 => 680, // Thief: Trickery, Bountiful Theft
        1286 => 685, // Thief: Trickery, Trickster
        1190 => 672, // Thief: Trickery, Pressure Striking
        1187 => 671, // Thief: Trickery, Quick Pockets
        1158 => 683, // Thief: Trickery, Sleight of Hand
        1706 => 673, // Thief: Trickery, Bewildering Ambush
        1994 => 686, // Thief: Daredevil, Enforcer Training
        1887 => 688, // Thief: Daredevil, Driven Fortitude
        1837 => 687, // Thief: Daredevil, Endurance Thief
        1933 => 689, // Thief: Daredevil, Evasive Empowerment
        2023 => 690, // Thief: Daredevil, Weakening Strikes
        1949 => 691, // Thief: Daredevil, Brawler's Tenacity
        1884 => 692, // Thief: Daredevil, Staff Master
        1893 => 693, // Thief: Daredevil, Escapist's Absolution
        1975 => 694, // Thief: Daredevil, Impacting Disruption
        1833 => 695, // Thief: Daredevil, Lotus Training
        1964 => 697, // Thief: Daredevil, Unhindered Combatant
        2047 => 696, // Thief: Daredevil, Bounding Dodger
        432  => 700, // Engineer: Explosives, Evasive Powder Keg
        517  => 702, // Engineer: Explosives, Steel-Packed Powder
        429  => 703, // Engineer: Explosives, Explosive Powder
        514  => 713, // Engineer: Explosives, Grenadier
        525  => 714, // Engineer: Explosives, Explosive Descent
        1882 => 715, // Engineer: Explosives, Glass Cannon
        482  => 716, // Engineer: Explosives, Aim-Assisted Rocket
        1892 => 717, // Engineer: Explosives, Shaped Charge
        1944 => 705, // Engineer: Explosives, Short Fuse
        1541 => 718, // Engineer: Explosives, Siege Rounds
        505  => 710, // Engineer: Explosives, Shrapnel
        1947 => 719, // Engineer: Explosives, Thermobaric Detonation
        515  => 726, // Engineer: Firearms, Sharpshooter
        536  => 727, // Engineer: Firearms, Hematic Focus
        516  => 728, // Engineer: Firearms, Serrated Steel
        1878 => 729, // Engineer: Firearms, Chemical Rounds
        1930 => 730, // Engineer: Firearms, Heavy Armor Exploit
        1914 => 731, // Engineer: Firearms, High Caliber
        1984 => 732, // Engineer: Firearms, Pinpoint Distribution
        2006 => 739, // Engineer: Firearms, Skilled Marksman
        1923 => 733, // Engineer: Firearms, No Scope
        510  => 737, // Engineer: Firearms, Juggernaut
        526  => 741, // Engineer: Firearms, Modified Ammunition
        433  => 742, // Engineer: Firearms, Incendiary Powder
        518  => 751, // Engineer: Inventions, Cleansing Synergy
        508  => 752, // Engineer: Inventions, Heal Resonator
        519  => 753, // Engineer: Inventions, Energy Amplifier
        394  => 766, // Engineer: Inventions, Over Shield
        1901 => 754, // Engineer: Inventions, Automated Medical Response
        507  => 757, // Engineer: Inventions, Autodefense Bomb Dispenser
        1678 => 758, // Engineer: Inventions, Experimental Turrets
        1834 => 755, // Engineer: Inventions, Soothing Detonation
        445  => 772, // Engineer: Inventions, Mecha Legs
        472  => 759, // Engineer: Inventions, Advanced Turrets
        1680 => 760, // Engineer: Inventions, Bunker Down
        1916 => 756, // Engineer: Inventions, Medical Dispersion Field
        468  => 779, // Engineer: Alchemy, Hidden Flask
        487  => 780, // Engineer: Alchemy, Transmute
        413  => 781, // Engineer: Alchemy, Alchemical Tinctures
        396  => 789, // Engineer: Alchemy, Invigorating Speed
        509  => 790, // Engineer: Alchemy, Protection Injection
        521  => 783, // Engineer: Alchemy, Health Insurance
        520  => 784, // Engineer: Alchemy, Inversion Enzyme
        469  => 796, // Engineer: Alchemy, Self-Regulating Defenses
        470  => 797, // Engineer: Alchemy, Backpack Regenerator
        473  => 795, // Engineer: Alchemy, HGH
        1871 => 785, // Engineer: Alchemy, Stimulant Supplier
        1854 => 786, // Engineer: Alchemy, Iron Blooded
        1979 => 802, // Engineer: Tools, Optimized Activation
        1872 => 803, // Engineer: Tools, Mechanized Deployment
        1936 => 804, // Engineer: Tools, Excessive Energy
        532  => 822, // Engineer: Tools, Static Discharge
        1997 => 805, // Engineer: Tools, Reactive Lenses
        531  => 819, // Engineer: Tools, Power Wrench
        512  => 814, // Engineer: Tools, Streamlined Kits
        1946 => 806, // Engineer: Tools, Lock On
        1832 => 807, // Engineer: Tools, Takedown Round
        1856 => 808, // Engineer: Tools, Kinetic Battery
        523  => 812, // Engineer: Tools, Adrenal Implant
        1679 => 809, // Engineer: Tools, Gadgeteer
        685  => 837, // Mesmer: Domination, Illusion of Vulnerability
        694  => 838, // Mesmer: Domination, Dazzling
        1941 => 839, // Mesmer: Domination, Fragility
        686  => 845, // Mesmer: Domination, Confounding Suggestions
        682  => 849, // Mesmer: Domination, Empowered Illusions
        687  => 854, // Mesmer: Domination, Rending Shatter
        693  => 851, // Mesmer: Domination, Shattered Concentration
        713  => 840, // Mesmer: Domination, Blurred Inscriptions
        712  => 841, // Mesmer: Domination, Furious Interruption
        681  => 847, // Mesmer: Domination, Imagined Burden
        680  => 848, // Mesmer: Domination, Mental Anguish
        1688 => 842, // Mesmer: Domination, Power Block
        706  => 857, // Mesmer: Dueling, Critical Infusion
        710  => 858, // Mesmer: Dueling, Sharper Images
        707  => 859, // Mesmer: Dueling, Master Fencer
        701  => 870, // Mesmer: Dueling, Phantasmal Fury
        705  => 866, // Mesmer: Dueling, Desperate Decoy
        700  => 871, // Mesmer: Dueling, Duelist's Discipline
        1889 => 860, // Mesmer: Dueling, Blinding Dissipation
        1960 => 861, // Mesmer: Dueling, Evasive Mirror
        708  => 873, // Mesmer: Dueling, Fencer's Finesse
        692  => 865, // Mesmer: Dueling, Harmonious Mantras
        1950 => 862, // Mesmer: Dueling, Mistrust
        704  => 867, // Mesmer: Dueling, Deceptive Evasion
        666  => 876, // Mesmer: Chaos, Metaphysical Rejuvenation
        667  => 877, // Mesmer: Chaos, Illusionary Membrane
        1865 => 878, // Mesmer: Chaos, Chaotic Persistence
        670  => 887, // Mesmer: Chaos, Descent into Madness
        675  => 881, // Mesmer: Chaos, Illusionary Defense
        677  => 888, // Mesmer: Chaos, Master of Manipulation
        673  => 890, // Mesmer: Chaos, Mirror of Anguish
        668  => 879, // Mesmer: Chaos, Chaotic Transference
        669  => 893, // Mesmer: Chaos, Chaotic Dampening
        671  => 886, // Mesmer: Chaos, Chaotic Interruption
        674  => 884, // Mesmer: Chaos, Prismatic Understanding
        1687 => 880, // Mesmer: Chaos, Bountiful Disillusionment
        757  => 896, // Mesmer: Inspiration, Mender's Purity
        1852 => 898, // Mesmer: Inspiration, Inspiring Distortion
        1915 => 899, // Mesmer: Inspiration, Healing Prism
        756  => 905, // Mesmer: Inspiration, Medic's Feedback
        738  => 909, // Mesmer: Inspiration, Restorative Mantras
        744  => 911, // Mesmer: Inspiration, Persisting Images
        751  => 906, // Mesmer: Inspiration, Warden's Feedback
        740  => 910, // Mesmer: Inspiration, Restorative Illusions
        1980 => 900, // Mesmer: Inspiration, Protected Phantasms
        2005 => 901, // Mesmer: Inspiration, Mental Defense
        1866 => 902, // Mesmer: Inspiration, Illusionary Inspiration
        752  => 908, // Mesmer: Inspiration, Temporal Enchanter
        734  => 917, // Mesmer: Illusions, Illusionary Retribution
        723  => 916, // Mesmer: Illusions, Illusionist's Celerity
        731  => 918, // Mesmer: Illusions, Master of Misdirection
        721  => 927, // Mesmer: Illusions, Compounding Power
        1869 => 920, // Mesmer: Illusions, Persistence of Memory
        691  => 919, // Mesmer: Illusions, The Pledge
        722  => 921, // Mesmer: Illusions, Shattered Strength
        729  => 933, // Mesmer: Illusions, Phantasmal Haste
        1690 => 923, // Mesmer: Illusions, Maim the Disillusioned
        733  => 925, // Mesmer: Illusions, Ineptitude
        2035 => 922, // Mesmer: Illusions, Master of Fragmentation
        753  => 924, // Mesmer: Illusions, Malicious Sorcery
        2030 => 936, // Mesmer: Chronomancer, Time Splitter
        1927 => 937, // Mesmer: Chronomancer, Flow of Time
        1859 => 938, // Mesmer: Chronomancer, Time Marches On
        1838 => 940, // Mesmer: Chronomancer, Delayed Reactions
        1995 => 941, // Mesmer: Chronomancer, Time Catches Up
        1987 => 939, // Mesmer: Chronomancer, All's Well That Ends Well
        2009 => 942, // Mesmer: Chronomancer, Danger Time
        1913 => 943, // Mesmer: Chronomancer, Illusionary Reversion
        1978 => 944, // Mesmer: Chronomancer, Improved Alacrity
        1942 => 946, // Mesmer: Chronomancer, Lost Time
        2022 => 947, // Mesmer: Chronomancer, Seize The Moment
        1890 => 945, // Mesmer: Chronomancer, Chronophantasma
    ];
    protected $mapStatsWeapons    = [
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
    protected $mapStatsTrinkets   = [
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
    protected $mapStatsArmors     = [
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
    protected $mapUpgrades        = [
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
    protected $mapRaces           = [
        Character::RACE_HUMAN   => '1',
        Character::RACE_CHARR   => '2',
        Character::RACE_NORN    => '3',
        Character::RACE_ASURA   => '4',
        Character::RACE_SYLVARI => '5',
    ];
    protected $mapProfessions     = [
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
    protected $mapWeapons         = [
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
            if (isset($this->mapUpgrades[$upgrades[1]->getId()])) {
                $code6 = $this->mapUpgrades[$upgrades[1]->getId()];
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
     * @param Build $build
     * @return string
     */
    protected function getTraits(Build $build = null) {
        if (empty($build)) {
            return '';
        }
        $parts = [];
        foreach ($build->getSpecializations() as /* @var $spe Specialization */ $spe) {
            if (isset($this->mapSpecializations[$spe->getId()])) {
                $ids = [$this->mapSpecializations[$spe->getId()], 'false', 'false', 'false'];
                foreach ($spe->getMajorTraits() as /* @var $trait SpecializationTrait */ $trait) {
                    if ($trait->isSelected() && isset($this->mapTraits[$trait->getId()])) {
                        $ids[$trait->getTier()] = $this->mapTraits[$trait->getId()];
                    }
                }
                $parts[] = implode('.', $ids);
            }
        }
        return implode(':', $parts);
    }

    /**
     * 
     * @param Character $character
     * @param int $mode
     * @return string
     */
    public function getLink(Character $character, $mode = self::MODE_PVE) {
        try {
            $client   = $character->getClient();
            $lang     = $client->getLang();
            $cache    = $client->getClientV2()->getRequestManager()->getCache();
            $cacheKey = 'gw2skills-link/' . $lang . '/' . $mode . '/' . $character->getName();
            if ($cache) {
                $url = $cache->get($cacheKey);
                if ($url) {
                    return $url;
                }
            }
            if ($mode == self::MODE_PVE) {
                $build = $character->getBuildPve();
            }
            elseif ($mode == self::MODE_PVP) {
                $build = $character->getBuildPvp();
            }
            elseif ($mode == self::MODE_WVW) {
                $build = $character->getBuildWvw();
            }
            else {
                $mode  = self::MODE_PVE;
                $build = null;
            }

            $data = [
                'bf'   => '0.0',
                'inf'  => $this->getInfusions($character),
                'mode' => $mode,
                'p'    => $this->getProfession($character),
                'pet'  => '0.0.0.0',
                'r'    => $this->getRace($character),
                's'    => '0.0.0.0.0',
                'sa'   => '0.0.0.0.0',
                't'    => $this->getTraits($build),
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
            if (preg_match('!^.*?\n(.*?(?:-[ew])?)(\s|\n|\t|\r|$)!si', $content, $m)) {
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
