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

    protected static $ALL_PERMISSIONS_IDS;
    protected $permissions   = [];
    protected $permissionIds = [];

    protected function setData($data) {
        parent::setData($data);

        $env = $this->getEnvironment();
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->permissionIds = $data['permissions'];
        }
        $allIds = $this->getAllPermissionIds();
        foreach ($allIds as $id) {
            $perm                = new GuildPermission($env, $id);
            $perm->setUnlocked(in_array($id, $this->permissionIds));
            $this->permissions[] = $perm;
        }
        usort($this->permissions, function($a, $b) {
            return strcmp((string) $a, (string) $b);
        });
    }

    /**
     * 
     * @return array
     */
    public function getPermissionIds() {
        return $this->permissionIds;
    }

    /**
     * 
     * @return array
     */
    public function getAllPermissionIds() {
        $env = $this->getEnvironment();
        if (!isset(self::$ALL_PERMISSIONS_IDS)) {
            $ids = $env->getClientVersion2()->apiGuildPermissions();
            if (!empty($ids) && is_array($ids)) {
                self::$ALL_PERMISSIONS_IDS = $ids;
            }
        }
        return self::$ALL_PERMISSIONS_IDS;
    }

    /**
     * 
     * @return array
     */
    public function getPermissions() {
        return $this->permissions;
    }

}
