<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Plugin;

use Exception;
use GhostUnicorns\Ga4\Model\GetProductLayer;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\GoogleTagManager\Block\ListJson;
use Magento\Quote\Model\Quote\Item;

class GetCartContentPlugin
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var GetProductLayer
     */
    private $getProductLayer;

    /**
     * @param Session $checkoutSession
     * @param SerializerInterface $serializer
     * @param GetProductLayer $getProductLayer
     */
    public function __construct(
        Session $checkoutSession,
        SerializerInterface $serializer,
        GetProductLayer $getProductLayer
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->getProductLayer = $getProductLayer;
    }

    /**
     * @param ListJson $subject
     * @param string $result
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws SessionException
     */
    public function afterGetCartContent(ListJson $subject, string $result): string
    {
        unset($result);

        $cart = [];
        $quote = $this->getCheckoutSession()->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            $cart[]= $this->formatProduct($item);
        }
        return $this->serializer->serialize($cart);
    }

    /**
     * @param ListJson $subject
     * @param string $result
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws SessionException
     */
    public function afterGetCartContentForUpdate(ListJson $subject, string $result): string
    {
        unset($result);

        $cart = [];
        $quote = $this->getCheckoutSession()->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            $cart[$item->getSku()]= $this->formatProduct($item);
        }
        return $this->serializer->serialize($cart);
    }

    /**
     * @return Session
     * @throws SessionException
     */
    private function getCheckoutSession()
    {
        if (!$this->checkoutSession->isSessionExists()) {
            $this->checkoutSession->start();
        }
        return $this->checkoutSession;
    }

    /**
     * @param Item $item
     * @return array
     */
    private function formatProduct($item): array
    {
        $sku = $item->getSku();

        try {
            $product = $this->getProductLayer->execute(
                $sku,
                (float)$item->getQty(),
                (float)$item->getPrice(),
                (float)$item->getDiscountAmount()
            );
        } catch (Exception $e) {
            unset($e);
            $product = [];
        }

        return $product;
    }
}
