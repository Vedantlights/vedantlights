<?php
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => ['User-Agent: PHP']
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

$composerUrl = 'https://getcomposer.org/download/latest-stable/composer.phar';
$composerPhar = file_get_contents($composerUrl, false, $context);

if ($composerPhar !== false) {
    file_put_contents('composer.phar', $composerPhar);
    echo "Composer downloaded successfully!\n";
} else {
    echo "Failed to download Composer.\n";
    exit(1);
}
?>
