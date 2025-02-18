<?php

namespace App\Http\Controllers;

class AboutController extends Controller
{
    /**
     * Display the About page.
     */
    public function index()
    {
        $appVersion = config('app.version'); // App version from config
        $plugins = [
            'Laravel Framework' => app()->version(),
            'Bootstrap' => '5.3.0',
            'FontAwesome' => '6.0.0-beta3',
            'jQuery' => '3.6.0',
            'Select2' => '4.1.0',
        ];

        $programmingLanguages = [
            'PHP' => '8.2.12',
            'Javascript' => '',
            'HTML5' => '',
            'CSS3' => '',
        ];

        $developerName = 'Mok Monyratanak'; 

        return view('about', compact('appVersion', 'plugins', 'programmingLanguages', 'developerName'));
    }
}
