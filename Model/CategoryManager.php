<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Category as ResourceCategory;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;

class CategoryManager
{
    /**
     * @var Session
     */
    private $catalogSession;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var ResourceCategory
     */
    private $resourceCategory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ResourceProduct
     */
    private $resourceProduct;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetProductFromSession
     */
    private $getProductFromSession;

    /**
     * @param Session $catalogSession
     * @param CategoryFactory $categoryFactory
     * @param ResourceCategory $resourceCategory
     * @param ProductFactory $productFactory
     * @param ResourceProduct $resourceProduct
     * @param RequestInterface $request
     * @param GetProductFromSession $getProductFromSession
     */
    public function __construct(
        Session $catalogSession,
        CategoryFactory $categoryFactory,
        ResourceCategory $resourceCategory,
        ProductFactory $productFactory,
        ResourceProduct $resourceProduct,
        RequestInterface $request,
        GetProductFromSession $getProductFromSession
    ) {
        $this->catalogSession = $catalogSession;
        $this->categoryFactory = $categoryFactory;
        $this->resourceCategory = $resourceCategory;
        $this->productFactory = $productFactory;
        $this->resourceProduct = $resourceProduct;
        $this->request = $request;
        $this->getProductFromSession = $getProductFromSession;
    }

    /**
     * @param string $sku
     * @return Category
     * @throws Exception
     */
    public function getCurrentCategory(string $sku = ''): Category
    {
        return $this->getCategoryByProductSku($sku);
//        if ($sku !== '') {
//            return $this->getCategoryByProductSku($sku);
//        }
//        switch ($this->request->getControllerName()) {
//            case GetPageType::CONTROLLER_CATEGORY:
//                $currentCategoryId = $this->catalogSession->getData('last_viewed_category_id');
//                $category = $this->categoryFactory->create();
//                $this->resourceCategory->load($category, $currentCategoryId);
//                return $category;
//            case GetPageType::CONTROLLER_PRODUCT:
//                $product = $this->getProductFromSession->execute();
//                return $this->getCategoryByProductSku($product->getSku());
//            default:
//                throw new Exception('No category found');
//        }
    }

    /**
     * @throws LocalizedException
     */
    private function getCategoryByProductSku(string $sku): Category
    {
        $product = $this->productFactory->create();
        $productId = $this->resourceProduct->getIdBySku($sku);
        $this->resourceProduct->load($product, $productId);
        $categories = $product->getCategoryCollection();
        $categoriesFirst = $categories->getFirstItem();
        $categoryId = (int)$categoriesFirst->getId();
        try {
            return $this->getCategoryByCategoryId($categoryId);
        } catch (LocalizedException $e) {
            unset($e);
            throw new LocalizedException(__('Product with sku %1 has no category', $sku));
        }
    }

    /**
     * @throws LocalizedException
     */
    private function getCategoryByCategoryId(int $categoryId): Category
    {
        $category = $this->categoryFactory->create();
        $this->resourceCategory->load($category, $categoryId);
        if (!$category->getId()) {
            throw new LocalizedException(__('Category with id %1 not found', $categoryId));
        }
        return $category;
    }

    /**
     * @return string
     */
    public function getCurrentArea(): string
    {
        return match ($this->request->getControllerName()) {
            GetPageType::CONTROLLER_HOME => 'home',
            GetPageType::CONTROLLER_RESULT => 'search',
            default => '',
        };
    }

    /**
     * @param string $sku
     * @param int $level
     * @return string
     */
    public function getCategoryLevel(int $level, string $sku = ''): string
    {
        try {
            $currentCategory = $this->getCurrentCategory($sku);
        } catch (Exception $e) {
            unset($e);
            return $this->getCurrentArea();
        }

        $categoryTree = $currentCategory->getParentCategories();

        if (!is_array($categoryTree)) {
            return '';
        }

        return $this->getCategoryFromTreeByLevel($categoryTree, $level);
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getCategoryLevel1(string $sku = ''): string
    {
        return $this->getCategoryLevel(0, $sku);
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getCategoryLevel2(string $sku = ''): string
    {
        return $this->getCategoryLevel(1, $sku);
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getCategoryLevel3(string $sku = ''): string
    {
        return $this->getCategoryLevel(2, $sku);
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getCategoryLevel4(string $sku = ''): string
    {
        return $this->getCategoryLevel(3, $sku);
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getCategoryLevel5(string $sku = ''): string
    {
        return $this->getCategoryLevel(4, $sku);
    }

    /**
     * @param $categoryTree
     * @param int $categoryLevel
     * @return string
     */
    public function getCategoryFromTreeByLevel($categoryTree, int $categoryLevel): string
    {
        $categoryCount = count($categoryTree);
        if ($categoryCount >= $categoryLevel) {

            $i = 0;
            $noName = false;
            foreach ($categoryTree as $categoryItem) {
                $category = $categoryItem;

                if ($categoryLevel == $i) {
                    break;
                }

                $i++;

                if (count($categoryTree) === $i) {
                    $noName = true;
                }
            }

            if ($noName) {
                return '';
            }

            return $category->getName();
        }

        return '';
    }
}
