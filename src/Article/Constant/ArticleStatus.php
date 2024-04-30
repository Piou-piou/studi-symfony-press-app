<?php

namespace App\Article\Constant;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ArticleStatus: string implements TranslatableInterface
{
    case DRAFT = 'DRAFT';
    case PUBLISHED = 'PUBLISHED';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::DRAFT  => $translator->trans('article.draft.label', locale: $locale),
            self::PUBLISHED => $translator->trans('article.published.label', locale: $locale),
        };
    }
}
