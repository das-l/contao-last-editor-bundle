# Contao Last Editor Bundle

Stores and shows which backend user last edited an item. The tables for which this is recorded can be configured.

## Use case

How can this be useful when versioning already exists? By default, versions are purged after 90 days. If you need to know which user last edited something more than 90 days ago, you can't get that information. Technically, to avoid this, you can set the `versionPeriod` config to something huge, or remove the purge task by replacing the `PurgeExpiredDataCron` and removing the purge config in `TL_PURGE`. However, if you have a quite frequently edited site, you end up with a very large amount of data over time, which to avoid the purge exists in the first place.

**If all you need to know is which user last edited something, rather than the entire edit history, this extension is for you. It does not replace Contao's versioning in any way, it just adds the last editor as a permanent piece of information.**

## Setup and Usage

Install the extension and run database migrations:

```
composer require das-l/contao-last-editor-bundle
vendor/bin/contao-console contao:migrate
```

A migration is included that will copy the most recent editor from existing version information.

From now on, any time an item is saved, the editing user is stored. To view this information, use the "show details" feature ("info" icon in default list views).

## Config

By default, the last editor is stored for the tables `tl_page`, `tl_article`, and `tl_content`. You can customize this in your config:

```yaml
contao_last_editor:
    tables: [ tl_page, tl_content, tl_news ]
```

After changing the config, you'll need to run migrations again:

```sh
vendor/bin/contao-console cache:clear
vendor/bin/contao-console contao:migrate
```
