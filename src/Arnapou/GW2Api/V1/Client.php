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

	const BASEURL = 'https://api.guildwars2.com/v1/';

	/**
	 * 
	 * @link https://wiki.guildwars2.com/wiki/API:1/maps
	 * @param int $mapId
	 * @return array
	 */
	public function apiMaps($mapId = null) {
		$parameters = [];
		if ($mapId) {
			$parameters['map_id'] = $mapId;
		}
		return $this->request(self::BASEURL . 'maps.json', $parameters);
	}

}
