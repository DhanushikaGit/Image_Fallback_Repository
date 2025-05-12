<?php

// app/Helpers/image_helper.php

function getValidImagePath(string $path): string
{
    // Skip external URLs, inline images, and SVGs
    if (
        preg_match('/^https?:\/\//i', $path) ||
        preg_match('/^data:image\//i', $path) ||
        preg_match('/\.svg$/i', $path)
    ) {
        return $path;
    }

    $fullPath = FCPATH . ltrim($path, '/');
    if (file_exists($fullPath)) {
        return $path;
    }

    return '/assets/images/default.png';
}

// Keep existing helper for compatibility
function image_url(string $path): string
{
    $validPath = getValidImagePath($path);
    return base_url($validPath);
}