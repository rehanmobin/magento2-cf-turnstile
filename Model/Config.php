<?php
/**
 * @category  Mage4
 * @package   Mage4_Turnstile
 * @author    Rehan Mobin <m.rehan.mobin@gmail.com>
 * @copyright Copyright (c) Mage4. All rights reserved. (https://www.mage4.com)
 */

namespace Mage4\Turnstile\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private ScopeConfigInterface $config;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->config = $scopeConfig;
    }

    public function isEnabledForContactForm(): bool
    {
        return $this->config->isSetFlag('m4_trunstile/m4_trunstile_frontend/contact', ScopeInterface::SCOPE_STORE);
    }

    public function getSiteKey(): ?string
    {
        return $this->config->getValue('m4_trunstile/m4_trunstile_api_config/site_key', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getSecretKey(): ?string
    {
        return $this->config->getValue('m4_trunstile/m4_trunstile_api_config/secret_key', ScopeInterface::SCOPE_WEBSITE);
    }

    private function isEnabledForForm(string $key): bool
    {
        return $this->config->isSetFlag('m4_trunstile/'. $key, ScopeInterface::SCOPE_STORE);
    }

    public function isEnabledFor(string $key): bool
    {
        $isEnabledFormForm = $this->isEnabledForForm($key);
        $siteKey = $this->getSiteKey();
        $secretKey = $this->getSecretKey();
        return $isEnabledFormForm && !empty($siteKey) && !empty($secretKey);
    }

    public function getUiConfig(): array
    {
        return \array_diff_key($this->config->getValue('m4_trunstile/m4_trunstile_api_config', ScopeInterface::SCOPE_WEBSITE), ['secret_key' => '']);
    }
}
