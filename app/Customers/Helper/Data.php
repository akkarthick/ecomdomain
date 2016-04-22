<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
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
 * @category    Compucom
 * @package     Compucom_Customers
 * @copyright Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * Catalog category helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Compucom_Customers_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * @var boolean
     */
    public function getCustomerId($liferayId)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        //$userId = $readConnection->fetchOne('SELECT entity_id FROM customer_entity_varchar WHERE entity_type_id = 1 AND attribute_id = 173 AND value = "'.$liferayId.'"');
        $userId = $readConnection->fetchOne('SELECT entity_id FROM customer_entity_varchar WHERE entity_type_id = 1 AND attribute_id = 174 AND value = "'.$liferayId.'"');
        return $userId;
    }
}