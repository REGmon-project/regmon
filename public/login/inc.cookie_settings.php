<?php
// Shared login policy. Production cookies stay secure behind a TLS reverse proxy,
// even when the proxy-to-Apache connection uses HTTP.
declare(strict_types=1);

function get_Login_Cookie_Options(int $expires = 0, bool $http_only = true): array {
    global $CONFIG;
    return [
        'expires' => $expires,
        'path' => '/' . $CONFIG['REGmon_Folder'],
        'secure' => (bool)($CONFIG['Production_Mode'] ?? true),
        'httponly' => $http_only,
        'samesite' => 'Lax',
    ];
}

function configure_Login_Session(): void {
    $options = get_Login_Cookie_Options();
    ini_set('session.cookie_secure', $options['secure'] ? '1' : '0');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
}
