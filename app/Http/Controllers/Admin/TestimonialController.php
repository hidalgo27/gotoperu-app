<?php

namespace App\Http\Controllers\Admin;

use App\TTestimonio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonial = TTestimonio::all();
        return view('admin.testimonial', compact('testimonial'));
    }
}
