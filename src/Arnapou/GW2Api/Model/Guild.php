<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Model;

use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Guild extends AbstractObject {

    /**
     * 
     * @param SimpleClient $client
     * @param string $id
     */
    public function __construct(SimpleClient $client, $id) {
        parent::__construct($client);

        $data = $this->client->getClientV1()->apiGuildDetails($id)->execute(self::$cacheDurationApiGuilds)->getData();
        if (!is_array($data)) {
            throw new Exception('Invalid received data.');
        }
        $this->data = $data;
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->data['guild_id'];
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->data['guild_name'];
    }

    /**
     * 
     * @return string
     */
    public function getTag() {
        return $this->data['tag'];
    }

    /**
     * 
     * @return string
     */
    public function getFullname() {
        return $this->getName() . ' [' . $this->getTag() . ']';
    }

    /**
     * 
     * @return boolean
     */
    public function hasEmblem() {
        return !empty($this->getEmblemForegroundId());
    }

    /**
     * 
     * @return int
     */
    public function getEmblemBackgroundId() {
        return $this->getSubkey(['emblem', 'background_id']);
    }

    /**
     * 
     * @return int
     */
    public function getEmblemForegroundId() {
        return $this->getSubkey(['emblem', 'foreground_id']);
    }

    /**
     * 
     * @return array
     */
    public function getEmblemFlags() {
        return $this->getSubkey(['emblem', 'flags']);
    }

    /**
     * 
     * @return int
     */
    public function getEmblemBackgroundColorId() {
        return $this->getSubkey(['emblem', 'background_color_id']);
    }

    /**
     * 
     * @return int
     */
    public function getEmblemForegroundPrimaryColorId() {
        return $this->getSubkey(['emblem', 'foreground_primary_color_id']);
    }

    /**
     * 
     * @return int
     */
    public function getEmblemForegroundSecondaryColorId() {
        return $this->getSubkey(['emblem', 'foreground_secondary_color_id']);
    }

    /**
     * 
     * Thanks to http://guilds.gw2w2w.com/
     * 
     * @link https://github.com/fooey/node-gw2guilds
     * @param integer $size
     * @return string
     */
    public function getIconLinkGw2guildsSvg($size = 256) {
        if (empty($this->data['emblem'])) {
            return null;
        }
        $slug = str_replace(' ', '-', $this->getName());
        return 'http://guilds.gw2w2w.com/guilds/' . rawurlencode($slug) . '/' . $size . ".svg";
    }

    /**
     * 
     * Thanks to http://data.gw2.fr/guild-emblem/
     * 
     * @param integer $size
     * @return string
     */
    public function getIconLinkGw2Png($size = 256) {
        if (empty($this->data['emblem'])) {
            return null;
        }
        return 'http://data.gw2.fr/guild-emblem/name/' . rawurlencode($this->getName()) . '/' . $size . ".png";
    }

}
