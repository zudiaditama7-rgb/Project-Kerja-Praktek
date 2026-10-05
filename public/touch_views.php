<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);
$count = 0;
foreach($files as $file) {
    touch($file[0]);
    $count++;
}
echo "Touched $count files to trigger Vite reload and invalidate View cache.";
