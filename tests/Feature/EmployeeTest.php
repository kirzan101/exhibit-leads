<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
// use Illuminate\Foundation\Testing\DatabaseMigrations;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(TestSeeder::class);

        $user = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => 1
        ]);

        $this->user = $user;
    }

    /**
     * test can view index of employees
     *
     * @return void
     */
    public function test_can_view_employee_index()
    {
        // seed data
        // $this->seed(TestSeeder::class);

        $users = User::factory(5)->create();
        foreach ($users as $user) {
            Employee::factory()->create([
                'user_id' => $user->id,
                'user_group_id' => 2
            ]);
        }

        $this->actingAs($this->user);

        $response = $this->get('/employees');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Employees/IndexPaginateEmployee')
                ->has('items', 3) // 3 because, 2 admin account is excluded in results
        );
    }

    /**
     * test can view employee show
     *
     * @return void
     */
    public function test_can_view_employee_show()
    {
        // $this->seed(TestSeeder::class);

        // create two records
        $users = User::factory(2)->create();
        foreach ($users as $user) {
            Employee::factory()->create([
                'user_id' => $user->id,
                'user_group_id' => 1
            ]);
        }

        // get other records aside from admin accounts
        $employee = Employee::find(2);

        $employee = tap($employee)->update([
            'user_group_id' => 2
        ]);

        // get the admin account
        $this->actingAs($this->user);

        $this->get(route('employees.show', $employee))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Employees/ShowEmployee')
                    ->has(
                        'employee',
                        fn (Assert $page) => $page
                            ->where('first_name', $employee->first_name)
                            ->where('middle_name', $employee->middle_name)
                            ->where('last_name', $employee->last_name)
                            ->where('position', $employee->position)
                            ->where('property', $employee->property)
                            ->where('user_group_id', $employee->user_group_id)
                            ->where('property_id', $employee->property_id)
                            ->where('user_group', $employee->userGroup)
                            ->where('employee_venues', $employee->employeeVenue)
                            ->where('user', $employee->user)
                            ->where('full_name', $employee->getFullName())
                            ->where('id', $employee->id)
                            ->where('venue_id', $employee->venue_id)
                            ->where('exhibitor_id', $employee->exhibitor_id)
                            ->etc()
                            // ->where('is_active', $employee->user->is_active)
                            // ->where('user.is_password_changed', $employee->user->is_password_changed)
                    )
            );
    }

    /**
     * test create employee
     *
     * @return void
     */
    public function test_can_view_employee_create()
    {
        // $this->seed(TestSeeder::class);

        // get the admin account
        $this->actingAs($this->user);

        $this->get(route('employees.create'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Employees/CreateEmployee')
            );

        // $data = [
        //     'first_name' => 'first',
        //     'last_name' => 'last',
        //     'position' => 'test',
        //     'property_id' => 1,
        //     'email' => 'first.last@email.com',
        //     'user_group_id' => 1,
        //     'venue_ids' => [1, 2],
        //     'password' => 'password',
        //     'is_active' => true
        // ];
        
        // $this->post('/employees', [
        //     'first_name' => 'first',
        //     'last_name' => 'last',
        //     'position' => 'test',
        //     'property_id' => 1,
        //     'email' => 'first.last@email.com',
        //     'user_group_id' => 1,
        //     'venue_ids' => [1, 2],
        //     'password' => 'password',
        //     'is_active' => true
        // ])->assertFound();

    }

    public function test_can_view_employee_edit()
    {
        $this->actingAs($this->user);

        // create two records
        $users = User::factory(2)->create();
        foreach ($users as $user) {
            Employee::factory()->create([
                'user_id' => $user->id,
                'user_group_id' => 1
            ]);
        }

        // get other records aside from admin accounts
        $employee = Employee::find(2);

        $employee = tap($employee)->update([
            'user_group_id' => 2
        ]);

        $this->get('/employees/' . $employee->id . '/edit')
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Employees/EditEmployee')
            );
    }
}
