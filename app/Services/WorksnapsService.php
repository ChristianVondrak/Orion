<?php

namespace App\Services;

use App\Models\WorksnapUser;
use App\Models\Timming;
use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WorksnapsService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.worksnaps.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.worksnaps.api_key');
    }

    public function syncUsers()
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("{$this->baseUrl}/users.json");

            $users = $response->json();

            foreach ($users as $userData) {
                WorksnapUser::updateOrCreate(
                    ['id' => $userData['id']],
                    [
                        'login' => $userData['login'],
                        'first_name' => $userData['first_name'],
                        'last_name' => $userData['last_name'],
                        'email' => $userData['email'],
                        'timezone_id' => $userData['timezone_id'],
                        'timezone_name' => $userData['timezone_name']
                    ]
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing Worksnaps users: ' . $e->getMessage());
            throw $e;
        }
    }

    public function syncProjects()
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("{$this->baseUrl}/projects.json");

            $projects = $response->json();

            foreach ($projects as $projectData) {
                Project::updateOrCreate(
                    ['id' => $projectData['id']],
                    [
                        'name' => $projectData['name'],
                        'description' => $projectData['description'] ?? '',
                        'status' => $projectData['status'] === 'active'
                    ]
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing Worksnaps projects: ' . $e->getMessage());
            throw $e;
        }
    }

    public function syncTimeEntries(Carbon $startDate, Carbon $endDate)
    {
        try {
            $users = WorksnapUser::all();

            foreach ($users as $user) {
                $response = Http::withBasicAuth($this->apiKey, 'X')
                    ->get("{$this->baseUrl}/users/{$user->id}/time_entries.json", [
                        'from_timestamp' => $startDate->timestamp,
                        'to_timestamp' => $endDate->timestamp
                    ]);

                $timeEntries = $response->json();

                foreach ($timeEntries as $entry) {
                    Timming::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'from_timestamp' => $entry['start_timestamp'],
                            'project_id' => $entry['project_id']
                        ],
                        [
                            'logged_timestamp' => $entry['logged_timestamp'] ?? null,
                            'minutes' => ceil($entry['duration_seconds'] / 60),
                            'type' => $entry['type'] ?? 'regular',
                            'task_id' => $entry['task_id'] ?? 0,
                            'task_name' => $entry['task_name'] ?? '',
                            'thumbnail_url' => $entry['thumbnail_url'] ?? '',
                            'full_resolution_url' => $entry['full_resolution_url'] ?? '',
                            'activity_level' => $entry['activity_level'] ?? 0,
                            'activities' => json_encode($entry['activities'] ?? [])
                        ]
                    );
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing Worksnaps time entries: ' . $e->getMessage());
            throw $e;
        }
    }
} 