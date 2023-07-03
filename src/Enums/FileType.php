<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Enums;

use Enricky\RequestValidator\Exceptions\InvalidExtensionException;

/** Enum representation of the data types that can be handled by the validators. */
enum FileType: string
{
    case AAC = "audio/aac";
    case ABW = "application/x-abiword";
    case ARC = "application/x-freearc";
    case AVIF = "image/avif";
    case AVI = "video/x-msvideo";
    case AZW = "application/vnd.amazon.ebook";
    case BIN = "application/octet-stream";
    case BMP = "image/bmp";
    case BZ = "application/x-bzip";
    case BZ2 = "application/x-bzip2";
    case CDA = "application/x-cdf";
    case CSH = "application/x-csh";
    case CSS = "text/css";
    case CSV = "text/csv";
    case DOC = "application/msword";
    case DOCX = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    case EOT = "application/vnd.ms-fontobject";
    case EPUB = "application/epub+zip";
    case EXE = "application/x-msdownload";
    case GZ = "application/gzip";
    case GIF = "image/gif";
    case HTML = "text/html";
    case ICO = "image/vnd.microsoft.icon";
    case ICS = "text/calendar";
    case JAR = "application/java-archive";
    case JPEG = "image/jpeg";
    case JS = "text/javascript";
    case JSON = "application/json";
    case JSONLD = "application/ld+json";
    case MIDI = "audio/midi";
    case MP3 = "audio/mpeg";
    case MP4 = "video/mp4";
    case MPEG = "video/mpeg";
    case MPKG = "application/vnd.apple.installer+xml";
    case ODP = "application/vnd.oasis.opendocument.presentation";
    case ODS = "application/vnd.oasis.opendocument.spreadsheet";
    case ODT = "application/vnd.oasis.opendocument.text";
    case OGA = "audio/ogg";
    case OGV = "video/ogg";
    case OGX = "application/ogg";
    case OPUS = "audio/opus";
    case OTF = "font/otf";
    case PNG = "image/png";
    case PDF = "application/pdf";
    case PHP = "application/x-httpd-php";
    case PPT = "application/vnd.ms-powerpoint";
    case PPTX = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
    case RAR = "application/vnd.rar";
    case RTF = "application/rtf";
    case SH = "application/x-sh";
    case SVG = "image/svg+xml";
    case TAR = "application/x-tar";
    case TIFF = "image/tiff";
    case TS = "video/mp2t";
    case TTF = "font/ttf";
    case TXT = "text/plain";
    case VSD = "application/vnd.visio";
    case WAV = "audio/wav";
    case WEBA = "audio/webm";
    case WEBM = "video/webm";
    case WEBP = "image/webp";
    case WOFF = "font/woff";
    case WOFF2 = "font/woff2";
    case XHTML = "application/xhtml+xml";
    case XLS = "application/vnd.ms-excel";
    case XLSX = "application/vnd.openxmlformats-";
    case XML = "application/xml";
    case XUL = "application/vnd.mozilla.xul+xml";
    case ZIP = "application/zip";
    case _3GP = "video/3gpp";
    case _3G2 = "video/3gpp2";
    case _7Z = "application/x-7z-compressed";

    /**
     * @return FileType[] An array with all image types
     */
    public static function image(): array
    {
        return array_filter(
            self::cases(),
            fn (FileType $type) => str_starts_with($type->value, "image/")
        );
    }

    /**
     * @return FileType[] An array with all audio types
     */
    public static function audio(): array
    {
        return array_filter(
            self::cases(),
            fn (FileType $type) => str_starts_with($type->value, "audio/")
        );
    }

    /**
     * @return FileType[] An array with all video types
     */
    public static function video(): array
    {
        return array_filter(
            self::cases(),
            fn (FileType $type) => str_starts_with($type->value, "video/")
        );
    }

    /**
     * @return FileType[] An array with all video types
     */
    public static function text(): array
    {
        return array_filter(
            self::cases(),
            fn (FileType $type) => str_starts_with($type->value, "text/")
        );
    }

