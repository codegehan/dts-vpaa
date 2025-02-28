<?php
class Encryption
{
    private $cipher = 'aes-256-cbc'; // Cipher method
    private $key; // Secret key

    public function __construct($key)
    {
        $this->key = $key; // Set the secret key
    }
    // Method to encrypt data
    public function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher)); // Generate an IV
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv); // Encrypt the data
        return base64_encode($encrypted . '::' . base64_encode($iv)); // Return the encrypted data and IV
    }

    // Method to decrypt data
    public function decrypt($encodedData)
    {
        list($encrypted, $encodedIv) = explode('::', base64_decode($encodedData), 2); // Split data and IV
        $iv = base64_decode($encodedIv); // Decode the IV
        return openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv); // Decrypt the data
    }
}
?>
