<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Plugin;

use GhostUnicorns\Ga4\Model\GetProductLayer;
use Magento\Checkout\CustomerData\Cart;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class AddProductInfoToCustomerDataCart
{
    /**
     * @var GetProductLayer
     */
    private $getProductLayer;

    /**
     * @param GetProductLayer $getProductLayer
     */
    public function __construct(
        GetProductLayer $getProductLayer
    ) {
        $this->getProductLayer = $getProductLayer;
    }

    /**
     * @param Cart $subject
     * @param array $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetSectionData(Cart $subject, array $result): array
    {
        if (is_array($result['items'])) {
            foreach ($result['items'] as $key => $itemAsArray) {
                $sku = $result['items'][$key]['product_sku'];
                $productLayer = $this->getProductLayer->execute($sku);
                $result['items'][$key] = array_merge($result['items'][$key], $productLayer);
            }
        }

        return $result;
    }
}
