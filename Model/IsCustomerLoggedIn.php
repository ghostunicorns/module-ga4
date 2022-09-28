<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\Model;

use Magento\Customer\Model\Context as ContextModel;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context as Context;

class IsCustomerLoggedIn
{

    /**
     * @var Context
     */
    private $context;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Context $context
     * @param Session $session
     */
    public function __construct(
        Context $context,
        Session $session
    ) {
        $this->context = $context;
        $this->session = $session;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        if ($this->context->getValue(ContextModel::CONTEXT_AUTH)) {
            return true;
        } else if ($this->session->isLoggedIn()) {
            return true;
        }

        return false;
    }
}
