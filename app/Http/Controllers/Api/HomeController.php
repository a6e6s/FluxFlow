<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Home\HomeInsights;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HomePeriodRequest;
use App\Http\Resources\EffortLoadResource;
use App\Http\Resources\ProjectProgressResource;
use App\Http\Resources\TaskInvitationResource;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomeController extends Controller
{
    public function summary(HomePeriodRequest $request, HomeInsights $insights): JsonResponse
    {
        $period = $request->range();

        return response()->json([
            'data' => [
                'period' => $period,
                ...$insights->summary($request->user(), $period['start'], $period['end']),
            ],
        ]);
    }

    public function dueSoon(Request $request, HomeInsights $insights): AnonymousResourceCollection
    {
        return TaskResource::collection($insights->dueSoon($request->user()));
    }

    public function upcomingDeadlines(Request $request, HomeInsights $insights): AnonymousResourceCollection
    {
        return TaskResource::collection($insights->upcomingDeadlines($request->user()));
    }

    public function myTasks(Request $request, HomeInsights $insights): AnonymousResourceCollection
    {
        return TaskResource::collection($insights->myTasks($request->user()));
    }

    public function projectProgress(Request $request, HomeInsights $insights): AnonymousResourceCollection
    {
        return ProjectProgressResource::collection($insights->projectProgress($request->user()));
    }

    public function effortLoad(HomePeriodRequest $request, HomeInsights $insights): AnonymousResourceCollection
    {
        $period = $request->range();

        return EffortLoadResource::collection(
            $insights->effortLoad($request->user(), $period['start'], $period['end'])
        );
    }

    public function recentActivity(Request $request, HomeInsights $insights): AnonymousResourceCollection
    {
        return TaskResource::collection($insights->recentActivity($request->user()));
    }

    public function invitations(Request $request, HomeInsights $insights): AnonymousResourceCollection
    {
        return TaskInvitationResource::collection($insights->pendingInvitations($request->user()));
    }
}
