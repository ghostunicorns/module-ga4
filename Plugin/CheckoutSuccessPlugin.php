<?php

namespace GhostUnicorns\Ga4\Plugin;

use GhostUnicorns\Ga4\Model\GetCurrencyCode;
use GhostUnicorns\Ga4\Model\GetProductLayer;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GoogleTagManager\Block\GtagGa;
use Magento\Sales\Model\OrderRepository;

class CheckoutSuccessPlugin
{
    /**
     * @var GetProductLayer
     */
    private $getProductLayer;

    /**
     * @var GetCurrencyCode
     */
    private $getCurrencyCode;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param GetProductLayer $getProductLayer
     * @param GetCurrencyCode $getCurrencyCode
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct
    (
        GetProductLayer $getProductLayer,
        GetCurrencyCode $getCurrencyCode,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->getProductLayer = $getProductLayer;
        $this->getCurrencyCode = $getCurrencyCode;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param GtagGa $subject
     * @param array $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetOrdersDataArray(GtagGa $subject, array $result): array
    {
        unset($result);
        $result = [];
        $orderIds = $subject->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return $result;
        }
        $this->searchCriteriaBuilder->addFilter(
            'entity_id',
            $orderIds,
            'in'
        );
        $collection = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection->getItems() as $order) {
            $products = [];
            /** @var \Magento\Sales\Model\Order\Item $item*/
            foreach ($order->getAllVisibleItems() as $item) {
                $products[] = $this->getProductLayer->execute($item->getSku(), $item->getQtyOrdered(), $item->getPriceInclTax(), $item->getDiscountAmount());
            }

            $result[] = [
                'event' => 'purchase',
                'ecommerce' => [
                    'currency' => $this->getCurrencyCode->execute(),
                    'value' => (float)$order->getGrandTotal(),
                    'tax' => (float)$order->getTaxAmount(),
                    'shipping' => (float)$order->getShippingInclTax(),
                    'transaction_id' => $order->getIncrementId(),
                    'coupon' => (string)$order->getCouponCode(),
                    'payment_type' => $order->getPayment()->getMethod(),
                    'shipping_tier' => $order->getShippingMethod(),
                    'items' => $products
                ]
            ];
        }
        return $result;
    }
}
