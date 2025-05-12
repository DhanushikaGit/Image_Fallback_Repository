<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ImageFallback implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Nothing before request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if ($response->getBody()) {
            $body = $response->getBody();

            // Match <img> tags, capturing src and entire tag
            $pattern = '/<img\s+([^>]*)src=["\']([^"\']+)["\']([^>]*)>/i';

            $body = preg_replace_callback($pattern, function ($matches) {
                $before_src = $matches[1]; // Attributes before src
                $image_path = $matches[2]; // src value
                $after_src = $matches[3];  // Attributes after src
                $full_tag = $matches[0];   // Full <img> tag

                // Skip if tag has onerror attribute
                if (stripos($before_src . $after_src, 'onerror') !== false) {
                    return $full_tag;
                }

                // Skip external URLs, inline images, and SVGs
                if (
                    preg_match('/^https?:\/\//i', $image_path) ||
                    preg_match('/^data:image\//i', $image_path) ||
                    preg_match('/\.svg$/i', $image_path)
                ) {
                    return $full_tag;
                }

                // Check if image exists using helper
                $new_path = getValidImagePath($image_path);
                $is_fallback = $new_path !== $image_path;

                // Prepare attributes
                $attributes = trim($before_src . ' ' . $after_src);

                // Add loading="lazy" if not present
                if (stripos($attributes, 'loading=') === false) {
                    $attributes .= ' loading="lazy"';
                }

                // Add img-check class
                if (stripos($attributes, 'class=') !== false) {
                    $attributes = preg_replace(
                        '/class=["\']([^"\']*)["\']/i',
                        'class="$1 img-check"',
                        $attributes
                    );
                } else {
                    $attributes .= ' class="img-check"';
                }

                // Add data-fallback="true" if fallback was used
                if ($is_fallback) {
                    $attributes .= ' data-fallback="true"';
                }

                // Reconstruct <img> tag
                return "<img $attributes src=\"$new_path\">";
            }, $body);

            $response->setBody($body);
        }

        return $response;
    }
}