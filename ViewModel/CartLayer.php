<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\ViewModel;

use GhostUnicorns\Ga4\Model\GetCartLayer;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CartLayer implements ArgumentInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var GetCartLayer
     */
    private $getCartLayer;

    /**
     * @param SerializerInterface $serializer
     * @param GetCartLayer $getCartLayer
     */
    public function __construct(
        SerializerInterface $serializer,
        GetCartLayer $getCartLayer
    ) {
        $this->serializer = $serializer;
        $this->getCartLayer = $getCartLayer;
    }

    /**
     * @return string
     */
    public function getCartLayers(): string
    {
        return $this->serializer->serialize($this->getCartLayer->execute());
    }
}
