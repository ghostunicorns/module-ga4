<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\ViewModel;

use Exception;
use GhostUnicorns\Ga4\Model\GetProductLayer;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductList implements ArgumentInterface
{
    /**
     * @var GetProductLayer
     */
    private $getProductLayer;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param GetProductLayer $getProductLayer
     * @param SerializerInterface $serializer
     */
    public function __construct(
        GetProductLayer $getProductLayer,
        SerializerInterface $serializer
    ) {
        $this->getProductLayer = $getProductLayer;
        $this->serializer = $serializer;
    }

    /**
     * @param string $sku
     * @param string $position
     * @param string $listName
     * @param string $listPosition
     * @return string
     */
    public function getProductLayerInList(string $sku, string $position, string $listName, string $listPosition): string
    {
        try {
            $product = $this->getProductLayer->execute($sku);
            $product['position'] = $position;
            $product['list'] = $listName;
            $product['list_position'] = $listPosition;
            return $this->serializer->serialize($product);
        } catch (Exception $e) {
            unset($e);
            return '';
        }
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getProductLayer(string $sku): string
    {
        try {
            return $this->serializer->serialize($this->getProductLayer->execute($sku));
        } catch (Exception $e) {
            unset($e);
            return '""';
        }
    }
}
