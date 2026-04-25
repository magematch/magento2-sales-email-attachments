<?php

declare(strict_types=1);

namespace MageMatch\SalesEmailAttachments\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Config section path for this module.
     */
    public const XML_PATH_MODULE = 'rameera_sales_email_attachments/';

    /**
     * Retrieve a raw config value by full path.
     */
    public function getConfigValue(string $field, ?int $storeId = null): mixed
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve a value from the module's general group.
     */
    public function getGeneralConfig(string $code, ?int $storeId = null): mixed
    {
        return $this->getConfigValue(
            self::XML_PATH_MODULE . 'general/' . $code,
            $storeId
        );
    }
}
