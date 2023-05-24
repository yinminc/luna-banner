<?php

declare(strict_types=1);

namespace App\View;

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

use App\Entity\Banner;
use Lyrasoft\Banner\Repository\BannerRepository;
use Lyrasoft\Banner\Script\BannerScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Edge\Component\ComponentAttributes;

use function Windwalker\uid;

/**
 * @var ComponentAttributes $attributes
 * @var Banner[] $banners
 */

$id ??= 'c-swiper-' . uid();

$options ??= [];
$navigation ??= true;
$pagination ??= false;
$scrollbar ??= false;
$height ??= '';
$linkTarget ??= null;
$type ??= null;
$ratio ??= null;
$showText ??= false;

if ($navigation) {
    $options['navigation']['prevEl'] = "#$id .swiper-button-prev";
    $options['navigation']['nextEl'] = "#$id .swiper-button-next";
}

if ($pagination) {
    $options['pagination']['el'] = "#$id .swiper-pagination";
}

if ($scrollbar) {
    $options['scrollbar']['el'] = "#$id .swiper-scrollbar";
}

if (!isset($banners)) {
    $categoryAlias ??= null;
    $categoryId ??= null;

    $repo = $app->service(BannerRepository::class);

    if ($type) {
        $banners = $repo->getBannersByType($type)->all();
    } elseif ($categoryAlias) {
        $banners = $repo->getBannersByCategoryAlias($categoryAlias)->all();
    } elseif ($categoryId) {
        $banners = $repo->getBannersByCategoryId($categoryAlias)->all();
    } else {
        throw new \RuntimeException('No banners conditions');
    }
}

$attributes = $attributes->class('swiper')
    ->exceptProps(
        [
            'id',
            'banners',
            'categoryAlias',
            'categoryId',
            'type',
            'linkTarget',
            'height',
            'ratio',
            'options'
        ]
    );

$attributes['id'] = $id;
$attributes['style'] ??= '';

if ($height) {
    $attributes['style'] .= "height: $height";
}

$attributes = $attributes->class('l-swiper-banners');

$app->service(BannerScript::class)->swiper('#' . $id, $options);
?>

<div {!! $attributes !!}>
    <div class="swiper-wrapper">
        @foreach ($banners as $i => $banner)
            <div class="swiper-slide">
                @if ($item ?? null)
                    {!! $item(banner: $banner, i: $i) !!}
                @else
                    <x-banner-item :banner="$banner"
                        :type="$type"
                        :height="$height"
                        :link-target="$linkTarget"
                        :show-text="$showText"
                    ></x-banner-item>
                @endif
            </div>
        @endforeach
    </div>

    @if ($pagination)
        <div class="swiper-pagination"></div>
    @endif

    @if ($navigation)
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    @endif

    @if ($scrollbar)
        <div class="swiper-scrollbar"></div>
    @endif
</div>
