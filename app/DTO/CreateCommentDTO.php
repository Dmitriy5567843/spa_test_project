<?php

namespace App\DTO;

class CreateCommentDTO
{
    private string $name;

    private string $email;

    private string $content;

    private ?int $parent_id;


    public function __construct(string $name, string $email, string $content,?int $parent_id)
    {
        $this->name = $name;
        $this->email = $email;
        $this->content = $content;
        $this->parent_id = $parent_id;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parent_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }



}
