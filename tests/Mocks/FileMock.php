<?php

use Enricky\RequestValidator\Abstract\FileInterface;
use Enricky\RequestValidator\Types\FileType;

class FileMock implements FileInterface
{
    public function __construct(
        private string $name = "",
        private string $path = "",
        private ?FileType $type = null,
        private string $tempName = "",
        private int $error = 0,
        private int $size = 0
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFullPath(): string
    {
        return $this->path;
    }

    public function getType(): ?FileType
    {
        return $this->type;
    }

    public function getTempName(): string
    {
        return $this->tempName;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
