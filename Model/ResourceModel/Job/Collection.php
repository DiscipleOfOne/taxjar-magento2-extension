<?php
namespace Taxjar\SalesTax\Model\ResourceModel\Job;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class Collection extends AbstractCollection
{
    /**
     * Define Model and ResourceModel
     */
    protected function _construct()
    {
        $this->_init(
            'Taxjar\SalesTax\Model\Job',
            'Taxjar\SalesTax\Model\ResourceModel\Job'
        );
    }
}
