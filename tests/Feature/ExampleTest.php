<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_grade_scale_create_page(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $response = $this->actingAs($user)->get('/grading/scale/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_grading_edit_page(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $response = $this->actingAs($user)->get('/grading/edit');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_student_subject_assignments_page(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $response = $this->actingAs($user)->get('/subjects/student-subjects');

        $response->assertStatus(200);
    }
}
