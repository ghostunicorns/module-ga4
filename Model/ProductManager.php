<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

class ProductManager {

    /**
     * @var Product
     */
    protected $resourceProduct;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * @param Product $resourceProduct
     * @param ProductFactory $productFactory
     * @param Configurable $configurable
     */
    public function __construct(
        Product $resourceProduct,
        ProductFactory $productFactory,
        Configurable $configurable
    ) {
        $this->resourceProduct = $resourceProduct;
        $this->productFactory = $productFactory;
        $this->configurable = $configurable;
    }

    /**
     * @param string $sku
     * @return float
     * @throws LocalizedException
     */
    public function getProductDiscount(string $sku): float
    {
        if (!$sku) {
            throw new LocalizedException(__('Sku could not be empty'));
        }

        $productId = (int)$this->resourceProduct->getIdBySku($sku);
        $product = $this->getProductById($productId);

        if (!$product->getSpecialPrice()) {
            return 0.0;
        }

        return $product->getPrice() - $product->getSpecialPrice();
    }

    /**
     * @param string $sku
     * @param string $attributeCode
     * @return string|array|bool
     * @throws NoSuchEntityException
     */
    public function getProductAttributeValue(string $sku, string $attributeCode): string|array|bool
    {
        if (!$sku) {
            throw new NoSuchEntityException(__('Sku could not be empty'));
        }

        if (!$attributeCode) {
            throw new NoSuchEntityException(__('Attribute code could not be empty'));
        }

        $productId = (int)$this->resourceProduct->getIdBySku($sku);
        $product = $this->getProductById($productId);

        return $this->resourceProduct->getAttribute($attributeCode)->getFrontend()->getValue($product);
    }

    /**
     * @param int $productId
     * @return \Magento\Catalog\Model\Product
     * @throws LocalizedException
     */
    public function getProductById(int $productId): \Magento\Catalog\Model\Product
    {
        $product = $this->productFactory->create();
        $this->resourceProduct->load($product, $productId);

        if (!$product->getId()) {
            throw new LocalizedException(__('Product id not exist: %1', $productId));
        }
        return $product;
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getProductParentSku(string $sku): string
    {
        $productId = (int)$this->resourceProduct->getIdBySku($sku);
        $parentIds = $this->configurable->getParentIdsByChild($productId);

        if (empty($parentIds)) {
            return $sku;
        }

        $parentId = reset($parentIds);
        $product = $this->productFactory->create();
        $this->resourceProduct->load($product, $parentId);

        if (!$product->getId()) {
            return $sku;
        }

        return $product->getSku();
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getProductName(string $sku): string
    {
        $productId = (int)$this->resourceProduct->getIdBySku($sku);
        $parentIds = $this->configurable->getParentIdsByChild($productId);

        if (!empty($parentIds)) {
            $productId = reset($parentIds);
        }

        $product = $this->productFactory->create();
        $this->resourceProduct->load($product, $productId);

        return $product->getName();
    }

    /**
     * @param string $sku
     * @return float
     */
    public function getProductPrice(string $sku): float
    {
        $productId = (int)$this->resourceProduct->getIdBySku($sku);
        $product = $this->productFactory->create();
        $this->resourceProduct->load($product, $productId);

        return (float)$product->getPrice();
    }
}
