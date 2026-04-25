# Sales Email Attachments for Magento 2

> Free, open-source Magento 2 extension  
> by **Arjun Dhiman** — 
> [Adobe Commerce Certified Master](https://magematch.com/developers/arjun-dhiman)  
> Part of the [MageMatch](https://magematch.com) 
> developer ecosystem

# Rameera Sales Email Attachments

`Rameera_SalesEmailAttachments` automatically appends PDF documents and policy files to Magento sales transactional emails — no custom email template edits required.

## Features

- Auto-attaches PDF renditions of invoices, shipments, and credit memos to the matching transactional email.
- Attaches a configurable Terms & Conditions or policy document (PDF, DOC, DOCX, TXT) to selected email types.
- Supports CC and BCC fields for all outgoing sales emails.
- Enable/disable each attachment type independently from the admin panel.
- Built with `declare(strict_types=1)`, constructor property promotion, and a clean `match` expression — no legacy Zend 1 code.
- Compatible with Magento 2.4.4+ and PHP 8.1 through 8.4.

## Compatibility

- Magento Open Source / Adobe Commerce `2.4.4` and later in the `2.4.x` line.
- PHP `8.1`, `8.2`, `8.3`, and `8.4`.

## Installation

> Important: use **one installation mode only**.
>
> - If installed via Composer, do **not** keep a copy in `app/code/Rameera/SalesEmailAttachments`.
> - If using `app/code`, do **not** install `arjundhi/magento2-sales-email-attachments` via Composer.

### Install from app/code

Place the module under:

`app/code/Rameera/SalesEmailAttachments`

Then run:

```bash
php bin/magento module:enable Rameera_SalesEmailAttachments
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

### Install with Composer

```bash
composer require arjundhi/magento2-sales-email-attachments
php bin/magento module:enable Rameera_SalesEmailAttachments
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

## Troubleshooting: duplicate module registration

If you see an error like:

`Module 'Rameera_SalesEmailAttachments' ... has been already defined in 'vendor/...'.`

it means Magento found the same module in both locations:

- `app/code/Rameera/SalesEmailAttachments`
- `vendor/arjundhi/magento2-sales-email-attachments`

Fix (Composer-based install):

```bash
rm -rf app/code/Rameera/SalesEmailAttachments
composer install
php bin/magento setup:upgrade
php bin/magento cache:flush
```

Verify only one copy remains:

```bash
test -d app/code/Rameera/SalesEmailAttachments && echo "app/code present" || echo "app/code missing"
test -d vendor/arjundhi/magento2-sales-email-attachments && echo "vendor present" || echo "vendor missing"
```

## Configuration

In admin, go to:

`Stores > Configuration > Rameera Extensions > Sales Email Attachments`

### PDF Attachments

| Field | Description |
|---|---|
| **Attach Sales PDF to Email** | Enable/disable auto PDF attachment. |
| **Attach PDF For** | Choose one or more: Invoice, Shipment, Credit Memo. |

### Terms & Conditions

| Field | Description |
|---|---|
| **Attach Terms & Conditions** | Enable/disable the policy document attachment. |
| **Attach Terms & Conditions For** | Choose email types: Order, Invoice, Shipment, Credit Memo. |
| **Terms & Conditions File** | Upload a `.pdf`, `.doc`, `.docx`, or `.txt` file. |

### CC / BCC

| Field | Description |
|---|---|
| **CC Email Address(es)** | Comma-separated addresses added to every sales email. |
| **BCC Email Address(es)** | Comma-separated addresses added to every sales email. |

## How it works

The module overrides two Magento core classes via `di.xml` preferences:

- `Magento\Sales\Model\Order\Email\SenderBuilder` → `Rameera\SalesEmailAttachments\Mail\SenderBuilder`
  Intercepts `configureEmailTemplate()` to append attachments before the email is sent.

- `Magento\Framework\Mail\Template\TransportBuilder` → `Rameera\SalesEmailAttachments\Mail\Template\TransportBuilder`
  Adds the `addAttachment()` method used by `SenderBuilder`.

## Module Structure

```
Helper/Data.php                          Config value accessor
Mail/SenderBuilder.php                   Attachment injection logic
Mail/Template/TransportBuilder.php       addAttachment() support
Model/Config/Backend/TacFile.php         File upload backend model
Model/Config/Source/AttachPdfIn.php      PDF email type source
Model/Config/Source/AttachTermsConditions.php  T&C email type source
etc/adminhtml/system.xml                 Admin config fields
etc/acl.xml                              ACL resource
etc/config.xml                           Default config values
etc/di.xml                               Class preference wires
view/adminhtml/requirejs-config.js       Mixin registration
view/adminhtml/web/js/store-config/
  validator-rules-mixin.js               Client-side file-type validation
```

## CI Matrix

This repository includes a GitHub Actions workflow at `.github/workflows/ci.yml`.

Validation runs on:

- PHP `8.2`
- PHP `8.4`

It validates Composer metadata, PHP syntax, and XML well-formedness.

### Install commands by environment

Stable production install:

```bash
composer require arjundhi/magento2-sales-email-attachments:^1.0
```

Staging/dev install (before first stable tag is visible on Packagist):

```bash
composer require arjundhi/magento2-sales-email-attachments:"dev-main@dev"
```

## License

This project is licensed under the MIT License. See `LICENSE` for details.

---
## Installation
```bash
composer require magematch/magento2-sales-email-attachments
bin/magento module:enable MageMatch_SalesEmailAttachments
bin/magento setup:upgrade
bin/magento cache:clean
```

## Compatibility
- Magento Open Source 2.4.x
- Adobe Commerce 2.4.x
- PHP 8.1, 8.2, 8.3

## Support & Custom Development
Need custom Magento development?  
Find vetted Adobe Commerce developers at  
**[magematch.com](https://magematch.com)**

## License
MIT License — free to use commercially
