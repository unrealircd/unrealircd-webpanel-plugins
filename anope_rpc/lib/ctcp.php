<?php
/*
 * Equivalent of "/ctcp NickServ VERSION": opens a short-lived IRC connection,
 * registers a throw-away nick, sends a CTCP VERSION to the services bot and
 * returns the reply (e.g. "Anope-2.1.28 services.example.net :...").
 * Used only when a probe host is configured, and the result is cached.
 */

class AnopeCTCPProbe
{
	/**
	 * @return string|null CTCP VERSION reply, or null on failure
	 * @throws AnopeRPCException with a readable reason on failure
	 */
	public static function version(string $host, int $port, bool $tls, string $target = 'NickServ', string $password = '', float $timeout = 6.0): ?string
	{
		$deadline = microtime(true) + $timeout;
		$ctx = stream_context_create(['ssl' => [
			// Probing localhost or a self-signed services link is common: certificate checks are
			// relaxed for loopback addresses only.
			'verify_peer' => !self::isLoopback($host),
			'verify_peer_name' => !self::isLoopback($host),
			'SNI_enabled' => true,
		]]);
		$uri = ($tls ? 'tls://' : 'tcp://') . $host . ':' . $port;
		$sock = @stream_socket_client($uri, $errno, $errstr, min(4, $timeout), STREAM_CLIENT_CONNECT, $ctx);
		if (!$sock)
			throw new AnopeRPCException(t('CTCP probe: cannot connect to %s (%s)', "$host:$port", $errstr ?: "error $errno"));
		stream_set_timeout($sock, 1);

		$nick = 'anoperpc' . random_int(1000, 9999);
		$send = function (string $l) use ($sock) { fwrite($sock, $l . "\r\n"); };
		if ($password !== '') $send('PASS ' . $password);
		$send("NICK $nick");
		$send("USER anoperpc 0 * :Anope RPC webpanel version probe");

		$registered = false; $reply = null;
		while (microtime(true) < $deadline && !feof($sock)) {
			$line = fgets($sock, 4096);
			if ($line === false) continue;
			$line = rtrim($line, "\r\n");
			if (str_starts_with($line, 'PING ')) { $send('PONG ' . substr($line, 5)); continue; }
			$parts = explode(' ', $line, 4);
			$cmd = $parts[1] ?? '';
			if ($cmd === '433' || $cmd === '432') { $nick = 'anoperpc' . random_int(10000, 99999); $send("NICK $nick"); continue; }
			if (!$registered && ($cmd === '001' || $cmd === '376' || $cmd === '422')) {
				$registered = true;
				$send("PRIVMSG $target :\x01VERSION\x01");
				continue;
			}
			// :NickServ!service@host NOTICE nick :\x01VERSION Anope-2.1.28 ...\x01
			if ($cmd === 'NOTICE' && stripos($parts[0], ':' . $target . '!') === 0
				&& preg_match('/:\x01VERSION\s+([^\x01]+)\x01?/', $line, $m)) {
				$reply = trim($m[1]);
				break;
			}
			if ($cmd === 'ERROR' || $cmd === '465') break;
			if ($cmd === '401' && $registered) break; // no such nick
		}
		@fwrite($sock, "QUIT :done\r\n");
		@fclose($sock);
		if ($reply === null)
			throw new AnopeRPCException($registered ? t('CTCP probe: %s did not answer VERSION', $target) : t('CTCP probe: could not register on %s', "$host:$port"));
		return $reply;
	}

	private static function isLoopback(string $h): bool
	{
		return in_array(strtolower($h), ['localhost', '::1'], true) || str_starts_with($h, '127.');
	}
}
