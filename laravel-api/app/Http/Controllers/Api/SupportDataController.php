<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class SupportDataController extends Controller
{
    public function groups()
    {
        return Group::query()->get(['id', 'name']);
    }
}
