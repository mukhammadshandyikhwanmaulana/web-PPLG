<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'action'       => 'create',
            'description'  => 'Dummy activity log entry for testing.',
            'subject_type' => null,
            'subject_id'   => null,
            'properties'   => ['note' => 'sample'],
            'ip_address'   => '127.0.0.1',
            'user_agent'   => 'Mozilla/5.0 (Testing) ActivityLogFactory',
            'created_at'   => now(),
        ];
    }
}