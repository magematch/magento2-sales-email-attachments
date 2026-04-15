<?php

declare(strict_types=1);

namespace Rameera\SalesEmailAttachments\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Source model: document types that may receive the Terms & Conditions attachment.
 */
class AttachTermsConditions implements OptionSourceInterface
{
    /**
     * @return array<int, array{value: string, label: \Magento\Framework\Phrase}>
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'order',      'label' => __('Order')],
            ['value' => 'invoice',    'label' => __('Invoice')],
            ['value' => 'shipment',   'label' => __('Shipment')],
            ['value' => 'creditmemo', 'label' => __('Credit Memo')],
        ];
    }
}
