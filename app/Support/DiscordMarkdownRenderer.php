<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class DiscordMarkdownRenderer
{
    public function render(string $markdown): string
    {
        return $this->renderMarkdown($markdown);
    }

    private function renderMarkdown(string $markdown, int $depth = 0): string
    {
        $tokens = [];
        $blockTokens = [];
        $tokenIndex = 0;
        $tokenize = static function (string $html) use (&$tokens, &$tokenIndex): string {
            $token = 'LSSDHTMLTOKEN' . $tokenIndex++ . 'END';
            $tokens[$token] = $html;

            return $token;
        };

        $markdown = preg_replace_callback(
            '/```([a-zA-Z0-9_+-]*)[ \t]*\r?\n([\s\S]*?)```|`([^`\r\n]+)`/u',
            static function (array $matches) use ($tokenize, &$blockTokens): string {
                if (($matches[2] ?? '') !== '') {
                    $language = preg_replace('/[^a-zA-Z0-9_+-]/', '', $matches[1]);
                    $class = $language === '' ? '' : ' class="language-' . $language . '"';
                    $code = e(rtrim($matches[2], "\r\n"));
                    $token = $tokenize('<pre><code' . $class . '>' . $code . '</code></pre>');
                    $blockTokens[] = $token;

                    return $token;
                }

                return $tokenize('<code>' . e($matches[3] ?? '') . '</code>');
            },
            $markdown
        ) ?? $markdown;

        if ($depth < 5) {
            $markdown = preg_replace_callback(
                '/(?<!_)__(.+?)__(?!_)/s',
                fn (array $matches): string => $tokenize(
                    '<u>' . trim(Str::inlineMarkdown($matches[1], [
                        'html_input' => 'escape',
                        'allow_unsafe_links' => false,
                    ])) . '</u>'
                ),
                $markdown
            ) ?? $markdown;

            $markdown = preg_replace_callback(
                '/\|\|(.+?)\|\|/s',
                fn (array $matches): string => $tokenize(
                    '<span class="discord-spoiler" role="button" tabindex="0" aria-label="Nội dung spoiler, nhấn để hiển thị">'
                    . trim(Str::inlineMarkdown($matches[1], [
                        'html_input' => 'escape',
                        'allow_unsafe_links' => false,
                    ]))
                    . '</span>'
                ),
                $markdown
            ) ?? $markdown;
        }

        $markdown = preg_replace_callback(
            '/<(a?):([a-zA-Z0-9_]{2,32}):(\d{17,22})>/',
            static function (array $matches) use ($tokenize): string {
                $extension = $matches[1] === 'a' ? 'gif' : 'webp';
                $name = e($matches[2]);
                $id = $matches[3];

                return $tokenize(
                    '<img class="discord-emoji" src="https://cdn.discordapp.com/emojis/'
                    . $id . '.' . $extension . '?size=48" alt=":' . $name . ':" loading="lazy">'
                );
            },
            $markdown
        ) ?? $markdown;

        $markdown = preg_replace_callback(
            '/<t:(-?\d{1,12})(?::([tTdDfFR]))?>/',
            static function (array $matches) use ($tokenize): string {
                try {
                    $date = CarbonImmutable::createFromTimestampUTC((int) $matches[1])
                        ->setTimezone(config('app.timezone', 'UTC'));
                } catch (\Throwable) {
                    return e($matches[0]);
                }

                $format = match ($matches[2] ?? 'f') {
                    't' => 'H:i',
                    'T' => 'H:i:s',
                    'd' => 'd/m/Y',
                    'D' => 'd F Y',
                    'F' => 'l, d F Y H:i',
                    'R' => null,
                    default => 'd F Y H:i',
                };
                $label = $format === null ? $date->diffForHumans() : $date->translatedFormat($format);

                return $tokenize(
                    '<time class="discord-timestamp" datetime="' . $date->toIso8601String()
                    . '" title="' . e($date->translatedFormat('d/m/Y H:i:s'))
                    . '">' . e($label) . '</time>'
                );
            },
            $markdown
        ) ?? $markdown;

        $html = Str::markdown($markdown, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'renderer' => ['soft_break' => "<br>\n"],
        ]);

        if ($blockTokens !== []) {
            $html = preg_replace_callback(
                '~<p>(LSSDHTMLTOKEN\d+END)</p>~',
                static fn (array $matches): string => in_array($matches[1], $blockTokens, true)
                    ? $matches[1]
                    : $matches[0],
                $html
            ) ?? $html;
        }

        return strtr($html, $tokens);
    }
}
