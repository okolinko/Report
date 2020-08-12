<?php
namespace Toppik\Report\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {
	
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
		
        if(version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->upgradeTo_1_0_1($setup, $context);
        }
		
        if(version_compare($context->getVersion(), '1.0.2') < 0) {
            $this->upgradeTo_1_0_2($setup, $context);
        }
		
        if(version_compare($context->getVersion(), '1.0.3') < 0) {
            $this->upgradeTo_1_0_3($setup, $context);
        }
		
        if(version_compare($context->getVersion(), '1.0.4') < 0) {
            $this->upgradeTo_1_0_4($setup, $context);
        }
		
        if(version_compare($context->getVersion(), '1.0.5') < 0) {
            $this->upgradeTo_1_0_5($setup, $context);
        }
		
        if(version_compare($context->getVersion(), '1.0.6') < 0) {
            $this->upgradeTo_1_0_6($setup, $context);
        }
		
        $setup->endSetup();
    }
	
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function upgradeTo_1_0_1(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
		
		$table = $setup->getConnection()
			->newTable($setup->getTable('subscriptions_report'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
			)
			->addColumn(
				'period_from',
				\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
				null,
				['nullable' => false]
			)
			->addColumn(
				'period_to',
				\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
				null,
				['nullable' => false]
			)
			->addColumn(
				'order_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'order_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'subscription_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'subscription_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'percentage_subscription',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'web_order_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'web_order_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'cs_order_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'cs_order_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'web_subscription_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'web_subscription_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'cs_subscription_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'cs_subscription_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'autoship_subscription_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'autoship_subscription_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'cancelled_subscription_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'cancelled_subscription_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'suspended_subscription_count',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'suspended_subscription_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			);
		
		$setup->getConnection()->createTable($table);
		
        $installer->endSetup();
    }
	
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function upgradeTo_1_0_2(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
		
		$table = $setup->getConnection()
			->newTable($setup->getTable('toppikreport_daily_system'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
			)
			->addColumn(
				'created_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
				null,
				['nullable' => false]
			)
			->addColumn(
				'entity_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'entity_type',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'message',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'email_sent',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			);
		
		$setup->getConnection()->createTable($table);
		
        $installer->endSetup();
    }
	
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function upgradeTo_1_0_3(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->getConnection()
            ->addColumn(
                'toppikreport_daily_system',
                'amount',
                'DECIMAL(12.4) UNSIGNED DEFAULT NULL COMMENT "Amount" AFTER message'
            );
		
        $setup->getConnection()
            ->addColumn(
                'toppikreport_daily_system',
                'customer_id',
                'INT(10) UNSIGNED DEFAULT NULL COMMENT "Customer ID" AFTER amount'
            );
		
        $setup->getConnection()
            ->addColumn(
                'toppikreport_daily_system',
                'customer_name',
                'VARCHAR(255) NOT NULL DEFAULT "" COMMENT "Customer Name" AFTER customer_id'
            );
		
        $setup->getConnection()
            ->addColumn(
                'toppikreport_daily_system',
                'customer_email',
                'VARCHAR(255) NOT NULL DEFAULT "" COMMENT "Customer Email" AFTER customer_name'
            );
		
        $setup->getConnection()
            ->addColumn(
                'toppikreport_daily_system',
                'customer_phone',
                'VARCHAR(255) NOT NULL DEFAULT "" COMMENT "Customer Phone" AFTER customer_email'
            );
    }
	
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function upgradeTo_1_0_4(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
		
		$table = $setup->getConnection()
			->newTable($setup->getTable('subscription_report_future'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
			)
			->addColumn(
				'date',
				\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
				null,
				['nullable' => false]
			)
			->addColumn(
				'subscription_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false]
			)
			->addColumn(
				'subscription_period',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false]
			)
			->addColumn(
				'subscription_total',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'subscription_merchant_source',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			);
        
		$setup->getConnection()->createTable($table);
		
		$table = $setup->getConnection()
			->newTable($setup->getTable('subscription_report_future_aggregated'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
			)
			->addColumn(
				'date',
				\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
				null,
				['nullable' => false]
			)
			->addColumn(
				'subscription_count_toppik',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'subscription_total_toppik',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'subscription_count_ms',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'subscription_total_ms',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'12,2',
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			);
        
		$setup->getConnection()->createTable($table);
		
        $installer->endSetup();
    }
	
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function upgradeTo_1_0_5(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
		
		$table = $setup->getConnection()
			->newTable($setup->getTable('subscription_report_monthly_sum'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
			)
			->addColumn(
				'period',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'count_active_toppik',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_active_toppik',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'count_active_ms',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_active_ms',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'count_active',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_active',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			);
        
		$setup->getConnection()->createTable($table);
		
		$table = $setup->getConnection()
			->newTable($setup->getTable('subscription_report_pivot'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
			)
			->addColumn(
				'period',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'count_active',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_active',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'count_suspended',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_suspended',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'count_cancelled',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_cancelled',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'count_new_month',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_new_month',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'count_suspended_month',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_suspended_month',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			)
			->addColumn(
				'count_cancelled_month',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0]
			)
			->addColumn(
				'sum_cancelled_month',
				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => 0.00]
			);
        
		$setup->getConnection()->createTable($table);
		
        $installer->endSetup();
    }
	
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function upgradeTo_1_0_6(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
		
        $setup->getConnection()
            ->addColumn(
                'toppikreport_daily_system',
                'fixed',
                'TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER customer_phone'
            );
        
		$table = $setup->getConnection()
			->newTable($setup->getTable(\Toppik\Report\Model\ResourceModel\Report\System\Integrations\Entity\Types::MAIN_TABLE))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
			)
			->addColumn(
				'entity_type_code',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'entity_type_label',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'entity_type_description',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'admin_ids',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			)
			->addColumn(
				'admin_emails',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => false]
			);
        
		$setup->getConnection()->createTable($table);
		
        $installer->endSetup();
    }
	
}
