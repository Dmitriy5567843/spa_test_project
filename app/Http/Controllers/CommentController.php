<?php

namespace App\Http\Controllers;

use App\DTO\CreateCommentDTO;
use App\Http\Requests\CreateRequest;
use App\Http\Requests\CreateWithParentRequest;
use App\Http\Services\CommentService;
use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class CommentController extends Controller
{
    public function create(CommentService $commentService, CreateRequest $request): RedirectResponse
    {
        $commentService->SimpleFormCreate(new CreateCommentDTO(
            $request->get('name'),
            $request->get('email'),
            $request->get('content'),
            null
        ));

        return redirect()->route('comment.index');
    }

    public function index(): View
    {
        $comments = Comment::whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        foreach ($comments as $comment) {
            $comment->children = $this->getChildren($comment->id);
        }

        return view('welcome', ['comments' => $comments]);
    }

    public function store(CommentService $commentService, CreateWithParentRequest $request): RedirectResponse
    {
        $commentService->ModalFormCreate(new CreateCommentDTO(
            $request->get('name'),
            $request->get('email'),
            $request->get('content'),
            $request->get('parent_id'),
        ));

        return redirect()->route('comment.index');
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
