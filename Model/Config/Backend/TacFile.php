<?php

declare(strict_types=1);

namespace MageMatch\SalesEmailAttachments\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\File;

/**
 * Validates and persists the Terms & Conditions file upload.
 */
class TacFile extends File
{
    /**
     * Allowed file extensions for the Terms & Conditions attachment.
     *
     * @return string[]
     */
    protected function _getAllowedExtensions(): array
    {
        return ['pdf', 'doc', 'docx', 'txt'];
    }
}
