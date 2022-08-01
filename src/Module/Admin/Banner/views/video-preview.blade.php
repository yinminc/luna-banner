<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

declare(strict_types=1);

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var \Lyrasoft\Banner\Entity\Banner                 $item
 * @var \Windwalker\Form\Field\UrlField                $field
 * @var \Windwalker\Edge\Component\ComponentAttributes $attributes
 */

?>

<div {!! $attributes !!}>
    @if ($field->getValue())
        @if ($item->getVideoType()->equals(\Lyrasoft\Banner\Enum\BannerVideoType::FILE()))
            <div class="ratio ratio-16x9">
                <video src="{{ $field->getValue() }}" style="width: 100%; height: 100%;" class=""
                    controls
                >

                </video>
            </div>
        @else
            <?php
            $url = new \Windwalker\Uri\Uri($field->getValue());
            $id = null;

            if (str_contains($url->getHost(), 'youtube.com')) {
                $id = $url->getVar('v');
            } elseif (str_contains($url->getHost(), 'youtu.be')) {
                $id = $url->getPath();
            }
            ?>
                <iframe width="100%" height="400" src="https://www.youtube.com/embed/{{ $id }}"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        @endif
    @endif
</div>
