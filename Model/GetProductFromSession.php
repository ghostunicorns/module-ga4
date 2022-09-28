<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Framework\Exception\LocalizedException;

class GetProductFromSession
{
    /**
     * @var CatalogSession
     */
    private $catalogSession;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ResourceProduct
     */
    private $resourceProduct;

    /**
     * @param CatalogSession $catalogSession
     * @param ProductFactory $productFactory
     * @param ResourceProduct $resourceProduct
     */
    public function __construct(
        CatalogSession $catalogSession,
        ProductFactory $productFactory,
        ResourceProduct $resourceProduct
    ) {
        $this->catalogSession = $catalogSession;
        $this->productFactory = $productFactory;
        $this->resourceProduct = $resourceProduct;
    }

    /**
     * @return Product
     * @throws LocalizedException
     */
    public function execute(): Product
    {
        $productId = $this->catalogSession->getData('last_viewed_product_id');
        $product = $this->productFactory->create();
        $this->resourceProduct->load($product, $productId);
        if (!$product->getId()) {
            throw new LocalizedException(__('No product found '));
        }

        return $product;
    }

}
