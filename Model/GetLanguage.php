<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Framework\Locale\Resolver;

class GetLanguage
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(
        Resolver $resolver
    ) {
        $this->resolver = $resolver;
    }

    /**
     * @return string
     */
    public function execute(): string
    {
        return $this->resolver->getLocale();
    }
}
