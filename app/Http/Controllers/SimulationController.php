<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class SimulationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Simulation/Index');
    }
}
