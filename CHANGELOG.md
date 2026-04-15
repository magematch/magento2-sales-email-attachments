# Changelog

All notable changes to `arjundhi/magento2-sales-email-attachments` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-04-16

### Added
- Initial public release under the `Rameera` vendor namespace.
- Auto-attachment of invoice, shipment, and credit memo PDFs to matching transactional emails.
- Configurable Terms & Conditions file attachment (PDF, DOC, DOCX, TXT).
- CC and BCC fields applied to all outgoing sales emails.
- `Rameera\SalesEmailAttachments\Mail\Template\TransportBuilder` — adds `addAttachment()` to the core transport builder via a DI preference.
- `Rameera\SalesEmailAttachments\Mail\SenderBuilder` — intercepts `configureEmailTemplate()` to attach documents before dispatch.
- Admin configuration section under `Rameera Extensions > Sales Email Attachments`.
- Client-side file-type validator mixin for the Terms & Conditions upload field.

### Changed
- Vendor namespace migrated from `Sparsh` to `Rameera`.
- Config section path changed from `sparsh_sales_email_attachments` to `rameera_sales_email_attachments`.
- Media upload directory changed from `sparsh/sales_email_attachments` to `rameera/sales_email_attachments`.
- All PHP files updated to `declare(strict_types=1)`.
- `SenderBuilder` rewritten with constructor property promotion and extracted private helper methods.
- `TransportBuilder::prepareMessage()` uses `match` expression instead of `switch`.
- `AttachTermsConditons` (typo) renamed to `AttachTermsConditions`.
- `_getAllowedExtensions()` visibility corrected to `protected` with return type `array`.
- `OptionSourceInterface` used in place of deprecated `Magento\Framework\Option\ArrayInterface`.

### Removed
- `setup_version` attribute removed from `etc/module.xml`.
- `version` field removed from `composer.json` (Packagist convention).
- `TransportBuilder.php@bkp` backup file not carried forward.
