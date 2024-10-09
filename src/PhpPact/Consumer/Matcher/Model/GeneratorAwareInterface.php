<?php

namespace PhpPact\Consumer\Matcher\Model;

use PhpPact\Consumer\Matcher\Model\GeneratorInterface;

interface GeneratorAwareInterface
{
    public function setGenerator(?GeneratorInterface $generator): void;

    public function getGenerator(): ?GeneratorInterface;

    public function withGenerator(?GeneratorInterface $generator): static;
}
