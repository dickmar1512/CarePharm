<?php
$dir = 'core/app/model/';
$files = glob($dir . '*.php');

$results = "";

foreach($files as $file) {
    $content = file_get_contents($file);
    
    // First, let's make sure we don't duplicate it
    if (strpos($content, "#[AllowDynamicProperties]") !== false) {
        $results .= "Skipping $file, already has it.<br>";
        continue;
    }
    
    // Find 'class ClassName {' or 'class ClassName extends Parent {'
    $lines = explode("\n", $content);
    $newLines = [];
    $modified = false;
    
    foreach($lines as $line) {
        if (!$modified && preg_match('/^\s*class\s+[a-zA-Z0-9_]+/', $line)) {
            $newLines[] = "#[AllowDynamicProperties]";
            $newLines[] = $line;
            $modified = true;
        } else {
            $newLines[] = $line;
        }
    }
    
    if ($modified) {
        file_put_contents($file, implode("\n", $newLines));
        $results .= "Added #[AllowDynamicProperties] to $file<br>";
    } else {
        $results .= "No class definition found in $file<br>";
    }
}

echo "Done!<br>".$results;
?>
