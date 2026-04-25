<?php

declare(strict_types=1);

namespace MageMatch\SalesEmailAttachments\Mail;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Pdf\Creditmemo;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use MageMatch\SalesEmailAttachments\Helper\Data;

/**
 * Extends Magento's core SenderBuilder to append PDF and policy-document
 * attachments to outgoing sales transactional emails.
 */
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder,
        private readonly Filesystem $filesystem,
        private readonly Invoice $invoice,
        private readonly Creditmemo $creditmemo,
        private readonly Shipment $shipment,
        private readonly Data $data,
        private readonly InvoiceCollectionFactory $invoiceCollectionFactory,
        private readonly CreditmemoCollectionFactory $creditmemoCollectionFactory,
        private readonly ShipmentCollectionFactory $shipmentCollectionFactory,
        private readonly File $file,
        ?TransportBuilderByStore $transportBuilderByStore = null
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $transportBuilder,
            $transportBuilderByStore
        );
    }

    /**
     * Attach PDF documents and/or a terms & conditions file before sending.
     *
     * @throws \Zend_Mime_Exception
     * @throws \Zend_Pdf_Exception
     */
    protected function configureEmailTemplate(): void
    {
        $mimeTypes = [
            'txt'  => 'text/plain',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/msword',
        ];

        $templateVars = $this->templateContainer->getTemplateVars();

        if ($this->data->getGeneralConfig('attach_pdf_enabled')) {
            $this->attachSalesPdf($templateVars);
        }

        if ($this->data->getGeneralConfig('attach_terms_and_conditions_enabled')) {
            $this->attachTermsConditions($templateVars, $mimeTypes);
        }

        foreach ($this->getExtraAddresses('cc') as $address) {
            $this->transportBuilder->addCc($address);
        }
        foreach ($this->getExtraAddresses('bcc') as $address) {
            $this->transportBuilder->addBcc($address);
        }

        parent::configureEmailTemplate();
    }

    /**
     * Attach the appropriate sales PDF (invoice / shipment / credit memo).
     *
     * @param array<string, mixed> $templateVars
     */
    private function attachSalesPdf(array $templateVars): void
    {
        $attachFor = explode(',', (string) $this->data->getGeneralConfig('attach_pdf_for'));

        if (isset($templateVars['invoice']) && in_array('invoice', $attachFor, true)) {
            $invoices = $this->invoiceCollectionFactory->create()
                ->addFieldToFilter('entity_id', $templateVars['invoice']['entity_id']);
            $pdf      = $this->invoice->getPdf($invoices);
            $this->transportBuilder->addAttachment(
                $pdf->render(),
                'invoice' . $templateVars['invoice']['increment_id'] . '.pdf',
                'application/pdf'
            );
            return;
        }

        if (isset($templateVars['creditmemo']) && in_array('creditmemo', $attachFor, true)) {
            $creditmemos = $this->creditmemoCollectionFactory->create()
                ->addFieldToFilter('entity_id', $templateVars['creditmemo']['entity_id']);
            $pdf         = $this->creditmemo->getPdf($creditmemos);
            $this->transportBuilder->addAttachment(
                $pdf->render(),
                'creditmemo' . $templateVars['creditmemo']['increment_id'] . '.pdf',
                'application/pdf'
            );
            return;
        }

        if (isset($templateVars['shipment']) && in_array('shipment', $attachFor, true)) {
            $shipments = $this->shipmentCollectionFactory->create()
                ->addFieldToFilter('entity_id', $templateVars['shipment']['entity_id']);
            $pdf       = $this->shipment->getPdf($shipments);
            $this->transportBuilder->addAttachment(
                $pdf->render(),
                'shipment' . $templateVars['shipment']['increment_id'] . '.pdf',
                'application/pdf'
            );
        }
    }

    /**
     * Attach the uploaded Terms & Conditions file when applicable.
     *
     * @param array<string, mixed>  $templateVars
     * @param array<string, string> $mimeTypes
     */
    private function attachTermsConditions(array $templateVars, array $mimeTypes): void
    {
        $attachFor = explode(',', (string) $this->data->getGeneralConfig('attach_terms_and_conditions_for'));
        $emailType = $this->resolveEmailType($templateVars);
        $tacFile   = (string) $this->data->getGeneralConfig('terms_conditions');

        if (!in_array($emailType, $attachFor, true) || $tacFile === '') {
            return;
        }

        $mediaDirectory  = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $destinationPath = $mediaDirectory->getAbsolutePath('rameera/sales_email_attachments/');
        $filepath        = $destinationPath . $tacFile;
        $content         = $this->file->fileGetContents($filepath);
        $fileNameParts   = explode('/', $tacFile);
        $fileName        = end($fileNameParts);
        $extensionParts  = explode('.', $tacFile);
        $extension       = strtolower(end($extensionParts));
        $mimeType        = $mimeTypes[$extension] ?? 'application/octet-stream';

        $this->transportBuilder->addAttachment($content, $fileName, $mimeType);
    }

    /**
     * Resolve the email document type from template variables.
     *
     * @param array<string, mixed> $templateVars
     */
    private function resolveEmailType(array $templateVars): string
    {
        foreach (['invoice', 'shipment', 'creditmemo', 'order'] as $type) {
            if (isset($templateVars[$type])) {
                return $type;
            }
        }
        return '';
    }

    /**
     * Return a trimmed list of email addresses from a comma-separated config field.
     *
     * @return string[]
     */
    private function getExtraAddresses(string $field): array
    {
        $value = (string) $this->data->getGeneralConfig($field);
        if ($value === '') {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $value)));
    }
}
