<?php
header('Content-Type: text/plain');

function get_perms($path) {
    if (!file_exists($path)) {
        return "does not exist";
    }
    return decoct(fileperms($path) & 0777);
}

$paths = [
    'storage',
    '../storage',
    '../storage/app',
    '../storage/app/public',
    '../storage/app/public/images',
    '../storage/app/public/images/camaro_no_bg.png'
];

echo "=== CURRENT PERMISSIONS ===\n";
foreach ($paths as $path) {
    echo "$path: " . get_perms($path) . "\n";
}

echo "\n=== FIXING PERMISSIONS ===\n";

function chmod_r($path) {
    if (!file_exists($path)) return;
    
    if (is_dir($path) && !is_link($path)) {
        echo "chmod 0755 $path: " . (chmod($path, 0755) ? "OK" : "FAIL") . "\n";
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..') {
                chmod_r($path . '/' . $file);
            }
        }
        closedir($dh);
    } else {
        echo "chmod 0644 $path: " . (chmod($path, 0644) ? "OK" : "FAIL") . "\n";
    }
}

chmod_r('../storage/app/public');

echo "\n=== NEW PERMISSIONS ===\n";
foreach ($paths as $path) {
    echo "$path: " . get_perms($path) . "\n";
}
?>
