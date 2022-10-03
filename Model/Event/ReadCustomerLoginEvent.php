<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model\Event;

use Magento\Customer\Model\Session;

class ReadCustomerLoginEvent
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function execute(): string
    {
        return $this->session->getData(GetCustomerLoginEvent::EVENT_NAME) ?: '';
    }
}
