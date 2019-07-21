<?php

namespace Oforge\Engine\Modules\Core\Services;

use Oforge\Engine\Modules\Core\Exceptions\EncryptionException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

/**
 * Class EncryptionService
 *
 * @package Oforge\Engine\Modules\Core\Services
 */
class EncryptionService {
    private const DEFAULT_METHOD = 'aes-256-gcm';
    /** @var array $config */
    private $config = [];

    /**
     * EncryptionService constructor.
     *
     * @throws EncryptionException
     */
    public function __construct() {
        $this->config = Oforge()->Settings()->get('encryption');

        $this->config['method'] = ArrayHelper::get($this->config, 'method', self::DEFAULT_METHOD);

        foreach (['key'] as $key) {
            if (!isset($this->config[$key])) {
                throw new EncryptionException("Missing '$key' in encryption config!");
            }
        }
        if (!in_array($this->config['method'], openssl_get_cipher_methods())) {
            $method = $this->config['method'];
            throw new EncryptionException("Unsupported encryption method '$method'!");
        }
    }

    /**
     * Generate secret key for encryption.
     *
     * @return string
     */
    public function generateSecretKey() : string {
        return base64_encode(openssl_random_pseudo_bytes(256));
    }

    /**
     * Encrypt string.
     *
     * @param string|null $plainString
     *
     * @return string Returns null if $plainString is null or encrypted string.
     * @throws EncryptionException
     */
    public function encrypt(?string $plainString) : ?string {
        if (is_null($plainString)) {
            return null;
        }
        $method = $this->config['method'];
        $key    = $this->config['key'];
        $iv     = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));

        $encryptedString = openssl_encrypt($plainString, $method, $key, 0, $iv, $tag);
        if ($encryptedString === false) {
            throw new EncryptionException(sprintf("OpenSSL error: %s", openssl_error_string()));
        }

        return base64_encode($encryptedString . '::::' . $iv . '::::' . $tag);
    }

    /**
     * Decrypt string.
     *
     * @param string|null $encryptedString
     *
     * @return string Returns null if $encryptedString is null or decrypted string.
     * @throws EncryptionException
     */
    public function decrypt(?string $encryptedString) : ?string {
        if (is_null($encryptedString)) {
            return null;
        }
        if (empty($encryptedString)) {
            return '';
        }
        list($encryptedString, $iv, $tag) = explode('::::', base64_decode($encryptedString), 3);
        $method      = $this->config['method'];
        $key         = $this->config['key'];
        $plainString = openssl_decrypt($encryptedString, $method, $key, 0, $iv, $tag);
        if ($plainString === false) {
            throw new EncryptionException(sprintf("OpenSSL error: %s", openssl_error_string()));
        }

        return $plainString;
    }

}
