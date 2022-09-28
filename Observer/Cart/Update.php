<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Observer\Cart;

use GhostUnicorns\Ga4\Model\CategoryManager;
use GhostUnicorns\Ga4\Model\Event\GetCartUpdateEvents;
use GhostUnicorns\Ga4\Model\GetProductLayer;
use GhostUnicorns\Ga4\Model\ProductManager;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;

class Update implements ObserverInterface
{
	/**
	 * @var CheckoutSession
	 */
	protected $checkoutSession;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ProductManager
     */
    private $productManager;

    /**
     * @var CategoryManager
     */
    private $categoryManager;

    /**
     * @var GetProductLayer
     */
    private $getProductLayer;

    /**
     * @param CheckoutSession $checkoutSession
     * @param SerializerInterface $serializer
     * @param ProductManager $productManager
     * @param CategoryManager $categoryManager
     * @param GetProductLayer $getProductLayer
     */
	public function __construct (
		CheckoutSession $checkoutSession,
        SerializerInterface $serializer,
        ProductManager $productManager,
        CategoryManager $categoryManager,
        GetProductLayer $getProductLayer
	) {
		$this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->productManager = $productManager;
        $this->categoryManager = $categoryManager;
        $this->getProductLayer = $getProductLayer;
    }

    /**
     * @param EventObserver $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
	public function execute(EventObserver $observer)
	{
		$cartItems = $observer->getRequest()->getParam('cart');
		if (!$cartItems) {
            return;
        }

        $data = [
            'add' => [],
            'remove' => []
        ];
        $finalData = [];

        foreach ($cartItems as $key => $cartItem) {
            if (!isset($cartItem['qty'])) {
                continue;
            }

            $quantity = (float) $cartItem['qty'];
            $item = $this->checkoutSession->getQuote()->getItemById($key);

            if (!$item || !$item->getId() || $quantity === (float)$item->getQty()) {
                continue;
            }

            $product = $item->getProduct();
            $qty = $quantity > $item->getQty() ? ($quantity - $item->getQty()) :  ($item->getQty() - $quantity);

            $productData = $this->getProductLayer->execute(
                $product->getSku(),
                $qty,
                (float)$item->getPriceInclTax(),
                (float)$item->getDiscountAmount()
            );

            if ($quantity > $item->getQty()) {
                $data['add'][] = $productData;
            } else {
                $data['remove'][] = $productData;
            }
        }

        if (count($data['add'])) {
            $finalData[] = [
                'event' => 'add_to_cart',
                'ecommerce' => [
                    'items' => $data['add']
                ]
            ];
        }

        if (count($data['remove'])) {
            $finalData[] = [
                'event' => 'remove_to_cart',
                'ecommerce' => [
                    'items' => $data['remove']
                ]
            ];
        }

		if ($finalData) {
			$this->checkoutSession->setData(GetCartUpdateEvents::EVENT_NAME, $this->serializer->serialize($finalData));
		}
	}
}
