<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Datta\PartialInvoice\Plugin\Backend\Magento\Sales\Controller\Adminhtml\Order\Invoice;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class Save
{
	/**
     * @var Registry
     */
    protected $registry;
    protected $_resource;

    /**
     * @param Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $order,
        ModuleDataSetupInterface $setup,
         \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->registry = $registry;
        $this->orderFactory = $order;
        $this->_setup = $setup;
         $this->_resource = $resource;
    }

    public function afterExecute(\Magento\Sales\Controller\Adminhtml\Order\Invoice\Save $subject, $result)
    {
        

    	$this->_setup->startSetup();
        $tableName = $this->_setup->getTable('partial_invoice');
        // get connection
    $connection     = $this->_resource->getConnection();
    
    $tableName = $this->_resource->getTableName('partial_invoice');
    // Query
    $select_sql = "Select partial_invoice_amount FROM " . $tableName;
    // fetch result
    $results = $connection->fetchAll($select_sql);
    


        $orderId = $subject->getRequest()->getParam('order_id');
        $orders = $this->orderFactory->create();
        $aaa = $orders->load($orderId); 
        //var_dump($aaa);exit;
		$invoice_details = $aaa->getInvoiceCollection();

		foreach ($invoice_details as $invoice)
        {
        	
        	$original =  $invoice->getGrandTotal();
        	$updated  =  $original - $results[0]['partial_invoice_amount'];
        	$invoice->setGrandTotal($updated);
             $invoice_id = $invoice->getIncrementId();
             $invoice->save();
            
        }

        return $result;
    }

}

