<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    if (preg_match('/<thead[^>]*>(.*?)<\/thead>/is', $content, $matches)) {
        echo "<b>" . basename($path) . "</b><br>";
        echo htmlspecialchars($matches[1]) . "<br><hr>";
    }
}
