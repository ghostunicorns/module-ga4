<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model\Event;

use Magento\Customer\Model\Session;

class GetNewsletterSubscriptionEvent
{
    /** @var string */
    public const EVENT_NAME = 'newsletter_subscription';

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
