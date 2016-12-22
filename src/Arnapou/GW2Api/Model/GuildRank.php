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

/**
 * 
 * @method string getId()
 * @method string getOrder()
 * @method string getIcon()
 */
class GuildRank extends AbstractObject {

    protected $permissions = [];

    protected function setData($data) {
        parent::setData($data);

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $env = $this->getEnvironment();
            foreach ($data['permissions'] as $item) {
                $this->permissions[] = new GuildPermission($env, $item);
            }
            usort($this->permissions, function($a, $b) {
                return strcmp((string) $a, (string) $b);
            });
        }
    }

    /**
     * 
     * @return array
     */
    public function getPermissions() {
        return $this->permissions;
    }

}
