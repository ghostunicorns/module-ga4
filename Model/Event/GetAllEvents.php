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
     * @param GetCustomerRegisterEvent $getCustomerRegisterEvent
     * @param GetNewsletterSubscriptionEvent $getNewsletterSubscriptionEvent
     * @param GetCartUpdateEvents $getCartUpdateEvents
     * @param SerializerInterface $serializer
     */
    public function __construct(
        GetCustomerRegisterEvent $getCustomerRegisterEvent,
        GetNewsletterSubscriptionEvent $getNewsletterSubscriptionEvent,
        GetCartUpdateEvents $getCartUpdateEvents,
        SerializerInterface $serializer
    ) {
        $this->getCustomerRegisterEvent = $getCustomerRegisterEvent;
        $this->getNewsletterSubscriptionEvent = $getNewsletterSubscriptionEvent;
        $this->getCartUpdateEvents = $getCartUpdateEvents;
        $this->serializer = $serializer;
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

//        $contactEvent = $this->getContactEvent->execute();
//        if ($contactEvent) {
//            $events[] = $contactEvent;
//        }
//
//        $customerLoginEvent = $this->getCustomerLoginEvent->execute();
//        if ($customerLoginEvent) {
//            $events[] = $customerLoginEvent;
//        }

        return $events;
    }
}
