<?php

namespace App\Http\Controllers\Career;

use App\Http\Controllers\Controller;
use App\Services\Career\CareerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function __construct(
        protected CareerService $careerService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        return view('careers.index', [
            'careers' => $this->careerService->getPaginatedCareers($search, $request->user()),
            'search' => $search,
        ]);
    }
}
