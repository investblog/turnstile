# Cloudflare Turnstile for XenForo 2

Cloudflare Turnstile is a privacy-first CAPTCHA replacement. This add-on plugs it into XenForo 2 so you can protect registration, login, contact and any other CAPTCHA-enabled form without forcing visitors through intrusive puzzles.

## Features

- Registers a **Cloudflare Turnstile** CAPTCHA provider that appears alongside the built-in challenges on `/admin.php?captcha-questions/`.
- Works anywhere XenForo expects a CAPTCHA (registration, login, contact form, user promotions, etc.).
- Validates responses via Cloudflare's API with robust error handling and logging.
- Configuration can be supplied either via dedicated admin options (recommended) or a fallback block in `src/config.php`.

## Requirements

- XenForo 2.1+
- PHP 7.4+
- Cloudflare Turnstile site & secret keys

## Installation

1. Upload the `src/addons/AxCF/Turnstile` directory into the same path of your XenForo installation (or merge the repository contents if you develop directly against XenForo's `src` folder).
2. In the XenForo Admin Control Panel go to **Add-ons → Install/upgrade** and install **Cloudflare Turnstile**.
3. Provide your Turnstile keys:
   - If you maintain developer output, import the add-on's options so they appear under **Setup → Options → Cloudflare Turnstile**, or
   - Add the fallback block to `src/config.php`:
     ```php
     $config['axcfTurnstile'] = [
         'siteKey' => 'your-site-key',
         'secretKey' => 'your-secret-key',
         'theme' => 'auto',
         'size' => 'normal',
     ];
     ```
4. Open **Setup → CAPTCHA** (or `/admin.php?captcha-questions/`) and choose **Cloudflare Turnstile** as the active provider.

## Configuration

| Option        | Location             | Description                                                  |
| ------------- | -------------------- | ------------------------------------------------------------ |
| `siteKey`     | Admin option/config  | Public site key from the Turnstile dashboard.                |
| `secretKey`   | Admin option/config  | Secret key used for server-side verification.                |
| `theme`       | Admin option/config  | `auto`, `light` or `dark`. Determines widget styling.        |
| `size`        | Admin option/config  | `normal` or `compact`. Controls widget size.                 |

Values configured via the XenForo options UI always override the fallback `config.php` values. Changing either source takes effect immediately.

## Troubleshooting

- **Captcha not showing** – Confirm the site/secret keys are provided and that **Cloudflare Turnstile** is selected on the CAPTCHA configuration page.
- **All verifications fail** – Ensure the server has outbound HTTPS access to `https://challenges.cloudflare.com` and the system clock is correct.
- **Errors in logs** – The add-on logs HTTP and payload issues with a `[AxCF Turnstile]` prefix inside the XenForo error log for easier diagnosis.

## Development

- The add-on ships with developer output (`_output`) so you can tweak templates and phrases directly from source control.
- PHP code lives under `src/addons/AxCF/Turnstile`. Run `composer dump-autoload` inside your XenForo project if you add namespaces.

## License & Changelog

See [`CHANGELOG.md`](CHANGELOG.md) for release notes and [`LICENSE`](LICENSE) for licensing information.
