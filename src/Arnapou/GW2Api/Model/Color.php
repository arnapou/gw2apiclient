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
class Color extends AbstractObject {

    // CATEGORIES
    const CATEGORY_CLOTH   = 'cloth';
    const CATEGORY_LEATHER = 'leather';
    const CATEGORY_METAL   = 'metal';

    /**
     *
     * @var array
     */
    protected $attributes;

    /**
     *
     * @var boolean
     */
    protected $unlocked;

    /**
     * 
     * @param SimpleClient $client
     * @param array $id
     * @param boolean $unlocked
     */
    public function __construct(SimpleClient $client, $id, $unlocked = false) {
        parent::__construct($client);

        $this->data     = $this->apiColors($id);
        $this->unlocked = $unlocked;
    }

    /**
     * 
     * @return boolean
     */
    public function isUnlocked() {
        return $this->unlocked;
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->getSubkey(['name']);
    }

    /**
     * 
     * @return array
     */
    public function getBaseRgb() {
        return $this->getSubkey(['base_rgb']);
    }

    /**
     * 
     * @return string
     */
    public function getBaseRgbHex() {
        $rgb = $this->getBaseRgb();
        if (is_array($rgb)) {
            return '#' . sprintf('%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
        }
        return '#000000';
    }

    /**
     * 
     * @return float
     */
    public function getCategoryBrightness($category) {
        return $this->getSubkey([$category, 'brightness']);
    }

    /**
     * 
     * @return float
     */
    public function getCategoryContrast($category) {
        return $this->getSubkey([$category, 'contrast']);
    }

    /**
     * 
     * @return float
     */
    public function getCategoryHue($category) {
        return $this->getSubkey([$category, 'hue']);
    }

    /**
     * 
     * @return float
     */
    public function getCategorySaturation($category) {
        return $this->getSubkey([$category, 'saturation']);
    }

    /**
     * 
     * @return float
     */
    public function getCategoryLightness($category) {
        return $this->getSubkey([$category, 'lightness']);
    }

    /**
     * 
     * @return array
     */
    public function getCategoryRgb($category) {
        return $this->getSubkey([$category, 'rgb']);
    }
    /**
     * 
     * @return string
     */
    public function getCategoryRgbHex($category) {
        $rgb = $this->getCategoryRgb($category);
        if (is_array($rgb)) {
            return '#' . sprintf('%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
        }
        return '#000000';
    }

}
