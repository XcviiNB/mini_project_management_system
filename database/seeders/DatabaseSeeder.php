<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //Users
        $admin = User::create([
            'name'      =>  'Admin User',
            'email'     =>  'admin@example.com',
            'password'  =>   Hash::make('adminpassword'),
            'role'      =>  'admin',
        ]);

        $manager = User::create([
            'name'      =>  'Manager User',
            'email'     =>  'manager@example.com',
            'password'  =>   Hash::make('managerpassword'),
            'role'      =>  'manager',
        ]);

        $developer = User::create([
            'name'      =>  'developer User',
            'email'     =>  'developer@example.com',
            'password'  =>   Hash::make('developerpassword'),
            'role'      =>  'developer',
        ]);

        //Projects
        $project1 = Project::create([
            'name'          => 'Project Alpha: Website Redesign',
            'description'   => 'Redesign the company website with modern UI/UX.',
            'start_date'    => '2024-01-15',
            'end_date'      => '2024-06-30',
            'user_id'       => $admin->id,
        ]);

        $project2 = Project::create([
            'name' => 'Project Beta: Mobile App Development',
            'description' => 'Develop a cross-platform mobile app for inventory management.',
            'start_date' => '2024-02-01',
            'end_date' => null,
            'user_id' => $admin->id,
        ]);

        //Tasks
        Task::create([
            'project_id' => $project1->id,
            'assigned_to' => $developer->id,
            'title' => 'Design Wireframes',
            'status' => 'pending',
            'due_date' => '2024-01-20',
        ]);
        Task::create([
            'project_id' => $project1->id,
            'assigned_to' => $manager->id,
            'title' => 'Implement Homepage Layout',
            'status' => 'in-progress',
            'due_date' => '2024-02-15',
        ]);
        Task::create([
            'project_id' => $project1->id,
            'assigned_to' => $developer->id,
            'title' => 'Test Responsive Features',
            'status' => 'completed',
            'due_date' => '2024-03-01',
        ]);
        Task::create([
            'project_id' => $project2->id,
            'assigned_to' => $developer->id,
            'title' => 'Set Up Backend API',
            'status' => 'pending',
            'due_date' => '2024-02-10',
        ]);
        Task::create([
            'project_id' => $project2->id,
            'assigned_to' => $manager->id,
            'title' => 'Integrate User Authentication',
            'status' => 'in-progress',
            'due_date' => '2024-03-15',
        ]);
    }
}
