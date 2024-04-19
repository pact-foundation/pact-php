<?php

namespace PhpPact\Consumer\Model\Interaction;

trait CommentsTrait
{
    /**
     * @var string[]
     */
    private array $comments = [];

    /**
     * @return string[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param string[] $comments
     */
    public function setComments(array $comments): self
    {
        $this->comments = [];
        foreach ($comments as $value) {
            $this->addComment($value);
        }

        return $this;
    }

    public function addComment(string $comment): void
    {
        $this->comments[] = $comment;
    }
}
