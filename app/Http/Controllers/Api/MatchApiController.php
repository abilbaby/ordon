<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\AllocationMatch;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MatchApiController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return MatchResource::collection(
            AllocationMatch::with(['donor.user', 'recipient.user'])->latest()->paginate(20)
        );
    }
}
