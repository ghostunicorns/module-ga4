<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Controller\ShowMiniCart;

use GhostUnicorns\Ga4\Model\GetCartLayer;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Index implements ActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var GetCartLayer
     */
    private $getCartLayer;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param GetCartLayer $getCartLayer
     */
    public function __construct (
        JsonFactory $resultJsonFactory,
        GetCartLayer $getCartLayer
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->getCartLayer = $getCartLayer;
    }

    /**
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        return $this->resultJsonFactory->create()->setData(
            $this->getCartLayer->execute(true)
        );
    }
}
