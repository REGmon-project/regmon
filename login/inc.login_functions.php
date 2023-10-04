<?php
/**
 * hash_Password
 * This function hashes the result using the PASSWORD_DEFAULT algorithm and returns it.
 * - PASSWORD_DEFAULT use the bcrypt algorithm. Bcz it may change is recommended to store the result 
 * in a database column that can expand beyond 60 characters (255 characters would be a good choice).
 * @param string $password
 * @return string
 */
function hash_Password(string $password) {
	$password_hashed = password_hash($password, PASSWORD_DEFAULT);
	return $password_hashed;
}

/**
 * verify_Password
 * This function verifies a password by comparing the result to a hashed version of the password.
 * @param string $password
 * @param string $password_hashed
 * @return bool
 */
function verify_Password(string $password, string $password_hashed) {
	if (password_verify($password, $password_hashed)) {
		return true;
	}
	return false;
}


/**
 * hash_Secret
 * This function creates a hashed version of the password using 
 * the SHA-256 algorithm and the pepper as a key. 
 * Finally, it hashes the result using the PASSWORD_DEFAULT algorithm and returns it.
 * @param string $secret
 * @param string $pepper
 * @return string
 */
function hash_Secret(string $secret, string $pepper) {
	$secret_peppered = hash_hmac("sha256", $secret, $pepper);
	$secret_hashed = hash_Password($secret_peppered);
	return $secret_hashed;
}


/**
 * verify_Secret
 * This function verifies a secret by hashing it with a pepper 
 * and comparing the result to a hashed version of the secret.
 * @param string $secret_hashed
 * @param string $secret
 * @param string $pepper
 * @return bool
 */
function verify_Secret(string $secret_hashed, string $secret, string $pepper) {
	$secret_peppered = hash_hmac("sha256", $secret, $pepper);
	if (verify_Password($secret_peppered, $secret_hashed)) {
		return true;
	}
	return false;
}

//Encrypt/Decrypt ###########################
/**
 * Summary of Encrypt_String
 * @param string $string
 * @return bool|string
 */
function Encrypt_String(string $string) {
	return openssl_encrypt($string, "AES-128-ECB", "REGmon");
}
/**
 * Summary of Decrypt_String
 * @param string $encrypted_string
 * @return bool|string
 */
function Decrypt_String(string $encrypted_string) {
	return openssl_decrypt($encrypted_string, "AES-128-ECB", "REGmon");
}

?>
