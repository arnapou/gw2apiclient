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
class ClientV1 extends AbstractClient {

	/**
	 * 
	 * @return string
	 */
	public function getBaseUrl() {
		return 'https://api.guildwars2.com/v1/';
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiBuild() {
		return $this->request('build.json');
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiColors() {
		return $this->request('colors.json');
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiContinents() {
		return $this->request('continents.json');
	}

	/**
	 * 
	 * @param string $eventId
	 * @return Request
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
	 * @return Request
	 */
	public function apiEventNames() {
		return $this->request('event_names.json');
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiFiles() {
		return $this->request('files.json');
	}

	/**
	 * 
	 * @param string $guildId
	 * @param string $guildName
	 * @return Request
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
	 * @return Request
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
	 * @return Request
	 */
	public function apiItems() {
		return $this->request('items.json');
	}

	/**
	 * 
	 * @param integer $continentId
	 * @param integer $floor
	 * @return Request
	 */
	public function apiMapFloor($continentId, $floor) {
		$parameters = [
			'continent_id' => $continentId,
			'floor' => $floor,
		];
		return $this->request('map_floor.json', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiMapNames() {
		return $this->request('map_names.json');
	}

	/**
	 * 
	 * @param int $mapId
	 * @return Request
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
	 * @return Request
	 */
	public function apiRecipeDetails($recipeId) {
		$parameters = [
			'recipe_id' => $recipeId,
		];
		return $this->request('recipe_details.json', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiRecipes() {
		return $this->request('recipes.json');
	}

	/**
	 * 
	 * @param int $skinId
	 * @return Request
	 */
	public function apiSkinDetails($skinId) {
		$parameters = [
			'skin_id' => $skinId,
		];
		return $this->request('skin_details.json', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiSkins() {
		return $this->request('skins.json');
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiWorldNames() {
		return $this->request('world_names.json');
	}

}
