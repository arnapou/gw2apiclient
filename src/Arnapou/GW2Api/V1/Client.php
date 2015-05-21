<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\V1;

class Client extends \Arnapou\GW2Api\Core\AbstractClient {

	/**
	 * 
	 * @return string
	 */
	public function getBaseUrl() {
		return 'https://api.guildwars2.com/v1/';
	}

	/**
	 * https://api.guildwars2.com/v1/build.json
	 * @doc https://wiki.guildwars2.com/wiki/API:1/build
	 * @return array
	 */
	public function apiBuild() {
		return $this->request('build.json', [], [], 3600);
	}

	/**
	 * https://api.guildwars2.com/v1/colors.json
	 * @doc https://wiki.guildwars2.com/wiki/API:1/colors
	 * @return array
	 */
	public function apiColors() {
		return $this->request('colors.json', [], [], 86400);
	}

	/**
	 * https://api.guildwars2.com/v1/continents.json
	 * @doc https://wiki.guildwars2.com/wiki/API:1/continents
	 * @return array
	 */
	public function apiContinents() {
		return $this->request('continents.json', [], [], 86400);
	}

	/**
	 * https://api.guildwars2.com/v1/maps.json
	 * @doc https://wiki.guildwars2.com/wiki/API:1/maps
	 * @param int $mapId
	 * @return array
	 */
	public function apiMaps($mapId = null) {
		$parameters = [];
		if ($mapId) {
			$parameters['map_id'] = $mapId;
		}
		return $this->request('maps.json', $parameters, [], 86400);
	}

	/**
	 * https://api.guildwars2.com/v1/event_details.json
	 * @doc https://wiki.guildwars2.com/wiki/API:1/event_details
	 * @param int $eventId
	 * @return array
	 */
	public function apiEventDetails($eventId = null) {
		$parameters = [];
		if ($eventId) {
			$parameters['event_id'] = $eventId;
		}
		return $this->request('event_details.json', $parameters, [], 86400);
	}

}
