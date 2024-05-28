<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\projectUser;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public  function index()
    {
        $projects = Project::all();
        $projectUser = ProjectUser::all();
        $projectUser->where('project_id','id');

        return view('dashboard', compact('projects','projectUser'));
    }
}
