<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GetProductLayer
{
    const DEFAULT_VALUE = -99999.99;

    /**
     * @var ProductManager
     */
    private $productManager;

    /**
     * @var CategoryManager
     */
    private $categoryManager;

    /**
     * @var GetCurrencyCode
     */
    private $getCurrencyCode;

    /**
     * @param ProductManager $productManager
     * @param CategoryManager $categoryManager
     * @param GetCurrencyCode $getCurrencyCode
     */
    public function __construct(
        ProductManager $productManager,
        CategoryManager $categoryManager,
        GetCurrencyCode $getCurrencyCode
    ) {
        $this->productManager = $productManager;
        $this->categoryManager = $categoryManager;
        $this->getCurrencyCode = $getCurrencyCode;
    }

    /**
     * @param string $sku
     * @param float $quantity
     * @param float $price
     * @param float $discount
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(
        string $sku,
        float $quantity = self::DEFAULT_VALUE,
        float $price = self::DEFAULT_VALUE,
        float $discount = self::DEFAULT_VALUE
    ): array {
        $data = [];

        $parentSku = $this->productManager->getProductParentSku($sku);
        $category1 = $this->categoryManager->getCategoryLevel1($parentSku);
        $category2 = $this->categoryManager->getCategoryLevel2($parentSku);
        $category3 = $this->categoryManager->getCategoryLevel3($parentSku);
        $category4 = $this->categoryManager->getCategoryLevel4($parentSku);
        $category5 = $this->categoryManager->getCategoryLevel5($parentSku);
        $brand = $this->productManager->getProductAttributeValue($sku, 'manufacturer');
        $variant = $this->productManager->getProductAttributeValue($sku, 'swatch_color');
        $size = $this->productManager->getProductAttributeValue($sku, 'size');

        $data['item_id'] = (string)$parentSku;
        $data['item_name'] = (string)$this->productManager->getProductName($parentSku);
        $data['currency'] = (string)$this->getCurrencyCode->execute();
        $data['price'] = $price !== self::DEFAULT_VALUE ? (float)$price : (float)$this->productManager->getProductPrice($sku);
        $data['item_brand'] = (string)$brand;
        $data['item_category'] = (string)$category1;
        $data['item_category2'] = (string)$category2;
        $data['item_category3'] = (string)$category3;
        $data['item_category4'] = (string)$category4;
        $data['item_category5'] = (string)$category5;
        $data['item_variant'] = (string)$variant;
        $data['item_size'] = (string)$size;

        if ($quantity > 0) {
            $data['quantity'] = (float)$quantity;
        }

        if ($discount === self::DEFAULT_VALUE) {
            $discount = $this->productManager->getProductDiscount($sku);
        }

        if ($discount !== self::DEFAULT_VALUE && $discount !== 0.0) {
            $data['discount'] = (float)$discount;
        }

        return $data;
    }

}
