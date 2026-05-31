<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Services\Media\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(
        protected MediaService $mediaService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $viewMode = $request->string('view')->toString() === 'grid' ? 'grid' : 'list';

        return view('media.index', [
            'media' => $this->mediaService->getPaginatedMedia($search),
            'search' => $search,
            'viewMode' => $viewMode,
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        try {
            $media = $this->mediaService->storeMediaUpload(
                $request->user(),
                $request->file('media_upload')
            );

            return redirect()
                ->route('media.index', array_filter([
                    'search' => $request->string('search')->toString(),
                    'view' => $request->string('view')->toString(),
                ]))
                ->with(
                    'success',
                    $media->wasRecentlyCreated
                        ? 'Image uploaded successfully.'
                        : 'This image already exists in the media library, so the existing asset was reused.'
                );
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while uploading the image. Please try again.');
        }
    }
}
