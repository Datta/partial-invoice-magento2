<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Datta\PartialInvoice\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Setup\ModuleDataSetupInterface;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_order;
     protected  $_resource;
    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\ResourceConnection $resource,
        ModuleDataSetupInterface $setup,
        array $data = []
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_order = $order;
        $this->_resource = $resource;
        $this->_setup = $setup;
        return parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
     
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try{
        $orderId = $this->getRequest()->getParam('orderid');
        $partial_amount = $this->getRequest()->getParam('parAmt');
        $order = $this->_order->load($orderId);
        
            $this->_setup->startSetup();
            $tableName = $this->_setup->getTable('partial_invoice');
            $data = [
                    [
                        'order_id' =>$orderId ,
                        'partial_invoice_amount' => $partial_amount,
                    ],
                ];

            $data = $this->_setup->getConnection()->insertMultiple($tableName, $data);
            $this->_setup->endSetup();
            $this->messageManager->addSuccessMessage(__("Partial Invoice Generated"));

        } catch (\Exception) {
            $this->messageManager->addErrorMessage(__("Something went wrong, please try again OR Partial Invoice already been generated"));
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;

    }
}

