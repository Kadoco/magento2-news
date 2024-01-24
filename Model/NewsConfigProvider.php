<?php
declare(strict_types=1);

namespace Kadoco\News\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class NewsConfigProvider
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function getIsActive():string
    {
        return (string) $this->getConfigValue('news/configuration/active');
    }

    public function getDays():string
    {
        return (string) $this->getConfigValue('news/configuration/days');
    }

    public function getCategoryId():string
    {
        return (string) $this->getConfigValue('news/configuration/rootid');
    }

    private function getConfigValue(string $path): string
    {
        $storeId = $this->storeManager->getStore()->getId();

        return (string) $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
