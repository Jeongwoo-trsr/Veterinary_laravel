<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // assuming Pet Owners are in users table
use Illuminate\Http\Request;

class PetOwnerController extends Controller
{
    public function index()
    {
        $owners = User::where('role', 'pet-owner')->paginate(10);
        return view('admin.pet-owners', compact('owners'));
    }
}
