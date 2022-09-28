<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Observer\Customer;

use GhostUnicorns\Ga4\Model\Event\GetCustomerRegisterEvent;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Register implements ObserverInterface
{
	/**
	 * @var Session
	 */
	protected $session;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SessionFactory $sessionFactory
     * @param SerializerInterface $serializer
     */
    public function __construct
	(
		SessionFactory $sessionFactory,
        SerializerInterface $serializer
	) {
		$this->session = $sessionFactory->create();
        $this->serializer = $serializer;
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
	public function execute(EventObserver $observer)
	{
		if ($customer = $observer->getCustomer())
		{
			$this->session->setData(
                GetCustomerRegisterEvent::EVENT_NAME,
                $this->serializer->serialize([
                    'event' => 'new_registration',
                    'new_registration_result' => 'OK',
                    'user_email_md5' =>  hash('md5', $customer->getEmail()),
                    'user_email_sha256' =>  hash('sha256', $customer->getEmail())
                ])
            );
		}
	}
}
