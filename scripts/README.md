# Project Scripts

This directory contains operational scripts kept with the application.

## Active scripts

- `backup/` contains backup and recovery jobs.
- `maintenance/` contains maintained Laravel maintenance scripts.
- `deploy*.sh` and `publish*.sh` are deployment and release entry points.

## Archived one-off scripts

`archive/` contains historical diagnostics, patches, manual tests, and generated
outputs that were previously kept in the repository root. These are not loaded
by the application or run automatically. Review a script before running it:
some were written for one-time use and may contain old paths or assumptions.

Python POS build scripts and versioned PyInstaller specs are kept separately in
`apps/python-pos/build-history/`.

## maintenance
Scripts for checking, repairing, and maintaining the Laravel application.

Run from the repository root so Laravel paths and environment settings are predictable:

```powershell
php scripts/maintenance/check_schema.php
```

These scripts load the root `vendor/` and `bootstrap/` directories automatically.
