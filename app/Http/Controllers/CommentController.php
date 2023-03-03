<?php

namespace App\Http\Controllers;

use App\DTO\CreateCommentDTO;
use App\Http\Requests\CreateRequest;
use App\Http\Requests\CreateWithParentRequest;
use App\Http\Services\CommentService;
use App\Http\Services\FileService;
use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function create(CreateRequest $request, CommentService $commentService, FileService $fileService): RedirectResponse
    {
            $comment = $commentService->SimpleFormCreate(new CreateCommentDTO(
                $request->get('name'),
                $request->get('email'),
                $request->get('content'),
                null
            ));

        try {
            $files = $request->file('files');
            $fileService->uploadFiles($files, $comment->id);

            return redirect()->route('comment.index')->with('success', 'Comment added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Comment could not be added: ' . $e->getMessage());
        }
    }

    public function index(): View
    {
        $comments = Comment::whereNull('parent_id')
            ->with('files')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        foreach ($comments as $comment) {
            if (!empty($comment->files)) {
                foreach ($comment->files as $file) {
                    $fileType = pathinfo($file->name, PATHINFO_EXTENSION);
                    $file->setAttribute('type', pathinfo($file->name, PATHINFO_EXTENSION));

                    if ($fileType !== 'txt') {
                        $file->setAttribute('base_64', Storage::get("public/uploads/$file->name"));
                    }
                }
            }
            $comment->children = $this->getChildren($comment->id);
        }

        return view('welcome', ['comments' => $comments]);
    }


    public function store(CreateWithParentRequest $request, CommentService $commentService, FileService $fileService): RedirectResponse
    {

      $comment = $commentService->ModalFormCreate(new CreateCommentDTO(
            $request->get('name'),
            $request->get('email'),
            $request->get('content'),
            $request->get('parent_id'),
        ));

        try {
            $files = $request->file('files');
            $fileService->uploadFiles($files, $comment->id);

            return redirect()->route('comment.index')->with('success', 'Comment added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Comment could not be added: ' . $e->getMessage());
        }
    }

    public function download(string $fileName)
    {
        return response()->download(storage_path('app/public/uploads/' . $fileName));

    }

    private function getChildren($parentId): Collection
    {
        $children = Comment::where('parent_id', $parentId)
            ->with('files')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($children as $child) {
            if (!empty($child->files)) {
                foreach ($child->files as $file) {
                    $fileType = pathinfo($file->name, PATHINFO_EXTENSION);
                    $file->setAttribute('type', pathinfo($file->name, PATHINFO_EXTENSION));

                    if ($fileType !== 'txt') {
                        $file->setAttribute('base_64', Storage::get("public/uploads/$file->name"));
                    }
                }
            }
            $child->children = $this->getChildren($child->id);
        }

        return $children;
    }
}
