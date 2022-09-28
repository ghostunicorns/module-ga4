<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\ViewModel;

use GhostUnicorns\Ga4\Model\Event\GetAllEvents;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class EventsLayer implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetAllEvents
     */
    private $getAllEvents;

    /**
     * @param RequestInterface $request
     * @param GetAllEvents $getAllEvents
     */
    public function __construct(
        RequestInterface $request,
        GetAllEvents $getAllEvents
    ) {
        $this->request = $request;
        $this->getAllEvents = $getAllEvents;
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
