# Archived utility scripts

These one-off files were moved out of the Laravel project root to keep
application entry points easy to find. They are retained for reference and
are not part of the application runtime or automated test suite.

- `diagnostics/`: ad hoc checks and inspection helpers.
- `devtools/`: local-only developer utilities.
- `operations/`: one-time operational scripts; inspect before use.
- `outputs/`: captured command output.
- `patches/`: one-off code modification scripts, including earlier files from
  the top-level `patches/` directory.
- `scratch/`: generated or recovered working files.
- `tests/`: manual test scripts; use the project's PHPUnit/Python test suites
  for current automated tests.

Some scripts use absolute paths, access the local database, or were made for a
specific debugging session. Their presence here does not mean they are safe or
supported to run as-is.
