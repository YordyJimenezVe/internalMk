<?php
header('Content-Type: text/plain');

$target = '../storage/app/public';
$link = 'storage';

echo "Current working directory: " . getcwd() . "\n";

if (is_link($link)) {
    echo "Existing link points to: " . readlink($link) . "\n";
    if (unlink($link)) {
        echo "Successfully deleted existing link.\n";
    } else {
        echo "Failed to delete existing link.\n";
    }
} elseif (file_exists($link)) {
    echo "storage exists as a regular file/directory.\n";
    if (is_dir($link)) {
        echo "It is a directory. Cannot automatically delete directory. Please rename or delete it manually.\n";
    } else {
        if (unlink($link)) {
            echo "Successfully deleted existing file.\n";
        } else {
            echo "Failed to delete existing file.\n";
        }
    }
} else {
    echo "No existing storage file or link found.\n";
}

if (!file_exists($link)) {
    if (symlink($target, $link)) {
        echo "Created symbolic link: $link -> $target\n";
    } else {
        echo "Failed to create symbolic link.\n";
    }
}
?>
