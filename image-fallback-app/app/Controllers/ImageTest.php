<?php
// filepath: c:\Users\USER\Pictures\Image_fallback\Image_Fallback_Repository\image-fallback-app\app\Controllers\ImageTest.php

namespace App\Controllers;

use CodeIgniter\Controller;

class ImageTest extends BaseController
{
    public function index()
    {
        // Check if the view file exists
        $viewPath = APPPATH . 'Views/sample_response.php';
        if (!is_file($viewPath)) {
            log_message('error', 'View sample_response not found');
            throw new \CodeIgniter\Exceptions\PageNotFoundException('View not found');
        }

        return view('sample_response');
    }
}