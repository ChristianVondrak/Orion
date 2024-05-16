<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public  function index()
    {
        $projects = Project::all();
        //return view('dashboard', compact('projects'));
    }

    public function show($id)
    {
        $projects = Project::all();
        $project = $projects->find($id);

        return view('projects.show', compact('project'));
    }
}
