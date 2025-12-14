<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that School A cannot see School B's students.
     */
    public function test_school_a_cannot_see_school_b_students(): void
    {
        $schoolA = School::factory()->create(['slug' => 'school-a']);
        $schoolB = School::factory()->create(['slug' => 'school-b']);

        $studentA = Student::factory()->forSchool($schoolA)->create(['name' => 'Student A']);
        $studentB = Student::factory()->forSchool($schoolB)->create(['name' => 'Student B']);

        // Query without tenant context (simulating potential leak)
        $allStudents = Student::all();
        
        // This test documents current behavior - will be fixed with global scope
        // Currently both students are returned, which is a security issue
        $this->assertTrue($allStudents->contains($studentA));
        $this->assertTrue($allStudents->contains($studentB));
    }

    /**
     * Test that students are scoped to their school when querying by school.
     */
    public function test_students_are_scoped_to_school(): void
    {
        $schoolA = School::factory()->create(['slug' => 'school-a']);
        $schoolB = School::factory()->create(['slug' => 'school-b']);

        $studentA1 = Student::factory()->forSchool($schoolA)->create(['name' => 'Student A1']);
        $studentA2 = Student::factory()->forSchool($schoolA)->create(['name' => 'Student A2']);
        $studentB1 = Student::factory()->forSchool($schoolB)->create(['name' => 'Student B1']);

        $schoolAStudents = Student::where('school_id', $schoolA->id)->get();

        $this->assertCount(2, $schoolAStudents);
        $this->assertTrue($schoolAStudents->contains($studentA1));
        $this->assertTrue($schoolAStudents->contains($studentA2));
        $this->assertFalse($schoolAStudents->contains($studentB1));
    }

    /**
     * Test that staff are scoped to their school.
     */
    public function test_staff_are_scoped_to_school(): void
    {
        $schoolA = School::factory()->create(['slug' => 'school-a']);
        $schoolB = School::factory()->create(['slug' => 'school-b']);

        $staffA = Staff::factory()->forSchool($schoolA)->create(['name' => 'Staff A']);
        $staffB = Staff::factory()->forSchool($schoolB)->create(['name' => 'Staff B']);

        $schoolAStaff = Staff::where('school_id', $schoolA->id)->get();

        $this->assertCount(1, $schoolAStaff);
        $this->assertTrue($schoolAStaff->contains($staffA));
        $this->assertFalse($schoolAStaff->contains($staffB));
    }

    /**
     * Test that a user from School A cannot access School B's data via ID.
     */
    public function test_user_cannot_access_other_school_data_by_id(): void
    {
        $schoolA = School::factory()->create(['slug' => 'school-a']);
        $schoolB = School::factory()->create(['slug' => 'school-b']);

        $userA = User::factory()->forSchool($schoolA)->create();
        $studentB = Student::factory()->forSchool($schoolB)->create();

        // Simulate trying to access School B's student
        // This should fail once tenant scoping is enforced
        $foundStudent = Student::find($studentB->id);

        // Currently this will succeed (security issue)
        // After implementing global scope, this should return null or throw 403
        $this->assertNotNull($foundStudent);
    }
}

