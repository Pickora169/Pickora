---
layout: page
title: "Contact Us"
permalink: /contact-us/
---
Questions, corrections or suggestions? Send us a message.
{% if site.contact_form_endpoint != "" %}
<form method="post" action="{{ site.contact_form_endpoint }}">
  <input class="hp" type="text" name="_gotcha" tabindex="-1" autocomplete="off" aria-hidden="true">
  <div class="field"><label for="c-name">Name</label><input id="c-name" name="name" type="text" required></div>
  <div class="field"><label for="c-email">Email</label><input id="c-email" name="email" type="email" required></div>
  <div class="field"><label for="c-msg">Message</label><textarea id="c-msg" name="message" rows="6" required></textarea></div>
  <button class="btn" type="submit">Send message</button>
</form>
{% elsif site.contact_email != "" %}
Email us at <a href="mailto:{{ site.contact_email }}">{{ site.contact_email }}</a>.
{% else %}
<p class="note">Contact form not connected yet. Set <code>contact_form_endpoint</code> or <code>contact_email</code> in _config.yml.</p>
{% endif %}
