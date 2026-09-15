<?php

declare(strict_types=1);

namespace App\Campus\Import;

use DOMDocument;
use DOMNode;
use DOMXPath;
use RuntimeException;

class Html
{
    public readonly DOMXPath $xpath;

    public function __construct(string $html)
    {
        $previous = libxml_use_internal_errors(true);
        try {
            $document    = new DOMDocument;
            if (! $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
                throw new RuntimeException('Invalid HTML document');
            }
            $this->xpath = new DOMXPath($document);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    /** @return list<DOMNode> */
    public function nodes(string $expression, ?DOMNode $context = null): array
    {
        $nodes = $this->xpath->query($expression, $context);

        return $nodes === false ? [] : array_values(array_filter(iterator_to_array($nodes, false), fn ($node) => $node instanceof DOMNode));
    }

    public static function text(?DOMNode $node): string
    {
        if ($node === null) {
            return '';
        }
        $text = $node->textContent;

        return trim((string) preg_replace('/[\s\x{00a0}\x{3000}]+/u', ' ', $text));
    }

    public static function attr(DOMNode $node, string $name): string
    {
        return $node->attributes?->getNamedItem($name)->nodeValue ?? '';
    }

    public function first(string $expression, ?DOMNode $context = null): ?DOMNode
    {
        return $this->nodes($expression, $context)[0] ?? null;
    }

    public static function lines(DOMNode $node): string
    {
        $copy = $node->cloneNode(true);
        $walk = function (DOMNode $n) use (&$walk): string {
            if (strtolower($n->nodeName) === 'br') {
                return "\n";
            }
            if (! $n->hasChildNodes()) {
                return $n->textContent;
            }
            $result = '';
            foreach ($n->childNodes as $child) {
                $result .= $walk($child);
            }

            return $result;
        };

        return trim($walk($copy));
    }
}
