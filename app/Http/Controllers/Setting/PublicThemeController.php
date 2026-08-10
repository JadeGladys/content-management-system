<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Services\Setting\ThemeService;
use Illuminate\Http\JsonResponse;

class PublicThemeController extends Controller
{
    public function __construct(
        protected ThemeService $themeService
    ) {
    }

    public function show(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'theme' => $this->themeService->getActiveTheme(),
            ],
        ]);
    }
}
