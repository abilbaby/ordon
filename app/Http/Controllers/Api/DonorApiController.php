<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonorResource;
use App\Models\Donor;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DonorApiController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return DonorResource::collection(
            Donor::with('user')->latest()->paginate(20)
        );
    }
}
