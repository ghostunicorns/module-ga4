<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\SessionException;

class GetCartLayer
{
    /**
     * @var GetCurrencyCode
     */
    private $getCurrencyCode;

    /**
     * @var GetProductLayer
     */
    private $getProductLayer;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @param GetProductLayer $getProductLayer
     * @param GetCurrencyCode $getCurrencyCode
     * @param Session $checkoutSession
     */
    public function __construct(
        GetProductLayer $getProductLayer,
        GetCurrencyCode $getCurrencyCode,
        Session $checkoutSession
    ) {
        $this->getProductLayer = $getProductLayer;
        $this->getCurrencyCode = $getCurrencyCode;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param bool $isMiniCart
     * @return array
     */
    public function execute(
        bool $isMiniCart = false
    ): array {
        try {
            $quote = $this->getCheckoutSession()->getQuote();
        } catch (\Exception $e) {
            unset($e);
            return [];
        }

        $cartProducts = $quote->getAllVisibleItems();
        $cartTotal = $quote->getGrandTotal();

        $products = [];
        foreach ($cartProducts as $cartProduct) {
            try {
                $products[] = $this->getProductLayer->execute(
                    $cartProduct->getSku(),
                    (float)$cartProduct->getQty(),
                    (float)$cartProduct->getPriceInclTax(),
                    (float)$cartProduct->getDiscountAmount()
                );
            } catch (\Exception $e) {
                unset($e);
            }
        }

        return [
            'event' => 'view_cart',
            'cart_type' => $isMiniCart ? 'mini_cart' : 'cart',
            'ecommerce' => [
                'currency' => $this->getCurrencyCode->execute(),
                'value' => (float)$cartTotal,
                'items' => $products
            ]
        ];
    }

    /**
     * @return Session
     * @throws SessionException
     */
    private function getCheckoutSession(): Session
    {
        if (!$this->checkoutSession->isSessionExists()) {
            $this->checkoutSession->start();
        }
        return $this->checkoutSession;
    }
}
