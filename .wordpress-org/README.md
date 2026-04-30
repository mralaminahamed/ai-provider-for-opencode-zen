# WordPress.org Plugin Directory Assets

Visual assets for **AI Provider for OpenCode Zen**, designed to sit
visually consistent with the official `ai-provider-for-google`,
`ai-provider-for-openai`, and `ai-provider-for-anthropic` plugins on WP.org.

## Files

| File | Purpose | WP.org spec |
|------|---------|-------------|
| `icon-128x128.png` | Plugin directory icon (low DPI) | Required |
| `icon-256x256.png` | Plugin directory icon (high DPI / retina) | Required |
| `icon.svg`         | Vector master for the icon | Optional, recommended |
| `banner-772x250.png`  | Plugin page header (low DPI) | Required |
| `banner-1544x500.png` | Plugin page header (high DPI / retina) | Recommended |
| `banner.svg`         | Vector master for the banner | For source archival |

## Design notes

- **Background:** `#1E1E1E` — identical to the official sibling AI provider plugins.
- **WordPress mark:** the official WP "W" path used by the sibling plugins, rendered
  at `#353535` as a watermark in the banner and at full white in the icon.
- **Provider badge:** indigo `#6366F1` circle with a `#1E1E1E` 3 px stroke. The indigo
  is consistent with the existing brand palette in this plugin and intentionally
  distinct from Google (gradient), OpenAI (black), and Anthropic (coral) so the
  plugin is recognizable in a directory list.
- **Provider mark:** the official OpenCode Zen "zen" pixel-block wordmark, redrawn
  in white (`#FFFFFF`) with a lighter-indigo highlight (`#A5B4FC`) so the brand's
  signature two-tone effect reads cleanly against the indigo badge.

## Trademark / non-affiliation

This plugin is independent and not affiliated with OpenCode Zen. Use of the
"zen" wordmark inside the badge follows the standard nominative-fair-use pattern
that third-party WP.org integrations (Stripe gateways, PayPal extensions, etc.)
use to reference the service they connect to. The non-affiliation disclaimer in
`readme.txt` ("This plugin is an independent, third-party integration and is not
affiliated with, endorsed by, or sponsored by OpenCode Zen") must remain
prominent for this usage to stay defensible. If OpenCode Zen ever objects, the
fastest fallback is to replace the badge contents with a generic chevron mark —
the SVG comments in `icon.svg` and `banner.svg` make this swap a one-block edit.

## Where to put these in the repo

The repo already has a `.wordpress-org/` folder. Replace the existing files there:

```
.wordpress-org/
├── icon-128x128.png
├── icon-256x256.png
├── icon.svg
├── banner-772x250.png
└── banner-1544x500.png
```

## How they reach WP.org

WordPress.org plugin assets do **not** live in the plugin zip — they live in the
SVN repository's `/assets/` directory. Two paths:

1. **Manual SVN commit** (one-time):
   ```
   svn co https://plugins.svn.wordpress.org/alamin-ai-provider-for-opencode-zen/ svn-checkout
   cp .wordpress-org/* svn-checkout/assets/
   cd svn-checkout && svn add assets/* && svn ci -m "Add plugin directory assets"
   ```

2. **GitHub Action** (recommended, continuous): use
   [`10up/action-wordpress-plugin-asset-update`](https://github.com/10up/action-wordpress-plugin-asset-update)
   in `.github/workflows/`. It syncs `.wordpress-org/` to SVN `/assets/` on push.

## Re-rendering

Both PNGs are rendered from the SVG masters via cairosvg. To regenerate at
exact spec sizes:

```python
import cairosvg
cairosvg.svg2png(url='icon.svg',   write_to='icon-128x128.png',  output_width=128,  output_height=128)
cairosvg.svg2png(url='icon.svg',   write_to='icon-256x256.png',  output_width=256,  output_height=256)
cairosvg.svg2png(url='banner.svg', write_to='banner-772x250.png',  output_width=772,  output_height=250)
cairosvg.svg2png(url='banner.svg', write_to='banner-1544x500.png', output_width=1544, output_height=500)
```
