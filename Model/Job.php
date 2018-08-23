<?php
/**
 * Taxjar_SalesTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Taxjar
 * @package    Taxjar_SalesTax
 * @copyright  Copyright (c) 2017 TaxJar. TaxJar is a trademark of TPS Unlimited, Inc. (http://www.taxjar.com)
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Taxjar\SalesTax\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class job extends AbstractModel
{

    private $objectManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ObjectManagerInterface $objectManager

    ) {
        $this->_init('Taxjar\SalesTax\Model\ResourceModel\Job');
        $this->objectManager = $objectManager;
        parent::__construct($context, $registry);
    }

    public function run()
    {
        $class = $this->getData('class');
        $method = $this->getData('method');
        $args = json_decode($this->getData('args'), true);
        if (class_exists($class)) {
            $object = $this->objectManager->get($class);
            if (method_exists($object, $method)) {
                return call_user_func_array([$object, $method], $args);
            } else {
                throw new Exception("Method {$method} does not exist in class {$class}");
            }
        } else {
            throw new Exception("Class {$class} does not exist");
        }
    }

    public function setNextRunAt()
    {
        $this->setData('run_at', $this->_calculateNextRunAt());
    }

    protected function _calculateNextRunAt()
    {
        $attempts = $this->getAttempts();
        $expMinutes = pow(2, $attempts);
        $nextRun = date("Y-m-d H:i:s", strtotime("+$expMinutes minutes"));
        return $nextRun;
    }

}
