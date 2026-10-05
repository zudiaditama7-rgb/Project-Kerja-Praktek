<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$count = 0;

foreach($files as $file) {
    $path = $file[0];
    
    // Skip auth and layouts just in case
    if (strpos($path, 'auth') !== false || strpos($path, 'layouts') !== false) {
        continue;
    }

    $content = file_get_contents($path);
    $original = $content;

    // Remove text-sm from table
    $content = preg_replace('/<table\s+class="([^"]*?)\btext-sm\b([^"]*)"/is', '<table class="$1$2"', $content);
    // Ensure table has border-collapse
    $content = preg_replace_callback('/<table\s+class="([^"]+)"/is', function($m) {
        $c = $m[1];
        if (strpos($c, 'border-collapse') === false) {
            $c .= ' border-collapse';
        }
        return '<table class="' . $c . '"';
    }, $content);

    // Process TH
    $content = preg_replace_callback('/<th\s+class="([^"]+)"/is', function($m) {
        $cls = $m[1];
        // Remove existing paddings and vertical borders
        $cls = preg_replace('/\b(px-[0-9.]+|py-[0-9.]+|p-[0-9.]+|border-[rl](-[a-z0-9\/]+)?)\b/', '', $cls);
        $cls = trim(preg_replace('/\s+/', ' ', $cls));
        // Add new padding
        $cls = 'px-8 py-5 ' . $cls;
        return '<th class="' . $cls . '"';
    }, $content);

    // Process TD
    $content = preg_replace_callback('/<td\s+class="([^"]+)"/is', function($m) {
        $cls = $m[1];
        // Remove existing paddings and vertical borders
        $cls = preg_replace('/\b(px-[0-9.]+|py-[0-9.]+|p-[0-9.]+|border-[rl](-[a-z0-9\/]+)?)\b/', '', $cls);
        $cls = trim(preg_replace('/\s+/', ' ', $cls));
        // Add new padding
        $cls = 'px-8 py-4 ' . $cls;
        return '<td class="' . $cls . '"';
    }, $content);

    if ($original !== $content) {
        file_put_contents($path, $content);
        $count++;
    }
}

echo "Unified $count files.";
