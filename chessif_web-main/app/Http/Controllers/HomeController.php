<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function getHome()
    {
        $token = Auth::user();
        return view('home.index')->with(['token' => $token]);
    }

    public function getViewPainelDeControle(){
        return view('home.painel-de-controle');
    }


}
