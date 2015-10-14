<?php
// This requires the libsodium PECL extension

/**
 * Encrypt a message
 * 
 * @param string $message - message to encrypt
 * @param string $key - encryption key
 * @return string
 */
functionsafeEncrypt($message, $key)
{
    $nonce = \Sodium\randombytes_buf(
        \Sodium\CRYPTO_SECRETBOX_NONCEBYTES
    );

    $cipher = base64_encode(
$nonce.
        \Sodium\crypto_secretbox(
            $message,
            $nonce,
            $key
        )
    );
    \Sodium\memzero($message);
    \Sodium\memzero($key);
return $cipher;
}

/**
 * Decrypt a message
 * 
 * @param string $encrypted - message encrypted with safeEncrypt()
 * @param string $key - encryption key
 * @return string
 */
functionsafeDecrypt($encrypted, $key)
{   
    $decoded = base64_decode($encrypted);
    $nonce = mb_substr($decoded, 0, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $ciphertext = mb_substr($decoded, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

    $plain = \Sodium\crypto_secretbox_open(
        $ciphertext,
        $nonce,
        $key
    );
    \Sodium\memzero($ciphertext);
    \Sodium\memzero($key);
return $plain;
}

