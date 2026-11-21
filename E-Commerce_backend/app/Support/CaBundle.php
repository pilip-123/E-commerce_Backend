<?php

namespace App\Support;

/**
 * Finds a usable CA certificate bundle for outgoing HTTPS / TLS connections.
 *
 * Some PHP builds (e.g. MacPorts without the curl-ca-bundle port) point OpenSSL
 * at a CA file that does not exist. Every TLS connection then fails — Guzzle
 * reports it as "Connection refused" — which breaks Telegram alerts, Google /
 * GitHub login and SMTP mail. When that happens we fall back to the operating
 * system's bundle instead of turning certificate verification off.
 */
class CaBundle
{
    private const SYSTEM_BUNDLES = [
        '/etc/ssl/cert.pem',                  // macOS, Alpine
        '/etc/ssl/certs/ca-certificates.crt', // Debian / Ubuntu
        '/etc/pki/tls/certs/ca-bundle.crt',   // RHEL / CentOS
    ];

    /**
     * Path to a fallback bundle, or null when PHP's own bundle already works.
     */
    public static function fallbackPath(): ?string
    {
        $configured = env('CA_BUNDLE') ?: config('services.telegram.ca_bundle');
        if ($configured && is_readable($configured)) {
            return $configured;
        }

        $iniCaFile = ini_get('openssl.cafile');
        $default = openssl_get_cert_locations()['default_cert_file'] ?? null;
        if (($iniCaFile && is_readable($iniCaFile)) || ($default && is_readable($default))) {
            return null;
        }

        foreach (self::SYSTEM_BUNDLES as $bundle) {
            if (is_readable($bundle)) {
                return $bundle;
            }
        }

        return null;
    }

    /**
     * Value for Guzzle's "verify" option: a bundle path, or true for PHP's default.
     */
    public static function guzzleVerify(): string|bool
    {
        return self::fallbackPath() ?? true;
    }
}
