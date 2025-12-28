<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageController extends Controller
{
    // линк для фронта
    /**
     * @param Image $image
     * @return BinaryFileResponse
     */
    public function view(Image $image)
    {
        if (!Storage::disk('public')->exists($image->path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($image->path));
    }

    // скачивание

    /**
     * @param Image $image
     * @return StreamedResponse
     */
    public function download(Image $image)
    {
        if (!Storage::disk('public')->exists($image->path)) {
            abort(404);
        }

        return Storage::disk('public')->download($image->path);
    }
}
