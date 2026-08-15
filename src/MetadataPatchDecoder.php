<?php

declare(strict_types=1);

namespace PhpJsonDecodeNullErrorLab;

final class MetadataPatchDecoder
{
    /**
     * @return array{operation: 'replace'|'clear', metadata: array<string, mixed>|null}
     */
    public function decode(string $json): array
    {
        $metadata = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if ($metadata === null) {
            return ['operation' => 'clear', 'metadata' => null];
        }

        if (!is_array($metadata)) {
            throw new \DomainException('metadata must be a JSON object or null');
        }

        return ['operation' => 'replace', 'metadata' => $metadata];
    }
}
