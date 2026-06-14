<?php

namespace App\Controllers;

class Branches extends BaseController
{
    public function index()
    {
        $branches = $this->lpConfig->branches;
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Nuestras sucursales',
            'branches' => $branches
        ]);
        
        return view('branches/index', $viewData);
    }
}