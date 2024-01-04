# Helpful Posts
_A WordPress plugin to allow website visitors to vote if a Post is helpful or not._

## Description

**Helpful Posts** is a WordPress plugin that adds a voting system under every single Post. Visitors are asked if the article was useful and can vote **Yes** or **No** by clicking one of the two buttons.

Users are presented with the voting results in percentages for the two options and they can only vote once for each Post. Admins can see the actual totals and percentages in the backend for each Post. Admins can also reset the votes for each Post.

### Installation

1. Download the plugin from the link below.
2. In your admin panel, go to Plugins > Add New Plugin, click Upload Plugin and upload `helpful-posts.zip`.
2. Alternatively, upload the contents of `helpful-posts.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
3. Activate the plugin.

### Features

* No settings or configuration needed. Just install and activate.
* Voting buttons appear after every Post by appending to the content.
* Voting is done via AJAX, no page refresh needed.
* After voting, users can see the percentages and their own vote.
* Users can vote only once per Post with the same IP.
* Votes are kept as Post meta data, stored in the `wp_postmeta` table.
* User IPs along with a list of their votes are kept in a custom database table.
* Admins can see actual votes and percentages for each Post in their dashboard.
* Admins can reset votes for each Post. This updates Post meta and the IPs table.
* Clearing votes is done via AJAX, no refresh needed.
* Plugin is compatible with sites using cache plugins/systems.
* All strings are translations ready.

### Source

* Download it: _(add public link here)_
* Browse code: _(add public link here)_
* Revision Log: _(add public link here)_
