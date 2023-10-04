<?php

$mySecretKey = getenv('PASSWORD');

if ($mySecretKey) {
    // Use the secret key
    echo "My secret key is: " . $mySecretKey;
} else {
    echo "Secret key not found!";
}

?>
