# Release Checklist

Use this checklist before publishing a new GitHub release.

## Code Quality

- Confirm `composer.json` package metadata and repository URLs are current.
- Run `composer validate --no-check-lock --strict`.
- Run PHP lint on all module files.
- Confirm XML files are well-formed.

## Functional Checks

- Confirm the `Sales Email Attachments` config section is visible under `MageMatch Extensions`.
- Confirm PDF attachment is added to invoice emails when enabled.
- Confirm Terms & Conditions file uploads and attaches correctly.
- Confirm CC/BCC addresses receive copies of sales emails.
- Test with each supported document type: Invoice, Shipment, Credit Memo, Order.

## Release Prep

- Update `CHANGELOG.md` with version and date.
- Commit all changes with a clear release message.
- Create and push annotated tag (example: `v1.0.0`).
- Publish GitHub release notes.
- Trigger Packagist update (manual refresh or webhook sync).

## Install Verification Matrix

- Verify stable install works: `composer require arjundhi/magento2-sales-email-attachments:^1.0`.
- Verify dev fallback works for pre-tag testing: `composer require arjundhi/magento2-sales-email-attachments:"dev-main@dev"`.
- Run validation against PHP `8.2` and `8.4` (same matrix as CI).
