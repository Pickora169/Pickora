# PICKORA – GitHub Pages website

Better Picks. Smarter Living. A static review site built with Jekyll, which GitHub Pages builds automatically. No server, no database, no build step on your side.

## Deploy on GitHub (5 steps)
1. Create a new GitHub repository (public, or private if your plan supports Pages).
2. Upload everything in this folder to the repository root (`_config.yml` must be at the top level).
3. Open `_config.yml` and set:
   - `url: "https://YOUR-USERNAME.github.io"`
   - `baseurl: "/YOUR-REPO"` (use `""` if the repo is named `YOUR-USERNAME.github.io` or you use a custom domain)
4. Repository **Settings → Pages → Build and deployment → Deploy from a branch → `main` / `(root)` → Save**.
5. Wait 1 to 3 minutes. Your site appears at the address shown on the Pages settings screen.

## Publish a review every day
1. Open the `_templates/review-template.md` file and copy its text.
2. Go to the `_reviews` folder → **Add file → Create new file**.
3. Name it like `brand-model-review.md` (the name becomes the URL), paste, fill in the fields, write the review below the second `---`.
4. Upload the product photo to `assets/img/reviews/` and point `image:` at it.
5. **Commit changes.** The site rebuilds in a minute or two and the review appears on the homepage, its category page, search and related articles.

Guides, comparisons, best picks and deals work the same way using `_templates/guide-template.md` and the `_guides` folder.

To save a draft, add `published: false` to the top fields. Delete `sample-review-delete-me.md` before launch.

## Things you control without code
| What | Where |
|---|---|
| Featured review on the homepage | `featured_review:` in `_config.yml` (the file name without `.md`) |
| Amazon tracking ID | `affiliate_tag:` in `_config.yml`, added automatically to Amazon.in links |
| Social links | `social:` in `_config.yml` |
| Editor's Picks | `editors_pick: true` in a review |
| Categories | `_data/categories.yml`, plus a matching folder in `category/` (copy an existing one) |
| Budgets | `_data/budgets.yml`, plus a folder in `budget/` |
| Menu | `_data/nav.yml` |

## Forms (contact and newsletter)
GitHub Pages cannot process forms itself. Create a free form endpoint with a service such as Formspree, then paste its URL into `contact_form_endpoint` and `newsletter_endpoint` in `_config.yml`. Until then the site shows a "not connected yet" note. For the newsletter, use the form URL from your email service.

## What is different from the WordPress version
No admin dashboard (GitHub's web editor and the templates replace it), no scheduled publishing, and no server-side features. Search is built in and runs in the visitor's browser. If you want the dashboard and scheduling, use the WordPress package on a normal host instead.

## Before launch
- Write the Privacy Policy and Terms pages. They are placeholders marked as templates.
- Check the current Amazon Associates programme rules, including the disclosure and price display rules.
- Only enter prices you have verified. Never copy ratings or reviews from other sites.
- Add your social links and upload `assets/img/pickora-profile-1080.png` to Instagram, Facebook and YouTube.

## Status
Not test-built (no Jekyll in the environment where this was created). If the GitHub Pages build fails, open the **Actions** tab, read the error line, and send it to me to fix.
