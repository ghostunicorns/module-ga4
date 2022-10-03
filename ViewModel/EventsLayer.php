<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\ViewModel;

use GhostUnicorns\Ga4\Model\Event\GetAllEvents;
use GhostUnicorns\Ga4\Model\Event\ReadCustomerLoginEvent;
use GhostUnicorns\Ga4\Model\Event\ReadCustomerLogoutEvent;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class EventsLayer implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var GetAllEvents
     */
    private $getAllEvents;

    /**
     * @var ReadCustomerLoginEvent
     */
    private $readCustomerLoginEvent;

    /**
     * @param RequestInterface $request
     * @param SerializerInterface $serializer
     * @param GetAllEvents $getAllEvents
     * @param ReadCustomerLoginEvent $readCustomerLoginEvent
     */
    public function __construct(
        RequestInterface $request,
        SerializerInterface $serializer,
        GetAllEvents $getAllEvents,
        ReadCustomerLoginEvent $readCustomerLoginEvent
    ) {
        $this->request = $request;
        $this->serializer = $serializer;
        $this->getAllEvents = $getAllEvents;
        $this->readCustomerLoginEvent = $readCustomerLoginEvent;
    }

    /**
     * @return array
     */
    public function getLoginInfo(): array
    {
        if ($this->request->isXmlHttpRequest()) {
            return [];
        }

        $loginInfo = $this->readCustomerLoginEvent->execute();

        if (!$loginInfo) {
            return [];
        }

        return $this->serializer->unserialize($loginInfo);
    }

    /**
     * @return array
     */
    public function getEventsLayers(): array
    {
        if ($this->request->isXmlHttpRequest()) {
            return [];
        }

        return $this->getAllEvents->execute();
    }
}
