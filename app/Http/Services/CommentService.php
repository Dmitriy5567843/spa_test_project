<?php

namespace App\Http\Services;

use App\DTO\CreateCommentDTO;
use App\Models\Comment;

class CommentService
{
    public function SimpleFormCreate(CreateCommentDTO $dto): Comment
    {
        return Comment::create([
            'name' => $dto->getName(),
            'email' => $dto->getEmail(),
            'content' => $dto->getContent(),
            null
        ]);
    }
    public function ModalFormCreate(CreateCommentDTO $dto): Comment
    {
        return Comment::create([
            'name' => $dto->getName(),
            'email' => $dto->getEmail(),
            'content' => $dto->getContent(),
            'parent_id' => $dto->getParentId(),
        ]);
    }
}
