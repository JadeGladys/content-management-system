<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Services\Setting\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThemeController extends Controller
{
    public function __construct(
        protected ThemeService $themeService
    ) {}

    public function index(): View
    {
        return view('settings.theme', [
            'themes' => $this->themeService->getAllThemes(),
            'activeTheme' => $this->themeService->getActiveTheme(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $this->themeService->setTheme($request->string('theme')->toString());
        } catch (\InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('theme.index')
            ->with('success', 'Theme updated successfully.');
    }
}