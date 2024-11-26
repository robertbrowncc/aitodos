<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use App\Models\Person;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use App\Http\Resources\PersonResource;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Controllers\Api\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $todos = Todo::with(['person' => function($query) {
            $query->select('id', 'name', 'first_name', 'last_name');
        }])->latest()->get();

        return $this->resourceCollectionResponse(
            TodoResource::collection($todos),
            'Todos retrieved successfully'
        );
    }

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = Todo::create($request->validated());

        return $this->resourceResponse(
            new TodoResource($todo->load('person')),
            'Todo created successfully',
            201
        );
    }

    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        $todo->update($request->validated());

        return $this->resourceResponse(
            new TodoResource($todo->load('person')),
            'Todo updated successfully'
        );
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();
        return $this->noContentResponse();
    }

    public function getPeople(): JsonResponse
    {
        $people = Person::select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return $this->resourceCollectionResponse(
            PersonResource::collection($people),
            'People retrieved successfully'
        );
    }
}
