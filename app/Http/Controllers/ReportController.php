<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = [
            [
                'title' => 'Login por Profesional',
                'description' => 'Retrasos de entrada y horario de sesión',
                'route' => route('reports.login'),
            ],
            [
                'title' => 'Activity Index',
                'description' => 'Índice de actividad de WorkSnaps',
                'route' => route('reports.activity'),
            ],
            [
                'title' => 'Nuevos Ingresos',
                'description' => 'Seguimiento de nuevos profesionales',
                'route' => route('reports.newcomers'),
            ],
            [
                'title' => 'Actualizaciones de Tarifas',
                'description' => 'Cambios en el hourly rate',
                'route' => route('reports.rateupdates'),
            ],
        ];

        return view('reports.index', compact('reports'));
    }
}
