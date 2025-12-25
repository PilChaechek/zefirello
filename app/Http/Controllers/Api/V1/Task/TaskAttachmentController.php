<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Attachment\StoreAttachmentRequest;
use App\Http\Resources\Api\V1\Attachment\AttachmentResource;
use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttachmentRequest $request, Task $task)
    {
        $this->authorize('create', Attachment::class);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = $task->attachments()->create([
            'user_id' => Auth::id(),
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return AttachmentResource::make($attachment);
    }
}
