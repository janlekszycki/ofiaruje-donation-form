# Project Guidelines

## Architecture

- This repository is a WordPress plugin with a single main runtime file: `ofiaruje-donation-form.php`.
- Core boundaries inside `ofiaruje-donation-form.php`:
  - Admin settings registration and settings page rendering.
  - Frontend asset output (inline CSS/JS printed in `wp_footer`).
  - Shortcode rendering for `[ofiaruje_formularz]`.
- Payment method logos are loaded from `assets/` and rendered conditionally in the shortcode output.

## Build And Test

- No build, lint, or automated test commands are defined in this repository.
- Validate changes by smoke-testing in a local WordPress instance:
  - Activate the plugin.
  - Configure options in Settings -> Ofiaruje.
  - Render `[ofiaruje_formularz]` on a page and verify frontend behavior.
  - Verify that donation form POST action points to the configured Ofiaruje endpoint.

## Conventions

- Prefix all plugin functions and option keys with `ofiaruje_`.
- Keep compatibility with WordPress coding patterns already used in the file:
  - Register settings through `register_setting`.
  - Sanitize all options and escape all output.
  - Use WordPress helpers (`esc_*`, `sanitize_*`, `checked`, `add_query_arg`, `plugins_url`, `plugin_dir_path`).
- Preserve the shortcode contract:
  - Shortcode name remains `[ofiaruje_formularz]` unless explicitly requested.
  - Hidden field naming (`donation[...]`) must stay compatible with Ofiaruje endpoint expectations.
- Preserve existing behavior around minimum donation amount (`>= 20 PLN`) in both settings sanitization and frontend validation.
- Keep user-facing copy in Polish unless asked to localize.

## Existing Docs

- Project usage, installation, and configuration are documented in `README.md`.
- Prefer linking to `README.md` for end-user setup details instead of duplicating that content in code comments or new docs.
