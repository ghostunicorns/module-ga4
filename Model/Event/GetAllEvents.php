<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model\Event;

use Magento\Framework\Serialize\SerializerInterface;

class GetAllEvents
{
    /**
     * @var GetCustomerRegisterEvent
     */
    private $getCustomerRegisterEvent;

    /**
     * @var GetNewsletterSubscriptionEvent
     */
    private $getNewsletterSubscriptionEvent;

    /**
     * @var GetCartUpdateEvents
     */
    private $getCartUpdateEvents;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var GetCustomerLoginEvent
     */
    private $getCustomerLoginEvent;

    /**
     * @param GetCustomerRegisterEvent $getCustomerRegisterEvent
     * @param GetNewsletterSubscriptionEvent $getNewsletterSubscriptionEvent
     * @param GetCartUpdateEvents $getCartUpdateEvents
     * @param SerializerInterface $serializer
     * @param GetCustomerLoginEvent $getCustomerLoginEvent
     */
    public function __construct(
        GetCustomerRegisterEvent $getCustomerRegisterEvent,
        GetNewsletterSubscriptionEvent $getNewsletterSubscriptionEvent,
        GetCartUpdateEvents $getCartUpdateEvents,
        SerializerInterface $serializer,
        GetCustomerLoginEvent $getCustomerLoginEvent
    ) {
        $this->getCustomerRegisterEvent = $getCustomerRegisterEvent;
        $this->getNewsletterSubscriptionEvent = $getNewsletterSubscriptionEvent;
        $this->getCartUpdateEvents = $getCartUpdateEvents;
        $this->serializer = $serializer;
        $this->getCustomerLoginEvent = $getCustomerLoginEvent;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        $events = [];

        $newsletterSubscriptionEvent = $this->getNewsletterSubscriptionEvent->execute();
        if ($newsletterSubscriptionEvent) {
            $events[] = $newsletterSubscriptionEvent;
        }

        $cartUpdateEvents = $this->getCartUpdateEvents->execute();
        if ($cartUpdateEvents) {
            $cartUpdateEventsArray = $this->serializer->unserialize($cartUpdateEvents);
            foreach ($cartUpdateEventsArray as $cartUpdateEvent) {
                $events[] = $this->serializer->serialize($cartUpdateEvent);
            }
        }

        $customerRegisterEvent = $this->getCustomerRegisterEvent->execute();
        if ($customerRegisterEvent) {
            $events[] = $customerRegisterEvent;
        }

        $customerLoginEvent = $this->getCustomerLoginEvent->execute();
        if ($customerLoginEvent) {
            $events[] = $customerLoginEvent;
        }

//        $contactEvent = $this->getContactEvent->execute();
//        if ($contactEvent) {
//            $events[] = $contactEvent;
//        }

        return $events;
    }
}
