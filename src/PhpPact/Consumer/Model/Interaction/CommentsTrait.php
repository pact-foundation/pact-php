<?php

namespace PhpPact\Consumer\Model\Interaction;

trait CommentsTrait
{
    /**
     * @var array<string, string>
     */
    private array $comments = [];

    /**
     * @return array<string, string>
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param array<string, string> $comments
     */
    public function setComments(array $comments): self
    {
        $this->comments = [];
        foreach ($comments as $key => $value) {
            $this->comments[$key] = $value;
        }

        return $this;
    }
}
