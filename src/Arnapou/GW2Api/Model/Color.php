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
 * @method string getName()
 * @method string getCategories()
 * @method string getCloth()
 * @method string getLeather()
 * @method string getMetal()
 * @method string getItem()
 * @method string getBaseRgb()
 */
class Color extends AbstractStoredObject {

    use UnlockTrait;

    // CATEGORIES
    const CATEGORY_HUE_GRAY         = 'Gray';
    const CATEGORY_HUE_BROWN        = 'Brown';
    const CATEGORY_HUE_RED          = 'Red';
    const CATEGORY_HUE_ORANGE       = 'Orange';
    const CATEGORY_HUE_YELLOW       = 'Yellow';
    const CATEGORY_HUE_GREEN        = 'Green';
    const CATEGORY_HUE_BLUE         = 'Blue';
    const CATEGORY_HUE_PURPLE       = 'Purple';
    const CATEGORY_MATERIAL_VIBRANT = 'Vibrant';
    const CATEGORY_MATERIAL_LEATHER = 'Leather';
    const CATEGORY_MATERIAL_METAL   = 'Metal';
    const CATEGORY_RARITY_STARTER   = 'Starter';
    const CATEGORY_RARITY_COMMON    = 'Common';
    const CATEGORY_RARITY_UNCOMMON  = 'Uncommon';
    const CATEGORY_RARITY_RARE      = 'Rare';

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
        return $this->getData([$category, 'brightness']);
    }

    /**
     * 
     * @return float
     */
    public function getCategoryContrast($category) {
        return $this->getData([$category, 'contrast']);
    }

    /**
     * 
     * @return float
     */
    public function getCategoryHue($category) {
        return $this->getData([$category, 'hue']);
    }

    /**
     * 
     * @return float
     */
    public function getCategorySaturation($category) {
        return $this->getData([$category, 'saturation']);
    }

    /**
     * 
     * @return float
     */
    public function getCategoryLightness($category) {
        return $this->getData([$category, 'lightness']);
    }

    /**
     * 
     * @return array
     */
    public function getCategoryRgb($category) {
        return $this->getData([$category, 'rgb']);
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

    public function getApiName() {
        return 'colors';
    }

}
