<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Models\Media;
use App\Models\User;
use App\Services\Media\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class MediaController extends Controller
{
    protected const MAX_UPLOAD_SIZE_BYTES = 5 * 1024 * 1024;

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
            'searchSuggestions' => Media::query()
                ->whereNotNull('file_name')
                ->orderBy('file_name')
                ->pluck('file_name')
                ->merge(
                    User::query()
                        ->whereIn('id', Media::query()->select('uploaded_by'))
                        ->orderBy('name')
                        ->pluck('name')
                )
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $guardResponse = $this->ensureFileSizeIsValid($request->file('media_upload'));

        if ($guardResponse) {
            return $guardResponse;
        }

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

    protected function ensureFileSizeIsValid(UploadedFile $file): ?RedirectResponse
    {
        if ($file->getSize() > self::MAX_UPLOAD_SIZE_BYTES) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Images must be smaller than 5 MB.');
        }

        return null;
    }
}
