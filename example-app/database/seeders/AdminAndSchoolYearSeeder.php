<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\Hash;

class AdminAndSchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        $admins = [
            [
                'email' => 'admin1@example.com',
                'username' => 'admin1',
                'password' => Hash::make('password123'),
                'role_id' => 1,
            ],
            [
                'email' => 'admin2@example.com',
                'username' => 'admin2',
                'password' => Hash::make('password123'),
                'role_id' => 1,
            ],
            [
                'email' => 'admin3@example.com',
                'username' => 'admin3',
                'password' => Hash::make('password123'),
                'role_id' => 1,
            ],
        ];

        $createdAdmins = [];
        foreach ($admins as $admin) {
            $createdAdmins[] = User::create($admin);
        }

        // Create school years
        $schoolYears = [
            [
                'status' => 'Active',
                'enrollment_status' => 'Enrollment Available',
                'school_year' => 'S.Y. 2023-2024',
                'school_start' => '2023-08-01',
                'school_end' => '2024-05-31',
                'enrollment_start' => '2023-06-01',
                'enrollment_end' => '2023-07-31',
            ],
            [
                'status' => 'Inactive',
                'enrollment_status' => 'Enrollment Closed',
                'school_year' => 'S.Y. 2024-2025',
                'school_start' => '2024-08-01',
                'school_end' => '2025-05-31',
                'enrollment_start' => '2024-06-01',
                'enrollment_end' => '2024-07-31',
            ],
            [
                'status' => 'Pending',
                'enrollment_status' => 'Not Yet Open',
                'school_year' => 'S.Y. 2025-2026',
                'school_start' => '2025-08-01',
                'school_end' => '2026-05-31',
                'enrollment_start' => '2025-06-01',
                'enrollment_end' => '2025-07-31',
            ],
        ];

        foreach ($schoolYears as $index => $schoolYear) {
            // Assign an admin to each school year (cycling through the created admins)
            $adminId = $createdAdmins[$index % count($createdAdmins)]->id;
            $schoolYear['admin_id'] = $adminId;
            SchoolYear::create($schoolYear);
        }
    }
}
