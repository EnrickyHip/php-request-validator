<?php

namespace Enricky\RequestValidator\Abstract;

use Enricky\RequestValidator\Enums\FileType;

interface FileInterface
{
    /** @return string The file's name. */
    public function getName(): string;

    /** @return string The file's full path. */
    public function getFullPath(): string;

    /** @return FileType|null The file's type. */
    public function getType(): ?FileType;

    /** @return string The file's temporarily name. */
    public function getTempName(): string;

    /** @return int Return file error code. */
    public function getError(): int;

    /** @return int The file size in bytes. */
    public function getSize(): int;
}
