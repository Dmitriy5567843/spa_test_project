<?php

namespace App\Http\Controllers;

use App\DTO\CreateCommentDTO;
use App\Http\Requests\CreateRequest;
use App\Http\Requests\CreateWithParentRequest;
use App\Http\Services\CommentService;
use App\Http\Services\FileService;
use App\Models\Comment;
use App\Models\File;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class CommentController extends Controller
{
    public function create(CommentService $commentService, FileService $fileService, CreateRequest $request,): RedirectResponse
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
            ->orderBy('created_at', 'desc')
            ->paginate(25);
    //    $file = Comment::with('files')->get();

        foreach ($comments as $comment) {
            $comment->children = $this->getChildren($comment->id);
           // $comment->filePaths = $comment->files->pluck('name')->toArray();
        }


        return view('welcome', ['comments' => $comments]);
    }

    public function store(CommentService $commentService, FileService $fileService, CreateWithParentRequest $request): RedirectResponse
    {

      $comment = $commentService->ModalFormCreate(new CreateCommentDTO(
            $request->get('name'),
            $request->get('email'),
            $request->get('content'),
            $request->get('parent_id'),
        ));

        try {
            $files = $request->file();
            $fileService->uploadFiles($files, $comment->id);

            return redirect()->route('comment.index')->with('success', 'Comment added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Comment could not be added: ' . $e->getMessage());
        }
    }

    private function getChildren($parentId): Collection
    {
        $children = Comment::where('parent_id', $parentId)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($children as $child) {
            $child->children = $this->getChildren($child->id);
        }

        return $children;
    }
}
