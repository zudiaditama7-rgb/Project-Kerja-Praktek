<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$count = 0;

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;

    // Replace header row classes
    // Common pattern: bg-gray-50 border-b border-gray-200 text-gray-700 text-xs uppercase tracking-wider font-bold
    $content = preg_replace(
        '/<tr class="bg-gray-50 border-b border-gray-200 text-gray-700([^"]*)"/i',
        '<tr class="bg-[#065F46] text-white$1 border-b-0"',
        $content
    );

    // Some tables might just have: text-gray-400 text-xs uppercase tracking-wider
    if (strpos($path, 'admin\dashboard.blade.php') !== false) {
        $content = str_replace(
            '<tr class="text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">',
            '<tr class="bg-[#065F46] text-white text-xs uppercase tracking-wider font-bold border-b-0">',
            $content
        );
    }

    // Replace row hover effects to emerald
    $content = str_replace('hover:bg-gray-50/50', 'hover:bg-emerald-50/50', $content);

    // Replace text-gray-700 to text-white if it was left behind in th
    // We already replaced in tr, but what if there's th?
    // Often <th> has text-gray-500 or similar.
    // If the <tr> is now dark green, text inside <th> must be white.
    // In our new <tr class="bg-[#065F46] text-white..."> the text color should cascade, 
    // unless th overrides it.
    $content = preg_replace('/<th([^>]*)text-gray-500([^>]*)>/i', '<th$1text-white$2>', $content);
    $content = preg_replace('/<th([^>]*)text-gray-700([^>]*)>/i', '<th$1text-white$2>', $content);

    if ($original !== $content) {
        file_put_contents($path, $content);
        echo "Updated: $path<br>";
        $count++;
    }
}

echo "Done. Updated $count files.";
