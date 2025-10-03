# Cloudflare Turnstile for XenForo 2

## Overview
Cloudflare Turnstile (AxCF) integrates the [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/) CAPTCHA service with XenForo 2, providing a privacy-friendly alternative to traditional CAPTCHAs for registration, login and contact forms.

## Requirements
- XenForo 2.1 or 2.2
- PHP 7.4 or newer
- Valid Cloudflare Turnstile site and secret keys

## Installation
1. Upload the contents of the `src/addons/AxCF/Turnstile` directory into the same path on your XenForo installation.
2. Add your Turnstile credentials to `src/config.php`:
   ```php
   $config['axcfTurnstile'] = [
       'siteKey'   => 'your-site-key',
       'secretKey' => 'your-secret-key',
       'theme'     => 'auto',
       'size'      => 'normal',
   ];
   ```
3. In the XenForo Admin Control Panel go to **Add-ons → Install/upgrade** and install **Cloudflare Turnstile**.
4. Navigate to **Setup → Options → CAPTCHA** and select **Cloudflare Turnstile** as the active CAPTCHA provider.
5. Test the registration, login and contact forms to confirm the widget renders and validation passes.

## Configuration
The add-on reads its settings from `src/config.php`. The following options are available:

| Key       | Description                                                  | Default |
|-----------|--------------------------------------------------------------|---------|
| `siteKey` | Required. Your Cloudflare Turnstile site key.                | –       |
| `secretKey` | Required. Your Cloudflare Turnstile secret key.            | –       |
| `theme`   | Optional. Widget theme: `auto`, `light`, or `dark`.          | `auto`  |
| `size`    | Optional. Widget size: `normal` or `compact`.                | `normal`|

Changes to `src/config.php` take effect immediately without rebuilding the add-on.

## How it works
- The public template renders the Turnstile widget container and loads the Cloudflare API with the `defer` attribute.
- On form submission the add-on validates the `cf-turnstile-response` token server-side via the Cloudflare verification endpoint using XenForo's built-in HTTP client.
- Verification failures or network errors are logged via `\XF::logException` for easier troubleshooting.

## Troubleshooting
- **Widget not visible** – Ensure both `siteKey` and `secretKey` are present in `src/config.php`. The provider will not render without them.
- **Validation always fails** – Verify that the server can reach `https://challenges.cloudflare.com` and that the server clock is accurate.
- **Timeout errors** – Network issues will log an error. Check your firewall or proxy and confirm outbound HTTPS connections to Cloudflare are allowed.
- **Using a proxy** – Confirm the real visitor IP is forwarded correctly so Turnstile can validate the request.

## Uninstall
1. In the XenForo Admin Control Panel, switch to a different CAPTCHA provider.
2. Uninstall the **Cloudflare Turnstile** add-on from **Add-ons → Installed add-ons**.
3. Remove the `axcfTurnstile` configuration block from `src/config.php` if no longer required.
4. Delete the add-on files from the server if desired.

## Changelog & License
See [`CHANGELOG.md`](CHANGELOG.md) for release history and [`LICENSE`](LICENSE) for licensing details.
