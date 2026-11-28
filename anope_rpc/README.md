# anope_rpc — Anope plugin for the UnrealIRCd WebPanel

Adds an **Anope** menu to the webpanel, available in **English** (default), **French**, **Spanish**, **Dutch**, **German** and **Polish**.

| Page | Contents |
|---|---|
| Dashboard | Counters; TLS / identified / away shares; connections per hour; top channels; load per server; TLDs; clones; longest-connected users; auto-refresh |
| Users | Filterable table (identified, TLS, away, opers, server) and a detail page (mask, IP, account, channels, raw JSON) |
| Channels | Sortable list with a "no topic" filter. Each channel has a detail page: topic, modes, members grouped by status, members in common with another channel |
| Accounts | Registrations per month, inactive accounts, account page with the sessions currently online |
| Servers & opers | Topology tree, load per server, services opers with their online status |
| Tools | Diagnostics and latency, RPC explorer, global or per-server announcement, private message, credentials test, `nick!ident@host` mask search, cache purge |

It also adds an **Anope** card to the panel home page.

## Requirements

* A recent UnrealIRCd WebPanel, with PHP 8 and the `curl` extension.
* Anope 2.1.14 or later, with the `httpd`, `jsonrpc` and `rpc_data` modules. Add `rpc_message` for announcements and `rpc_user` for the credentials test.
  Copy the matching blocks from your `modules.example.conf` (2.1.x), and bind `httpd` to `127.0.0.1` or put it behind TLS.

## Installation

```bash
cd /var/www/html/unrealircd-webpanel/plugins
unzip anope_rpc.zip            # creates plugins/anope_rpc/
chown -R www-data: anope_rpc
```

1. In the panel, open **Settings → Plugins** and enable `anope_rpc`.
   You can also add `"anope_rpc"` to the `plugins` array in `config/config.php`.
2. In **Settings → General**, fill in the *Anope JSON-RPC* section: URL, token, cache lifetime, default language.
   The token is encrypted with the panel key.
   Alternatively, copy `config.sample.php` to `config.php` in the plugin folder.
3. Open **Anope → Tools**. The diagnostics should show "Connected" and the list of methods.

## Languages

The language is chosen in this order:

1. The language picker at the top right of every plugin page (remembered in a cookie for one year).
2. The *Default language* set in **Settings → General**, if it isn't set to *Automatic*.
3. The browser's `Accept-Language` header.
4. English.

Translations live in `lang/xx.php`. Each one is a PHP array whose keys are the English strings. To add a language:

1. Copy `lang/fr.php` to `lang/it.php` (for example) and translate the values.
2. Add `'it' => 'Italiano'` to `ANOPE_LANGS` in `lib/i18n.php`.

Any string that has no translation is shown in English. Placeholders (`%s`, `%d`) must be kept. If a language needs them in a different order, use positional placeholders such as `%2$s`.

## Permissions

* Read-only access (every page, plus the `anope.list*`, `anope.user`, `anope.channel`, `anope.server`, `anope.account`, `anope.oper` and `system.*` methods): every logged-in panel user.
* Announcements, messages, credentials test, unrestricted RPC explorer and e-mail addresses: users with the *Manage users* (or *Rehash*) permission.

## Notes

* `rpc_data` field names changed across 2.1.x releases, so the plugin tries several names for each field (see `lib/client.php`).
  If a column stays empty, open the *Raw JSON* of a detail page and add the right key to the matching `u_*`, `c_*` or `a_*` function.
* If your version doesn't recognise `anope.listUsers ["full"]`, the plugin fetches items one by one (capped at 500). The detail keyword can be changed in the settings.
* **Anope version** (dashboard): there is no documented RPC method for it. The plugin tries, in order:
  1. a dedicated method, if `system.listMethods` lists one;
  2. **CTCP VERSION** to NickServ (the same as `/ctcp NickServ VERSION`). The panel opens a short IRC connection with a temporary `anoperpcNNNN` nick, then disconnects. Enable it by filling in *IRC server host* in **Settings → General**. Allow the panel host in your connection limits if needed;
  3. the services server as seen by UnrealIRCd (`server.get` → `features.software`), through the panel's own UnrealIRCd JSON-RPC connection;
  4. a `version` field on the services (U-lined) server, read with `anope.server`;
  5. the `Server:` header sent by Anope's httpd;
  6. the manual *Anope version* setting.

  A detected version is cached for 1 h; a failed detection is retried after 5 min.
  It is compared with the latest release on the same branch, read from `https://www.anope.org/feed.xml` (cached 6 h). If the panel host has no Internet access, the comparison badge is simply not shown.
* The file cache is stored in `sys_get_temp_dir()/anope_rpc_cache` (30 s by default).
