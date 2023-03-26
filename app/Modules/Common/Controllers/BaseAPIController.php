<?php

namespace App\Modules\Common\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class BaseAPIController extends BaseController
{
    use ValidatesRequests;
}
