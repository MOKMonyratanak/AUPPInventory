<?php

namespace App\Http\Controllers;

class AboutController extends Controller
{
    /**
     * Display the About page.
     */
    public function index()
    {
        try {
            $appVersion = config('app.version'); // App version from config
            $plugins = [
                'Laravel Framework' => app()->version(),
                'Bootstrap' => '5.3.0',
                'FontAwesome' => '6.0.0-beta3',
                'jQuery' => '3.6.0',
                'Select2' => '4.1.0',
                'TailwindCSS' => '3.4.17',
                'AlpineJS' => '3.4.2',
                'Axios' => '1.7.4',
            ];
    
            $programmingLanguages = [
                'PHP' => '8.2.12',
                'Javascript' => '',
                'HTML5' => '',
                'CSS3' => '',
            ];

            $dependencies = [
                'barryvdh/laravel-dompdf' => '3.0',
                'phpoffice/phpword' => '1.3',
                'laravel/breeze' => '2.2',
                'laravel/pint' => '1.13',
                'fakerphp/faker' => '1.23',
                'pestphp/pest' => '3.2',
            ];
    
            $developerName = 'Mok Monyratanak'; 
    
            return view('about', compact('appVersion', 'plugins', 'programmingLanguages', 'developerName','dependencies'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
