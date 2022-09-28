<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Framework\App\RequestInterface;

class GetSiteArea
{
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
            'account' => 'account',
            default => 'ecommerce',
        };
    }

}
