<?php

namespace App\Http\Controllers\Api\V1\Attachment;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return response()->noContent();
    }
}
