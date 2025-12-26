<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Attachment\StoreAttachmentRequest;
use App\Http\Resources\Api\V1\Attachment\AttachmentResource;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class TaskAttachmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Task $task): AnonymousResourceCollection
    {
        $this->authorize('view', [$project, $task]);

        $attachments = $task->attachments()->with('user')->get();

        Log::info('Attachments data being sent to frontend:', $attachments->toArray());

        return AttachmentResource::collection($attachments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttachmentRequest $request, Task $task)
    {
        $this->authorize('create', Attachment::class);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');
        $thumbnailPath = null; // Initialize thumbnail path

        // Check if the uploaded file is an image
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $thumbnailDir = 'attachments/thumbnails';
            Storage::disk('public')->makeDirectory($thumbnailDir); // Ensure directory exists

            $manager = ImageManager::gd();
            $image = $manager->read(Storage::disk('public')->path($path));
            $thumbnailFileName = 'thumbnail_' . $file->hashName();
            $thumbnailPath = $thumbnailDir . '/' . $thumbnailFileName;

            // Resize and save thumbnail
            $image->scaleDown(300, 300)->save(Storage::disk('public')->path($thumbnailPath));
        }

        $attachment = $task->attachments()->create([
            'user_id' => Auth::id(),
            'path' => $path,
            'thumbnail_path' => $thumbnailPath, // Store thumbnail path
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return AttachmentResource::make($attachment);
    }
}
