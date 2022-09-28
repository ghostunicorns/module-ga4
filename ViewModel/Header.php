<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\ViewModel;

use GhostUnicorns\Ga4\Model\CategoryManager;
use GhostUnicorns\Ga4\Model\GetCurrencyCode;
use GhostUnicorns\Ga4\Model\GetProductFromSession;
use GhostUnicorns\Ga4\Model\GetProductLayer;
use GhostUnicorns\Ga4\Model\ProductManager;
use Magento\Bundle\Model\Product\Type as Simple;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class Header implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var GetProductFromSession
     */
    private $getProductFromSession;

    /**
     * @var Collection
     */
    private $optionCollection;

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
     * @var GetProductLayer
     */
    private $getProductLayer;

    /**
     * @param StoreManagerInterface $storeManager
     * @param SerializerInterface $serializer
     * @param GetProductFromSession $getProductFromSession
     * @param Collection $optionCollection
     * @param ProductManager $productManager
     * @param CategoryManager $categoryManager
     * @param GetCurrencyCode $getCurrencyCode
     * @param GetProductLayer $getProductLayer
     */
    public function __construct	(
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        GetProductFromSession $getProductFromSession,
        Collection $optionCollection,
        ProductManager $productManager,
        CategoryManager $categoryManager,
        GetCurrencyCode $getCurrencyCode,
        GetProductLayer $getProductLayer
	) {
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->getProductFromSession = $getProductFromSession;
        $this->optionCollection = $optionCollection;
        $this->productManager = $productManager;
        $this->categoryManager = $categoryManager;
        $this->getCurrencyCode = $getCurrencyCode;
        $this->getProductLayer = $getProductLayer;
    }

    /**
     * @return string
     */
    public function getStoreName(): string
    {
        try {
            return $this->serializer->serialize($this->storeManager->getStore()->getName());
        } catch (\Exception $e) {
            unset($e);
            return '';
        }
    }

    /**
     * @return string
     */
    public function getCurrentCurrencyCode(): string
    {
        return $this->getCurrencyCode->execute();
    }

    /**
     * @return string
     */
    public function getSuper(): string
    {
        $super = [];

        try {
            $product = $this->getProductFromSession->execute();

            if (Configurable::TYPE_CODE == $product->getTypeId()) {
                $attributes = $product->getTypeInstance()->getConfigurableAttributes($product);

                foreach($attributes as $attribute) {
                    $object = $attribute->getProductAttribute();

                    $super[] = [
                        'id' => $object->getAttributeId(),
                        'label' => $attribute->getFrontendLabel(),
                        'code' => $object->getAttributeCode(),
                        'options' => $this->getAttributeOptions($attribute)
                    ];
                }
            }

        } catch (\Exception $e) {
            unset($e);
            return '{}';
        }

        return $this->serializer->serialize($super);
    }


    /**
     * @param string $sku
     * @param string $attributeCode
     * @return string
     */
    public function getProductAttribute(string $sku, string $attributeCode): string
    {
        try {
            $brand = $this->productManager->getProductAttributeValue($sku, $attributeCode);
            return $brand ? (string)$brand : '';
        } catch (\Exception $e) {
            unset($e);
            return '';
        }
    }

    /**
     * @param string $sku
     * @param int $level
     * @return string
     */
    public function getItemCategory(string $sku, int $level): string
    {
        return $this->categoryManager->getCategoryLevel($level, $sku);
    }

    /**
     * @return string
     */
    public function getConfigurableSimples(): string
    {
        $simples = [];

        try {
            $product = $this->getProductFromSession->execute();

            if (Configurable::TYPE_CODE == $product->getTypeId()) {
                foreach ($product->getTypeInstance()->getUsedProducts($product) as $simple) {
                    $simples[$simple->getId()] = $this->getProductLayer->execute($simple->getSku());
                }
            }
        } catch (\Exception $e) {
            unset($e);
            return '{}';
        }

        return $this->serializer->serialize($simples);
    }

    /**
     * @return string
     */
    public function getBundle(): string
    {
        $bundles = [];
        $options = [];

        try {
            $product = $this->getProductFromSession->execute();

            if (Simple::TYPE_CODE === $product->getTypeId()) {
                foreach ($product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product),$product) as $bundle) {
                    $bundles[$bundle->getOptionId()][$bundle->getId()] = $this->getProductLayer->execute($bundle->getSku());
                }

                foreach ($product->getTypeInstance()->getOptionsCollection($product) as $option) {
                    $options[$option->getOptionId()] = [
                        'option_title' => $option->getDefaultTitle(),
                        'option_type'  => $option->getType()
                    ];
                }
            }

        } catch (\Exception $e) {
            unset($e);
            return '{}';
        }

        return $this->serializer->serialize([
            'bundles' => $bundles,
            'options' => $options
        ]);
    }

    /**
     * @param mixed $attribute
     * @return array
     */
    private function getAttributeOptions(mixed $attribute): array
    {
        $options = [];

        foreach ($attribute->getOptions() as $option) {
            $options[] = $option;
        }
        try {
            foreach ($options as &$option) {
                $this->optionCollection->clear();
                $this->optionCollection->getSelect()->reset(\Zend_Db_Select::WHERE);
                $this->optionCollection->getSelect()->where('main_table.option_id IN (?)',[$option['value_index']]);
                $this->optionCollection->getSelect()->group('main_table.option_id');
                $option['admin_label'] = $this->optionCollection->getFirstitem()->getValue();
            }
            unset($option);
        } catch (\Exception $e) {
            unset($e);
            return [];
        }

        return $options;
    }
}
