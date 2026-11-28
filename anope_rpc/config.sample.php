<?php
/*
 * Optional fallback configuration.
 * Settings made in the panel under Settings → General take precedence.
 * Copy this file to config.php to use it.
 */
return [
	'url'    => 'http://127.0.0.1:8080/jsonrpc', // Anope jsonrpc module endpoint
	'token'  => '',                              // token defined in Anope's jsonrpc block
	'cache'  => 30,                              // cache lifetime in seconds (0 = disabled)
	'detail' => 'full',                          // detail level for anope.list*
	'source' => '',                              // source nick for messages (e.g. Global), if required
	'lang'   => 'auto',                          // auto (browser) | en | fr | es | nl | de | pl
	// CTCP VERSION probe (like /ctcp NickServ VERSION); leave probe_host empty to disable
	'probe_host'   => '',          // e.g. 127.0.0.1
	'probe_port'   => 6697,
	'probe_tls'    => true,
	'probe_target' => 'NickServ',
	'probe_pass'   => '',          // IRC server password, if any
	'version'=> '',                              // Anope version, only if it cannot be detected (e.g. 2.1.28)
];
