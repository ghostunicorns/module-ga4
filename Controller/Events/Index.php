<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Controller\Events;

use GhostUnicorns\Ga4\Model\Event\GetAllEvents;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Index implements ActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var GetAllEvents
     */
    private $getAllEvents;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param GetAllEvents $getAllEvents
     */
    public function __construct (
        JsonFactory $resultJsonFactory,
        GetAllEvents $getAllEvents
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->getAllEvents = $getAllEvents;
    }

    /**
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        $events = $this->getAllEvents->execute();

        return $this->resultJsonFactory->create()->setData([
            'events' => $events
        ]);
    }
}
