<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controlador base del sistema.
 * 
 * Proporciona acceso a las utilidades estándar de Laravel como validación
 * de solicitudes y control de autorización mediante políticas y roles.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
