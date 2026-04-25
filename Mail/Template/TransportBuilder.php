<?php

declare(strict_types=1);

namespace MageMatch\SalesEmailAttachments\Mail\Template;

use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\Exception\InvalidArgumentException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;

/**
 * Extends Magento's TransportBuilder with addAttachment() support so that
 * SenderBuilder can inject PDF and document files into outgoing emails.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /** @var MimePart[] */
    private array $attachments = [];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        ?MessageInterfaceFactory $messageFactory = null,
        ?EmailMessageInterfaceFactory $emailMessageInterfaceFactory = null,
        ?MimeMessageInterfaceFactory $mimeMessageInterfaceFactory = null,
        ?MimePartInterfaceFactory $mimePartInterfaceFactory = null,
        ?AddressConverter $addressConverter = null
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory,
            $messageFactory,
            $emailMessageInterfaceFactory,
            $mimeMessageInterfaceFactory,
            $mimePartInterfaceFactory,
            $addressConverter
        );
    }

    /**
     * Attach a raw-content part (PDF, document, etc.) to the outgoing message.
     *
     * @param string $content  Raw file contents.
     * @param string $fileName Desired attachment filename.
     * @param string $fileType MIME type (e.g. 'application/pdf').
     * @return $this
     */
    public function addAttachment(string $content, string $fileName, string $fileType): static
    {
        $part              = new MimePart($content);
        $part->encoding    = Mime::ENCODING_BASE64;
        $part->type        = $fileType;
        $part->disposition = Mime::DISPOSITION_ATTACHMENT;
        $part->filename    = $fileName;

        $this->attachments[] = $part;

        return $this;
    }

    /**
     * Reset state including attachments list.
     */
    protected function reset(): static
    {
        $this->attachments = [];
        return parent::reset();
    }

    /**
     * Build the MIME message, merging body part with any attachments.
     *
     * @throws LocalizedException
     */
    protected function prepareMessage(): static
    {
        $template = $this->getTemplate();
        $content  = $template->processTemplate();

        $part = match ($template->getType()) {
            TemplateTypesInterface::TYPE_TEXT => MimeInterface::TYPE_TEXT,
            TemplateTypesInterface::TYPE_HTML => MimeInterface::TYPE_HTML,
            default                           => throw new LocalizedException(new Phrase('Unknown template type')),
        };

        $mimePart = $this->mimePartInterfaceFactory->create(['content' => $content]);
        $parts    = $this->attachments !== []
            ? array_merge([$mimePart], $this->attachments)
            : [$mimePart];

        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(['parts' => $parts]);
        $this->messageData['subject'] = html_entity_decode(
            (string) $template->getSubject(),
            ENT_QUOTES
        );
        $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);

        return $this;
    }
}
