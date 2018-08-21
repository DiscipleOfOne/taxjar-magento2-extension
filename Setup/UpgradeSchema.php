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

namespace Taxjar\SalesTax\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.7.0') < 0) {
            $installer = $setup;
            $installer->startSetup();

            /**
            * Update table 'sales_order'
            */
            $installer->getConnection('sales')->addColumn(
                $installer->getTable('sales_order'),
                'tj_salestax_sync_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Order sync date for TaxJar'
                ]
            );

            /**
            * Update table 'sales_credit_memo'
            */
            $installer->getConnection('sales')->addColumn(
                $installer->getTable('sales_creditmemo'),
                'tj_salestax_sync_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Refund sync date for TaxJar'
                ]
            );

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $installer = $setup;
            $installer->startSetup();

            /**
            * Update table 'tax_nexus'
            */
            $installer->getConnection()->addColumn(
                $installer->getTable('tax_nexus'),
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'default' => 0,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Store ID'
                ]
            );

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.2.0' < 0)) {
            $installer = $setup;
            $installer->startSetup();

            /**
             * Create table 'taxjar_queue'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('taxjar_queue'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID')
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Store ID'
                )
                ->addColumn(
                    'method',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Queue method')
                ->addColumn(
                    'args',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Type ID')
                ->addColumn(
                    'class',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Class To Instantiate')
                ->addColumn(
                    'hash',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    40,
                    ['nullable' => false],
                    'Has options')
                ->addColumn(
                    'queue',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Required options')
                ->addColumn(
                    'priority',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [],
                    'Queue priority')
                ->addColumn(
                    'attempts',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [],
                    'Attempts to run queue')
                ->addColumn(
                    'run_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    [],
                    'Initial run time')
                ->addColumn(
                    'locked_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    [],
                    'Locked at time')
                ->addColumn(
                    'locked_by',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Locked by pid')
                ->addColumn(
                    'error',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [],
                    'Next run time')
                ->addColumn(
                    'next_run_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    [],
                    'Next run time')
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    [],
                    'Time Created')
                ->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Time Updated'
                )
                ->setComment('Taxjar Queue Table');
            $installer->getConnection()->createTable($table);
            $installer->getConnection()->addIndex(
                $setup->getTable('taxjar_queue'),
                $setup->getIdxName('taxjar_queue', ['next_run_at']),
                ['next_run_at']
            );
            $installer->getConnection()->addIndex(
                $setup->getTable('taxjar_queue'),
                $setup->getIdxName('taxjar_queue', ['attempts']),
                ['attempts']
            );
            $installer->getConnection()->addIndex(
                $setup->getTable('taxjar_queue'),
                $setup->getIdxName('taxjar_queue', ['priority']),
                ['priority']
            );
            $installer->endSetup();
        }
    }
}
