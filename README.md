Pico for Nextcloud
====

Pico for Nextcloud is an app, allowing users to designate any folder in their
Nexcloud folder as site folder and have it served by Pico.

Install
-------

- Place `files_picocms` in your app folder.
- Copy the folder `samplesite` to the folder of some user
- Set them uid of this user in the app settings
- Set up your web server to redirect '/sites/some_site' to
  '/apps/files_picocms?site=some_site', via the two mod_rewrite rules:

```
RewriteRule ^sites/([^/]*)/(.*) /apps/files_picocms?site=$1&path=$2 [QSA,L]
RewriteRule ^sites/([^/]*) /apps/files_picocms?site=$1 [QSA,L]
```

If your Nextcloud is running under e.g. /nextcloud, modify accordingly.