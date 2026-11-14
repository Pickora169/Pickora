PICKORA - Deployment and backup notes

HOSTING
- Any WordPress host with PHP 7.4+ (8.1+ recommended), MySQL/MariaDB and HTTPS. Enable the free SSL certificate.
- Install WordPress, then follow README.txt.

AFTER GOING LIVE
1. Settings > Permalinks: Post name. Save once after activating the plugin.
2. Settings > PICKORA: add your Associates tracking ID, then click "Create starter pages".
3. Review every starter page. Privacy Policy and Terms are blank drafts: write them (ideally with legal advice) before publishing.
4. Settings > Reading: tick "Discourage search engines" OFF when you are ready to be indexed.
5. Sitemap is built into WordPress at /wp-sitemap.xml and robots.txt is generated automatically. Submit the sitemap in Google Search Console.
6. Settings > General: set Tagline to "Better Picks. Smarter Living." and Site Title to PICKORA. Upload a Site Icon if you want to replace the built-in favicon.
7. Send a test message through Contact Us. If mail does not arrive, install a free SMTP plugin (e.g. WP Mail SMTP) because many hosts block default PHP mail.

AMAZON ASSOCIATES
- Check the current Associates Operating Agreement and India programme rules yourself, including any minimum qualifying-sales requirement and rules on displaying prices.
- Keep the disclosure on every page with affiliate links (already built in).
- Only enter prices you have verified, with a date, in "Verified price / value note".

BACKUPS
- Back up both the database and wp-content (themes, plugins, uploads).
- Use your host's daily backups or a plugin such as UpdraftPlus, storing copies off the server (Google Drive, S3).
- Take a manual backup before updating WordPress, the theme or plugins.
- Content lives in the PICKORA Core plugin, so switching themes will not delete your reviews.

SECURITY BASICS
- Strong admin password plus two-factor login (e.g. Wordfence or Two-Factor plugin).
- Keep WordPress, themes and plugins updated. Delete unused plugins and themes.

OPTIONAL PLUGINS
- Caching: LiteSpeed Cache or WP Super Cache (fast loading).
- SEO: if you install Yoast or Rank Math, PICKORA's own SEO and schema output switches off automatically to avoid duplicates.
- SMTP: WP Mail SMTP.
