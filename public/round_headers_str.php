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

    // Direct string replacement for common variants
    $search1 = '<tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0">';
    $replace1 = '<tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0 [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">';
    
    $search2 = '<tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider">';
    $replace2 = '<tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl">';

    $content = str_replace($search1, $replace1, $content);
    $content = str_replace($search2, $replace2, $content);

    // Make sure we don't have duplicate classes
    $content = str_replace(' [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl', ' [&>th:first-child]:rounded-tl-2xl [&>th:last-child]:rounded-tr-2xl', $content);

    // Replace border-collapse with border-separate border-spacing-0
    $content = str_replace('<table class="w-full text-left border-collapse">', '<table class="w-full text-left border-separate border-spacing-0">', $content);
    $content = str_replace('<table class="w-full text-left text-sm border-collapse">', '<table class="w-full text-left text-sm border-separate border-spacing-0">', $content);
    $content = str_replace('<table class="w-full text-left text-sm">', '<table class="w-full text-left text-sm border-separate border-spacing-0">', $content);

    if ($original !== $content) {
        file_put_contents($path, $content);
        $count++;
    }
}

echo "Rounded headers using str_replace in $count files.";
