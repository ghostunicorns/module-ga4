<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Framework\App\RequestInterface;

class GetPageType
{
    const CONTROLLER_HOME = 'index';
    const CONTROLLER_CART = 'cart';
    const CONTROLLER_CATEGORY = 'category';
    const CONTROLLER_PRODUCT = 'product';
    const CONTROLLER_RESULT = 'result';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function execute(): string
    {
        $controllerName = $this->request->getControllerName();
        return match ($controllerName) {
            self::CONTROLLER_HOME => 'home',
            self::CONTROLLER_CART => 'cart',
            self::CONTROLLER_CATEGORY => 'category',
            self::CONTROLLER_PRODUCT => 'product',
            self::CONTROLLER_RESULT => 'searchresults',
            default => 'other',
        };
    }

}
