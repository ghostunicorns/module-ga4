<?php
declare(strict_types=1);

namespace GhostUnicorns\Ga4\ViewModel;

use GhostUnicorns\Ga4\Model\CategoryManager;
use GhostUnicorns\Ga4\Model\GetCustomerEmailMd5;
use GhostUnicorns\Ga4\Model\GetCustomerEmailSha256;
use GhostUnicorns\Ga4\Model\GetLanguage;
use GhostUnicorns\Ga4\Model\GetPageType;
use GhostUnicorns\Ga4\Model\GetSiteArea;
use GhostUnicorns\Ga4\Model\IsCustomerLoggedIn;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class GenericLayer implements ArgumentInterface
{
    const STATUS_LOGGED = 'logged';
    const STATUS_NOT_LOGGED = 'not_logged';
    /**
     * @var CategoryManager
     */
    private $categoryManager;

    /**
     * @var IsCustomerLoggedIn
     */
    private $isCustomerLoggedIn;

    /**
     * @var GetPageType
     */
    private $getPageType;

    /**
     * @var GetSiteArea
     */
    private $getSiteArea;

    /**
     * @var GetLanguage
     */
    private $getCurrentLanguage;

    /**
     * @var GetCustomerEmailMd5
     */
    private $customerEmailMd5;

    /**
     * @var GetCustomerEmailSha256
     */
    private $customerEmailSha256;

    /**
     * @param CategoryManager $categoryManager
     * @param IsCustomerLoggedIn $isCustomerLoggedIn
     * @param GetPageType $getPageType
     * @param GetSiteArea $getSiteArea
     * @param GetLanguage $getCurrentLanguage
     * @param GetCustomerEmailMd5 $customerEmailMd5
     * @param GetCustomerEmailSha256 $customerEmailSha256
     */
    public function __construct(
        CategoryManager $categoryManager,
        IsCustomerLoggedIn $isCustomerLoggedIn,
        GetPageType $getPageType,
        GetSiteArea $getSiteArea,
        GetLanguage $getCurrentLanguage,
        GetCustomerEmailMd5 $customerEmailMd5,
        GetCustomerEmailSha256 $customerEmailSha256
    ) {
        $this->categoryManager = $categoryManager;
        $this->isCustomerLoggedIn = $isCustomerLoggedIn;
        $this->getPageType = $getPageType;
        $this->getSiteArea = $getSiteArea;
        $this->getCurrentLanguage = $getCurrentLanguage;
        $this->customerEmailMd5 = $customerEmailMd5;
        $this->customerEmailSha256 = $customerEmailSha256;
    }

    /**
     * @param string $value
     * @return string
     */
    private function format(string $value): string
    {
        $value = strtolower($value);
        $value = str_replace(' ', '_', $value);
        return preg_replace('/[^\w-]/', '', $value);
    }

    /**
     * @return string
     */
    public function getLoginStatus(): string
    {
        return $this->isCustomerLoggedIn->execute() ? self::STATUS_LOGGED : self::STATUS_NOT_LOGGED;
    }

    /**
     * @return string
     */
    public function getPageType(): string
    {
        return $this->format($this->getPageType->execute());
    }

    /**
     * @return string
     */
    public function getSiteArea(): string
    {
        return $this->format($this->getSiteArea->execute());
    }

    /**
     * @return string
     */
    public function getEcommerceArea(): string
    {
        return $this->format($this->categoryManager->getCategoryLevel1());
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->format($this->getCurrentLanguage->execute());
    }

    /**
     * @return string
     */
    public function getUserEmailMd5(): string
    {
        return $this->format($this->customerEmailMd5->execute());
    }

    /**
     * @return string
     */
    public function getUserEmailSha256(): string
    {
        return $this->format($this->customerEmailSha256->execute());
    }
}
