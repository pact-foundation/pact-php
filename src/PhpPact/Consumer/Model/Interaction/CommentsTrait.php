<?php

namespace PhpPact\Consumer\Model\Interaction;

trait CommentsTrait
{
    /**
     * @var array<string, mixed>
     */
    private array $comments = [];

    /**
     * @var string[]
     */
    private array $textComments = [];

    /**
     * @return array<string, mixed>
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param array<string, mixed> $comments
     */
    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return string[]
     */
    public function getTextComments(): array
    {
        return $this->textComments;
    }

    /**
     * @param string $comment
     */
    public function addTextComment(string $comment): void
    {
        $this->textComments[] = $comment;
    }
}
