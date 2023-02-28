<?php

namespace App\Http\Services;

use App\Models\File;

class FileService
{
    public function uploadFiles($files, $commentId)
    {
        if ($files !== null && count($files) > 0) {
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $filename = $file->getClientOriginalName();
                    $newFilename = uniqid() . '_' . $filename;
                    $file->storeAs('public/uploads', $newFilename);
                    File::create([
                        'comment_id'=> $commentId,
                        'name' => $newFilename,
                    ]);
                }
            }
        }
    }
}
