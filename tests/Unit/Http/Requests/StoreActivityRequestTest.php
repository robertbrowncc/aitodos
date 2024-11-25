<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Person;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreActivityRequestTest extends TestCase
{
    private StoreActivityRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreActivityRequest();
    }

    public function test_authorization_is_allowed()
    {
        $this->assertTrue($this->request->authorize());
    }

    public function test_validation_rules_exist_for_required_fields()
    {
        $rules = $this->request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('person_id', $rules);
        $this->assertArrayHasKey('start_time', $rules);
        $this->assertArrayHasKey('end_time', $rules);
        $this->assertArrayHasKey('day_of_week', $rules);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function test_validation_passes_with_valid_data($data)
    {
        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertTrue($validator->passes());
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function test_validation_fails_with_invalid_data($data, $expectedErrors)
    {
        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertEquals($expectedErrors, array_keys($validator->errors()->toArray()));
    }

    public static function validDataProvider()
    {
        return [
            'valid data set 1' => [
                [
                    'name' => 'Test Activity',
                    'person_id' => 1,
                    'start_time' => '09:00',
                    'end_time' => '10:00',
                    'day_of_week' => 'Monday',
                ],
            ],
            'valid data set 2' => [
                [
                    'name' => 'Another Activity',
                    'person_id' => 2,
                    'start_time' => '13:30',
                    'end_time' => '14:30',
                    'day_of_week' => 'Friday',
                ],
            ],
        ];
    }

    public static function invalidDataProvider()
    {
        return [
            'missing all fields' => [
                [],
                ['name', 'person_id', 'start_time', 'end_time', 'day_of_week']
            ],
            'invalid name' => [
                [
                    'name' => '',
                    'person_id' => 1,
                    'start_time' => '09:00',
                    'end_time' => '10:00',
                    'day_of_week' => 'Monday',
                ],
                ['name']
            ],
            'invalid person_id' => [
                [
                    'name' => 'Test Activity',
                    'person_id' => 'invalid',
                    'start_time' => '09:00',
                    'end_time' => '10:00',
                    'day_of_week' => 'Monday',
                ],
                ['person_id']
            ],
            'invalid time format' => [
                [
                    'name' => 'Test Activity',
                    'person_id' => 1,
                    'start_time' => 'invalid',
                    'end_time' => '10:00',
                    'day_of_week' => 'Monday',
                ],
                ['start_time', 'end_time']
            ],
            'end time before start time' => [
                [
                    'name' => 'Test Activity',
                    'person_id' => 1,
                    'start_time' => '10:00',
                    'end_time' => '09:00',
                    'day_of_week' => 'Monday',
                ],
                ['end_time']
            ],
            'invalid day of week' => [
                [
                    'name' => 'Test Activity',
                    'person_id' => 1,
                    'start_time' => '09:00',
                    'end_time' => '10:00',
                    'day_of_week' => 'InvalidDay',
                ],
                ['day_of_week']
            ],
        ];
    }
}
