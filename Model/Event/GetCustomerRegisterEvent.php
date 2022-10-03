<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model\Event;

use Magento\Customer\Model\Session;

class GetCustomerRegisterEvent
{
    /** @var string */
    public const EVENT_NAME = 'customer_register_event';

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
        $event = $this->session->getData(self::EVENT_NAME);

        if ($event) {
            $this->session->unsetData(self::EVENT_NAME);

            return $event;
        }

        return '';
    }
}
