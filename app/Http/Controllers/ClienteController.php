<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function dashboard()
    {
        return view('cliente.dashboard');
    }

    public function reservas()
    {
        return view('cliente.reservas');
    }

    public function nuevaCita()
    {
        return view('cliente.nueva-cita');
    }

    public function misCitas()
    {
        return view('cliente.mis-citas');
    }

    public function historial()
    {
        return view('cliente.historial');
    }

    public function beneficios()
    {
        return view('cliente.beneficios');
    }

    public function misPuntos()
    {
        return view('cliente.mis-puntos');
    }

    public function promociones()
    {
        return view('cliente.promociones');
    }

    public function cuenta()
    {
        return view('cliente.cuenta');
    }

    public function miPerfil()
    {
        return view('cliente.mi-perfil');
    }

    public function notificaciones()
    {
        return view('cliente.notificaciones');
    }
}