# Changelog

All notable changes to `arjundhi/magento2-sales-email-attachments` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-04-16

### Added
- Auto-attachment of invoice, shipment, and credit memo PDFs to matching transactional emails.
- Configurable Terms & Conditions file attachment (PDF, DOC, DOCX, TXT).
- CC and BCC fields applied to all outgoing sales emails.
- `Rameera\SalesEmailAttachments\Mail\Template\TransportBuilder` — adds `addAttachment()` to the core transport builder via a DI preference.
- `Rameera\SalesEmailAttachments\Mail\SenderBuilder` — intercepts `configureEmailTemplate()` to attach documents before dispatch.
- Admin configuration section under `Rameera Extensions > Sales Email Attachments`.
- Client-side file-type validator mixin for the Terms & Conditions upload field.
