<?php
echo "Testing hash...\n";
if (function_exists('password_hash')) {
    echo "password_hash exists.\n";
} else {
    echo "password_hash MISSING.\n";
}

$hash = password_hash('test', PASSWORD_DEFAULT);
echo "Hash: $hash\n";
