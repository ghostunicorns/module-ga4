<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Customer\Model\Session;

class GetCustomerEmailMd5
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session,
    ) {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function execute(): string
    {
        if (!$this->session->isLoggedIn()) {
            return '';
        }
        $customer = $this->session->getCustomer();
        $email = $customer->getEmail();

        return hash('md5', $email);
    }
}
