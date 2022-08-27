<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Enum;

use MyCLabs\Enum\Enum;
use Windwalker\Utilities\Contract\LanguageInterface;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;

/**
 * The BannerVideoType enum class.
 *
 * @method static $this EMBED()
 * @method static $this FILE()
 */
class BannerVideoType extends Enum implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    public const EMBED = 'embed';
    public const FILE = 'file';

    /**
     * Creates a new value of some type
     *
     * @psalm-pure
     *
     * @param  mixed  $value
     *
     * @psalm-param T $value
     * @throws \UnexpectedValueException if incompatible type is given.
     */
    public function __construct(mixed $value)
    {
        parent::__construct($value ?: static::FILE);
    }

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('banner.video.type.' . $this->getKey());
    }
}
