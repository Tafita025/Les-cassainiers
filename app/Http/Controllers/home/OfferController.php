<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfferController extends Controller
{
    public function offers(){
        return Inertia::render('home/Offers');
    }
}
