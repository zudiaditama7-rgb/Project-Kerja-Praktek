<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$count = 0;

foreach($files as $file) {
    $path = $file[0];
    
    // Skip auth and layouts
    if (strpos($path, 'auth') !== false || strpos($path, 'layouts') !== false) {
        continue;
    }

    $content = file_get_contents($path);
    $original = $content;

    // Add rounded corners to bg-[#065F46] table rows
    $content = preg_replace_callback('/<tr\s+class="([^"]*?bg-\[#065F46\][^"]*)"/is', function($m) {
        $cls = $m[1];
        if (strpos($cls, '[&>th:first-child]:rounded-tl-2xl') === false) {
            $cls .= ' [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl';
        }
        return '<tr class="' . trim(preg_replace('/\s+/', ' ', $cls)) . '"';
    }, $content);

    // Also, tables need border-separate or border-spacing-0 to allow border-radius on TH
    $content = preg_replace_callback('/<table\s+class="([^"]+)"/is', function($m) {
        $cls = $m[1];
        // Tailwind 'border-collapse' forces sharp corners. Change to 'border-separate border-spacing-0'
        $cls = preg_replace('/\bborder-collapse\b/', 'border-separate border-spacing-0', $cls);
        return '<table class="' . trim(preg_replace('/\s+/', ' ', $cls)) . '"';
    }, $content);

    if ($original !== $content) {
        file_put_contents($path, $content);
        $count++;
    }
}

echo "Rounded headers in $count files.";
