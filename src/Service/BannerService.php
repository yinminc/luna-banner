<?php

/**
 * Part of eva project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Service;

use Windwalker\Core\Router\SystemUri;
use Windwalker\Core\Runtime\Config;
use Windwalker\Filesystem\Path;
use Windwalker\Uri\Uri;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;

/**
 * The BannerService class.
 */
class BannerService
{
    public function __construct(
        protected Config $config,
        protected SystemUri $uri
    ) {
    }

    /**
     * @return  class-string<EnumTranslatableInterface>|null
     */
    public function getTypeEnum(): ?string
    {
        return $this->config->getDeep('banner.type_enum');
    }

    public function getTypeConfig(?string $type): array
    {
        $type ??= '_default';

        return $this->config->getDeep('banner.types.' . $type)
            ?? $this->config->getDeep('banner.types._default')
            ?? [];
    }

    public function getImageRatio(string $type, bool $mobile = false): float
    {
        $config = $this->getTypeConfig($type);

        if ($mobile) {
            $width = $config['mobile']['width'];
            $height = $config['mobile']['height'];
        } else {
            $width = $config['desktop']['width'];
            $height = $config['desktop']['height'];
        }

        return round($width / $height, 6);
    }

    public function handleVideoUrl(string $url)
    {
        $u = $this->uri->addUriBase($url, $this->uri->root);
        $u = new Uri($u);
        $ext = Path::getExtension($u->getPath());
        return (string) $u->withVar('format', '.' . $ext);
    }
}
