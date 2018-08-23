<?php

namespace Taxjar\SalesTax\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Job extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('taxjar_queue', 'id');
    }
}
