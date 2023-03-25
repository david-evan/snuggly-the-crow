<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class APIController extends BaseController
{
    use ValidatesRequests;
}
