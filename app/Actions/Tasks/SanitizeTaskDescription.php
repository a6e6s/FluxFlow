<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

class SanitizeTaskDescription
{
    public function handle(string $description): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousErrorMode = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div>'.$description.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorMode);

        $root = $document->getElementsByTagName('div')->item(0);

        if (! $root) {
            return '';
        }

        foreach (iterator_to_array($root->childNodes) as $child) {
            $this->sanitizeNode($child);
        }

        $sanitized = '';

        foreach ($root->childNodes as $child) {
            $sanitized .= $document->saveHTML($child);
        }

        return $sanitized;
    }

    private function sanitizeNode(\DOMNode $node): void
    {
        if (! $node instanceof \DOMElement) {
            return;
        }

        $allowedTags = ['a', 'b', 'blockquote', 'br', 'code', 'div', 'em', 'h1', 'h2', 'h3', 'h4', 'hr', 'i', 'li', 'ol', 'p', 'pre', 's', 'span', 'strike', 'strong', 'u', 'ul'];

        foreach (iterator_to_array($node->childNodes) as $child) {
            $this->sanitizeNode($child);
        }

        if (! in_array(strtolower($node->tagName), $allowedTags, true)) {
            $parent = $node->parentNode;

            if ($parent) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }

                $parent->removeChild($node);
            }

            return;
        }

        $attributeNames = [];

        foreach ($node->attributes as $attribute) {
            $attributeNames[] = $attribute->name;
        }

        foreach ($attributeNames as $attributeName) {
            $isSafeLink = strtolower($node->tagName) === 'a' && strtolower($attributeName) === 'href';
            $isQuillListType = strtolower($node->tagName) === 'li' && strtolower($attributeName) === 'data-list';

            if (! $isSafeLink && ! $isQuillListType) {
                $node->removeAttribute($attributeName);
            }
        }

        if ($node->hasAttribute('data-list') && ! in_array($node->getAttribute('data-list'), ['ordered', 'bullet', 'checked', 'unchecked'], true)) {
            $node->removeAttribute('data-list');
        }

        if ($node->hasAttribute('href')) {
            $href = trim($node->getAttribute('href'));
            $scheme = parse_url($href, PHP_URL_SCHEME);

            if (preg_match('/[\x00-\x20]/', $href) || ($scheme && ! in_array(strtolower($scheme), ['http', 'https', 'mailto'], true))) {
                $node->removeAttribute('href');
            }
        }
    }
}