    /**
     * Get an enum instance from string extensions. Allowed formats are: `.ext` and `ext`
     * @return FileType The enum instance
     * @throws InvalidExtensionException If extension is not found
     */
    public static function getFromExtension(string $extension): self
    {
        if (empty($extension)) {
            throw new InvalidExtensionException("extension '$extension' does not exists");
        }

        if ($extension[0] !== ".") {
            $extension = ".$extension";
        }

        foreach (self::cases() as $type) {
            $typeExtension = $type->getExtension();
            if (is_array($typeExtension)) {
                if (in_array($extension, $typeExtension)) {
                    return $type;
                }
                continue;
            }

            if ($typeExtension === $extension) {
                return $type;
            }
        }

        throw new InvalidExtensionException("extension '$extension' does not exists");
    }

    /**
     * Try to get an enum instance from string extensions. Allowed formats are: `.ext` and `ext`
     * @return FileType|false The enum instance if found, false otherwise
     */
    public static function tryFromExtension(string $extension): self|false
    {
        try {
            return self::getFromExtension($extension);
        } catch (InvalidExtensionException $exception) {
            return false;
        }
    }

    /**
     * Get type extensions
     * @return string|string[] The extension or array of extensions in the format `.ext`
     */
    public function getExtension(): string|array
    {
        return match ($this) {
            FileType::AAC => ".aac",
            FileType::ABW => ".abw",
            FileType::ARC => ".arc",
            FileType::AVIF => ".avif",
            FileType::AVI => ".avi",
            FileType::AZW => ".azw",
            FileType::BIN => ".bin",
            FileType::BMP => ".bmp",
            FileType::BZ => ".bz",
            FileType::BZ2 => ".bz2",
            FileType::CDA => ".cda",
            FileType::CSH => ".csh",
            FileType::CSS => ".css",
            FileType::CSV => ".csv",
            FileType::DOC => ".doc",
            FileType::DOCX => ".docx",
            FileType::EOT => ".eot",
            FileType::EPUB => ".epub",
            FileType::EXE => ".exe",
            FileType::GZ => ".gz",
            FileType::GIF => ".gif",
            FileType::HTML => ".html",
            FileType::ICO => ".ico",
            FileType::ICS => ".ics",
            FileType::JAR => ".jar",
            FileType::JPEG => [".jpeg", ".jpg", ".jfif", ".jif"],
            FileType::JS => ".js",
            FileType::JSON => ".json",
            FileType::JSONLD => ".jsonld",
            FileType::MIDI => ".midi",
            FileType::MP3 => ".mp3",
            FileType::MP4 => ".mp4",
            FileType::MPEG => ".mpeg",
            FileType::MPKG => ".mpkg",
            FileType::ODP => ".odp",
            FileType::ODS => ".ods",
            FileType::ODT => ".odt",
            FileType::OGA => ".oga",
            FileType::OGV => ".ogv",
            FileType::OGX => ".ogx",
            FileType::OPUS => ".opus",
            FileType::OTF => ".otf",
            FileType::PNG => ".png",
            FileType::PDF => ".pdf",
            FileType::PHP => ".php",
            FileType::PPT => ".ppt",
            FileType::PPTX => ".pptx",
            FileType::RAR => ".rar",
            FileType::RTF => ".rtf",
            FileType::SH => ".sh",
            FileType::SVG => ".svg",
            FileType::TAR => ".tar",
            FileType::TIFF => ".tiff",
            FileType::TS => ".ts",
            FileType::TTF => ".ttf",
            FileType::TXT => ".txt",
            FileType::VSD => ".vsd",
            FileType::WAV => ".wav",
            FileType::WEBA => ".weba",
            FileType::WEBM => ".webm",
            FileType::WEBP => ".webp",
            FileType::WOFF => ".woff",
            FileType::WOFF2 => ".woff2",
            FileType::XHTML => ".xhtml",
            FileType::XLS => ".xls",
            FileType::XLSX => ".xlsx",
            FileType::XML => ".xml",
            FileType::XUL => ".xul",
            FileType::ZIP => ".zip",
            FileType::_3GP => ".3gp",
            FileType::_3G2 => ".3g2",
            FileType::_7Z => ".7z",
        };
    }
}
