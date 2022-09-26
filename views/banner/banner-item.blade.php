<?php

declare(strict_types=1);

// namespace App\View;

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

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Banner\Script\BannerScript;
use Lyrasoft\Banner\Service\BannerService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var Banner $banner
 * @var ?string $type
 */

$type = $type ?? $banner?->category?->alias ?? '_default';

$bannerService = $app->service(BannerService::class);

if ($banner->getVideo() || $banner->getMobileVideo()) {
    $app->service(BannerScript::class)->youtubeBackground();
}

$height ??= null;
$linkTarget ??= null;
$desktopRatio = $bannerService->getImageRatio($type);
$mobileRatio = $bannerService->getImageRatio($type, true);
?>
<?php
if ($height) {
    $style = "height: $height;";
} else {
    $style = '--bs-aspect-ratio: ' . (100 / ($ratio ?? $desktopRatio)) . '%;';
}

$cover = $banner->getImage();

if ($cover) {
    $style .= "background-image: url({$cover}); background-position: cover;";
}
?>
<a
    @attr('href', $banner->getLink() ?: null)
    @attr('target', $linkTarget)
>
{{-- Desktop --}}
@if ($banner->getVideo())
    <div class="d-none d-md-block ratio"
        style="{{ $style }}"
    >
        <div data-vbg="{{ $banner->getVideo() }}"
            data-vbg-mobile
            data-vbg-poster="{{ $cover }}"
        ></div>
    </div>
@else
    <div class="ratio d-none d-md-block" style="{{ $style }}">
        <img class="img-fluid"
            style="width: 100%; object-fit: cover"
            src="{{ $cover }}"
            alt="{{ $banner->getTitle() }}"
        >
    </div>
@endif

<?php
if ($height) {
    $style = "height: $height;";
} else {
    $style = '--bs-aspect-ratio: ' . (100 / ($ratio ?? $mobileRatio)) . '%;';
}

$cover = $banner->getMobileImage() ?: $banner->getImage();

if ($cover) {
    $style .= "background-image: url({$cover}); background-position: cover;";
}
?>

@if ($banner->getMobileVideo())
    <a class="d-block d-md-none"
        style="{{ $style }}"
    >
        <div data-vbg="{{ $banner->getMobileVideo() }}"
            data-vbg-mobile
            data-vbg-poster="{{ $cover }}"
        ></div>
    </a>
@elseif (!$banner->getVideo())
    <div class="d-block d-md-none ratio" style="{{ $style }}">
        <img class="img-fluid"
            style="width: 100%; object-fit: cover"
            src="{{ $cover }}"
            alt="{{ $banner->getTitle() }}"
        >
    </div>
@endif
</a>
