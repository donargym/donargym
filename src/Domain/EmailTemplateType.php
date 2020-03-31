<?php

declare(strict_types=1);

namespace App\Domain;

final class EmailTemplateType
{
    const TEXT = 'text';
    const HTML = 'html';

    private string $templateType;

    public static function TEXT(): self
    {
        return self::fromString(self::TEXT);
    }

    public static function HTML(): self
    {
        return self::fromString(self::HTML);
    }

    public function toString(): string
    {
        return $this->templateType;
    }

    private static function fromString(string $templateType): self
    {
        $self               = new self();
        $self->templateType = $templateType;

        return $self;
    }

    private function __construct()
    {
    }
}
