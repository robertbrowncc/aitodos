<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $activities = Activity::with('person')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return $this->resourceCollectionResponse(
            ActivityResource::collection($activities),
            'Activities retrieved successfully'
        );
    }

    public function store(StoreActivityRequest $request): JsonResponse
    {
        $activity = Activity::create($request->validated());

        return $this->resourceResponse(
            new ActivityResource($activity->load('person')),
            'Activity created successfully',
            201
        );
    }

    public function show(Activity $activity): JsonResponse
    {
        return $this->resourceResponse(
            new ActivityResource($activity->load('person')),
            'Activity retrieved successfully'
        );
    }

    public function update(UpdateActivityRequest $request, Activity $activity): JsonResponse
    {
        $activity->update($request->validated());

        return $this->resourceResponse(
            new ActivityResource($activity->fresh()->load('person')),
            'Activity updated successfully'
        );
    }

    public function destroy(Activity $activity): JsonResponse
    {
        $activity->delete();
        return $this->noContentResponse();
    }
}
