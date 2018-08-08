<?php

/**
 * John Nguyen
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class JohnNguyen_Shell_Coupon extends Mage_Shell_Abstract {

    /**
     * Run script
     *
     */
    public function run() {
        $saleRuleId = '';
        if ($this->getArg('createCoupon')) {
            $saleRuleId = 43;
            $this->createCoupon($saleRuleId);
        }
    }

    public function createCoupon($saleRuleId) {
        $promo_rule = Mage::getModel('salesrule/rule')->load($saleRuleId);
        $coupon_format = array(
            'count' => 10,
            'format' => 'alphanumeric',
            'dash_every_x_characters' => 4,
            'prefix' => 'PRE',
            'suffix' => 'SUF',
            'length' => 12
        );
        $generator = Mage::getModel('salesrule/coupon_massgenerator');

        if (!empty($coupon_format['format'])) {
            switch (strtolower($coupon_format['format'])) {
                case 'alphanumeric':
                    $generator->setFormat(Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC);
                    break;
                case 'alphabetical':
                    $generator->setFormat(Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHABETICAL);
                    break;
                case 'numeric':
                    $generator->setFormat(Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_NUMERIC);
                    break;
            }
        }

        $generator->setLength((int) $coupon_format['length']);
        $generator->setPrefix($coupon_format['prefix']);
        $generator->setSuffix($coupon_format['suffix']);
        $generator->setDash((int) $coupon_format['dash_every_x_characters']);
        $promo_rule->setCouponCodeGenerator($generator);
        $promo_rule->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO);
        $count = (int) $coupon_format['count'];
        // Get as many coupons as you required
        $count = (int) $coupon_format['count'];
        $codes = array();
        for ($i = 0; $i < $count; $i++) {
            $coupon = $promo_rule->acquireCoupon();
            $coupon->setUsageLimit(1);
            $coupon->setTimesUsed(0);
            $coupon->setType(1);
            $coupon->save();
            $code = $coupon->getCode();
            $codes[] = $code;
        }

        return $codes;
    }

}

$shell = new JohnNguyen_Shell_Coupon();
$shell->run();
