<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Enum;

use MyCLabs\Enum\Enum;
use Windwalker\Utilities\Contract\LanguageInterface;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;

/**
 * The BannerVideoType enum class.
 */
enum BannerVideoType: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    case EMBED = 'embed';
    case FILE = 'file';

    public static function preprocessValue(mixed $value): mixed
    {
        return $value ?: static::FILE;
    }

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('banner.video.type.' . $this->getKey());
    }
}
