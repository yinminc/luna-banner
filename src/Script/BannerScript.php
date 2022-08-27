<?php

/**
 * Part of eva project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Script;

use Windwalker\Core\Asset\AbstractScript;

/**
 * The BannerScript class.
 */
class BannerScript extends AbstractScript
{
    public function youtubeBackground(): void
    {
        if ($this->available()) {
            $this->js('vendor/youtube-background/jquery.youtube-background.min.js');
            $this->internalJS("new VideoBackgrounds('[data-vbg]')");
            $this->internalCSS("[data-vbg] iframe { transition: opacity .3s ease-in-out; }");
        }
    }

    public function swiper(?string $selector, array $options = []): void
    {
        $defaultOptions = [
            'simulateTouch' => true,
            'allowTouchMove' => true,
            'autoHeight' => true
        ];

        if ($this->available()) {
            $this->js('vendor/swiper/swiper-bundle.min.js');
            $this->css('vendor/swiper/swiper-bundle.min.css');
        }

        if ($this->available($selector)) {
            $var = $options['variable_name'] ?? '';

            if ($var) {
                $var = "var $var = ";
            }

            $optionString = static::getJSObject($defaultOptions, $options);
            $this->internalJS(
                $var . "new Swiper('$selector', $optionString);"
            );
        }
    }
}
