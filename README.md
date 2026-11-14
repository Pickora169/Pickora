# PICKORA – WordPress theme and plugin

Product reviews, comparisons and buying guides for India. Better Picks. Smarter Living.

## What's inside
| Path | Purpose |
|---|---|
| `wp-content/themes/pickora` | The PICKORA theme (design, homepage, templates) |
| `wp-content/plugins/pickora-core` | Reviews, guides, fields, featured review, affiliate links, SEO, contact form, newsletter |
| `docs/INSTALL.md` | Setup and daily publishing guide |
| `docs/DEPLOY.md` | Launch, Amazon Associates and backup notes |
| `branding/` | Logo files for social profiles and favicon |

## Install (quick)
1. Run `./build.sh` to create `dist/pickora.zip` and `dist/pickora-core.zip`
   (or zip the two folders yourself so the folder name is at the top of each zip).
2. WordPress admin → Plugins → Add New → Upload `pickora-core.zip` → Activate.
3. Appearance → Themes → Add New → Upload `pickora.zip` → Activate.
4. Follow `docs/INSTALL.md`.

The plugin holds your content, so changing the theme later will not delete reviews.

## Status
Untested on a live WordPress site. Try it on a staging site first.

## License
Source files declare GPL-2.0-or-later. Add a `LICENSE` file via GitHub (Add file → Create new file → type `LICENSE`, then choose a template).
