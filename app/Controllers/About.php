<?php

namespace App\Controllers;

class About extends BaseController
{
    public function index()
    {
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Acerca de nosotros'
        ]);
        
        return view('about/index', $viewData);
    }
}