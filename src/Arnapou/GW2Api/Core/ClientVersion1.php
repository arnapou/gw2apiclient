<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Core;

use Arnapou\GW2Api\Exception\Exception;

/**
 * 
 * @doc https://wiki.guildwars2.com/wiki/API:1
 */
class ClientVersion1 extends AbstractClientVersion {

    /**
     * 
     * @return string
     */
    public function getBaseUrl() {
        return 'https://api.guildwars2.com/v1/';
    }

    /**
     * 
     * @return array
     */
    public function apiBuild() {
        return $this->request('build.json');
    }

    /**
     * 
     * @return array
     */
    public function apiColors() {
        return $this->request('colors.json');
    }

    /**
     * 
     * @return array
     */
    public function apiContinents() {
        return $this->request('continents.json');
    }

    /**
     * 
     * @param string $eventId
     * @return array
     */
    public function apiEventDetails($eventId = null) {
        $parameters = [];
        if ($eventId) {
            $parameters['event_id'] = $eventId;
        }
        return $this->request('event_details.json', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiEventNames() {
        throw new Exception('event_names API is deprecated');
    }

    /**
     * 
     * @return array
     */
    public function apiEvents() {
        throw new Exception('events API is deprecated');
    }

    /**
     * 
     * @return array
     */
    public function apiFiles() {
        return $this->request('files.json');
    }

    /**
     * 
     * @param string $guildId
     * @param string $guildName
     * @return array
     */
    public function apiGuildDetails($guildId = null, $guildName = null) {
        $parameters = [];
        if ($guildId) {
            $parameters['guild_id'] = $guildId;
        }
        if ($guildName) {
            $parameters['guild_name'] = $guildName;
        }
        if (empty($parameters)) {
            throw new Exception('You should use at least one parameter');
        }
        return $this->request('guild_details.json', $parameters);
    }

    /**
     * 
     * @param int $itemId
     * @return array
     */
    public function apiItemDetails($itemId = null) {
        $parameters = [];
        if ($itemId) {
            $parameters['item_id'] = $itemId;
        }
        return $this->request('item_details.json', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiItems() {
        return $this->request('items.json');
    }

    /**
     * 
     * @param integer $continentId
     * @param integer $floor
     * @return array
     */
    public function apiMapFloor($continentId, $floor) {
        $parameters = [
            'continent_id' => $continentId,
            'floor'        => $floor,
        ];
        return $this->request('map_floor.json', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiMapNames() {
        return $this->request('map_names.json');
    }

    /**
     * 
     * @param int $mapId
     * @return array
     */
    public function apiMaps($mapId = null) {
        $parameters = [];
        if ($mapId) {
            $parameters['map_id'] = $mapId;
        }
        return $this->request('maps.json', $parameters);
    }

    /**
     * 
     * @param int $recipeId
     * @return array
     */
    public function apiRecipeDetails($recipeId) {
        $parameters = [
            'recipe_id' => $recipeId,
        ];
        return $this->request('recipe_details.json', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiRecipes() {
        return $this->request('recipes.json');
    }

    /**
     * 
     * @param int $skinId
     * @return array
     */
    public function apiSkinDetails($skinId) {
        $parameters = [
            'skin_id' => $skinId,
        ];
        return $this->request('skin_details.json', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiSkins() {
        return $this->request('skins.json');
    }

    /**
     * 
     * @return array
     */
    public function apiWorldNames() {
        return $this->request('world_names.json');
    }

    /**
     * 
     * @param string $matchId
     * @return array
     */
    public function apiWvwMatchDetails($matchId) {
        $parameters = [
            'match_id' => $matchId,
        ];
        return $this->request('wvw/match_details.json', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiWvwMatches() {
        return $this->request('wvw/matches.json');
    }

    /**
     * 
     * @return array
     */
    public function apiWvwObjectiveNames() {
        return $this->request('wvw/objective_names.json');
    }

}
