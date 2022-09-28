<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Observer;

use GhostUnicorns\Ga4\Model\Event\GetNewsletterSubscriptionEvent;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Newsletter implements ObserverInterface
{
	/**
	 * @var Session
	 */
	protected $session;

	/**
	 * @var RequestInterface
	 */
	protected $request;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SessionFactory $sessionFactory
     * @param RequestInterface $request
     * @param SerializerInterface $serializer
     */
	public function __construct
	(
		SessionFactory $sessionFactory,
	    RequestInterface $request,
        SerializerInterface $serializer
	) {
		$this->session = $sessionFactory->create();
		$this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
	public function execute(EventObserver $observer)
	{
	    $isSubscribed = $this->request->getParam('is_subscribed');

	    if (is_null($isSubscribed)) {
	        $isSubscribed = true;
	    } else {
	        $isSubscribed = 1 === (int) $isSubscribed;
	    }

	    $email = $this->request->getParam('email');
        if (is_null($email)) {
            return;
        }

        $this->session->setData(
            GetNewsletterSubscriptionEvent::EVENT_NAME,
            $this->serializer->serialize([
                'event' => 'newsletter_subscription',
                'newsletter_subscription_result' => $isSubscribed ? 'OK' : 'KO',
                'user_email_md5' => hash('md5', $email),
                'user_email_sha256'=> hash('sha256', $email)
            ])
        );

	}
}
