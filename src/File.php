<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\FileInterface;
use Enricky\RequestValidator\Types\FileType;

/**
 * Internal class to represent a multipart form-data file coming from $_POST.
 * @internal
 */
class File implements FileInterface
{
    private string $name = "";
    private string $path = "";
    private ?FileType $type = null;
    private string $tempName = "";
    private int $error = UPLOAD_ERR_NO_FILE;
    private int $size = 0;

    public function __construct(mixed $file)
    {
        if (!is_array($file)) {
            return;
        }

        $this->name = $file["name"] ?? "";
        $this->path = $file["full_path"] ?? "";
        $this->tempName = $file["tmp_name"] ?? "";
        $this->error = $file["error"] ?? UPLOAD_ERR_NO_FILE;
        $this->size = $file["size"] ?? 0;
        $this->type = FileType::tryFrom($file["type"] ?? "");
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
