<?php

namespace App\Http\Services;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class FileService
{
    public function uploadFiles($files, $commentId)
    {
        if ($files !== null && count($files) > 0) {
            foreach ($files as $file) {

                $filename = $file->getClientOriginalName();
                $newFilename = uniqid() . '_' . $filename;

                if (in_array($file->getClientOriginalExtension(), ['jpg', 'gif', 'png'])) {

                    $image = Image::make($file);
                    $image->resize(240 , 320, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $image->save(storage_path('app/public/uploads/' . $newFilename));

                    File::create([
                        'comment_id'=> $commentId,
                        'name' => $newFilename,
                    ]);

                    continue;
                }

                $file->move(storage_path('app/public/uploads/'), $newFilename);

                File::create([
                    'comment_id'=> $commentId,
                    'name' => $newFilename,
                ]);
            }
        }
    }
}
