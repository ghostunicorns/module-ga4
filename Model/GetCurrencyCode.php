<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class GetCurrencyCode implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct	(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }
    /**
     * @return string
     */
    public function execute(): string
    {
        try {
            return $this->storeManager->getStore()->getCurrentCurrencyCode();
        } catch (\Exception $e) {
            unset($e);
            return '';
        }
    }
}
