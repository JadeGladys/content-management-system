<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

class MediaService
{
    public function getPaginatedMedia(?string $search): LengthAwarePaginator
    {
        return Media::query()
            ->with([
                'uploadedBy:id,name',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('file_name', 'ilike', "%{$search}%")
                        ->orWhere('file_path', 'ilike', "%{$search}%")
                        ->orWhereHas('uploadedBy', function ($uploaded_byQuery) use ($search) {
                            $uploaded_byQuery->where('name', 'ilike', "%{$search}%");
                        });
                });
            })
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
    }

    public function storeMediaUpload(User $actor, UploadedFile $file): Media
    {
        try {
            $hash = hash_file('sha256', $file->getRealPath());
            $existingMedia = Media::query()->where('file_hash', $hash)->first();

            if ($existingMedia) {
                $existingMedia->forceFill([
                    'updated_by' => $actor->id,
                ])->save();

                Log::info('Duplicate media upload reused existing asset.', [
                    'actor_id' => $actor->id,
                    'media_id' => $existingMedia->id,
                    'file_name' => $existingMedia->file_name,
                    'status' => 'duplicate_reused',
                ]);

                return $existingMedia;
            }

            $extension = $file->guessExtension() ?? $file->extension() ?? 'bin';
            $storedFileName = "{$hash}.{$extension}";
            $path = $file->storePubliclyAs('media/library', $storedFileName, 'public');

            $media = Media::create([
                'file_name' => $file->getClientOriginalName(),
                'file_hash' => $hash,
                'file_path' => $path,
                'file_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            Log::info('Media uploaded.', [
                'actor_id' => $actor->id,
                'media_id' => $media->id,
                'file_name' => $media->file_name,
                'status' => 'success',
            ]);

            return $media;
        } catch (Throwable $exception) {
            Log::error('Media upload failed.', [
                'actor_id' => $actor->id,
                'file_name' => $file->getClientOriginalName(),
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
