<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;

class ResultsController extends Controller
{
    public function show(){
        $pages = Page::paginate(20);
        return view('results', compact('pages'));
    }
}
