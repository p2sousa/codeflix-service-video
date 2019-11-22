<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\traits\AdvancedCrud;

abstract class BasicController extends Controller
{
    use AdvancedCrud;
}
