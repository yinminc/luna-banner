# LYRASOFT Banner Package

![screenshot 2022-08-01 下午6 59 04](https://user-images.githubusercontent.com/1639206/182134010-ec900d4c-b2fd-495f-bac0-d9034ce235c4.jpg)

## Installation

Install from composer

```shell
composer require lyrasoft/banner
```

Then copy files to project

```shell
php windwalker pkg:install lyrasoft/banner -t routes -t lang -t migrations -t seeders
```

Seeders

- Add `contact-seeder.php` to `resources/seeders/main.php`
- Add `banner` type to `category-seeder.php`

Languages

If you don't want to copy language files, remove `-t lang` from install command.

Then add this line to admin & front middleware:

```php
$this->lang->loadAllFromVendor('lyrasoft/banner', 'ini');
```

## Use Type or Category

You have 2 choice to structure banners, use `type` or `category`

Type:

![screenshot 2022-08-27 下午4 51 25](https://user-images.githubusercontent.com/1639206/187023024-762f6b4c-a6db-496e-9ac7-59f337448416.jpg)

Category:

![screenshot 2022-08-27 下午4 52 04](https://user-images.githubusercontent.com/1639206/187023025-33163b4b-140c-4290-a825-20dc0f0f2a06.jpg)

The default will use `category` mode. If you want to use `type` mode,
you must create a `BannerType` enum, for example:

```php
class BannerType extends Enum implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    #[Title('Home Banner')]
    public const HOME = 'home';

    #[Title('Works')]
    public const WORKS = 'works';

    // ...
}
```

Then register enum class to config file:

```php
return [
    'banner' => [
        // ...
        
        'type_enum' => \App\Enum\BannerType::class,

        // ...
```

The package will auto switch to `type` mode.

## Register Admin Menu

Edit `resources/menu/admin/sidemenu.menu.php`

Ues Type mode:

```php
// Banner
$menu->link('橫幅管理')
    ->to($nav->to('banner_list'))
    ->icon('fal fa-gallery-thumbnails');
```

Use Category mode:

```php
// Category
$menu->link('橫幅分類')
    ->to($nav->to('category_list', ['type' => 'banner']))
    ->icon('fal fa-sitemap');

// Banner
$menu->link('橫幅管理')
    ->to($nav->to('banner_list'))
    ->icon('fal fa-images');
```

## Add Widget

Add this to `packages/widget.php`

```php
return [
    'widget' => [
        'types' => [
            // ...
            'banner' => \Lyrasoft\Banner\Widget\Banner\BannerWidget::class
        ],
        // ...
    ]
];
```

## Install `Swiper` and `youtube-background`

If you needs use video & Youtbue, you must install `youtube-background`

- Swiper:
  - Getting Started: https://swiperjs.com/get-started
  - Demo: https://swiperjs.com/demos
- Youtbue Background
  - Github: https://github.com/stamat/youtube-background

Add package to fusion.

```js
  installVendors(
    [
      // ...
      'swiper',
      'youtube-background',
    ],
    [
      // ...
    ]
  );
```

Then install.

```shell
yarn add swiper youtube-background
```

## Frontend Usage

Use `BannerRepository` to get banners

```php
$repo = $app->service(BannerRepository::class);

/** @var Banner[] $banners */
$banners = $repo->getBannersByType('home')->all();
$banners = $repo->getBannersByCategoryAlias('category-alias')->all();
$banners = $repo->getBannersByCategoryId(5)->all();
```

Then use component in Edge:

```html
<x-swiper-banners :banners="$banners"></x-swiper-banners>
```

![2022-08-01 19 05 58](https://user-images.githubusercontent.com/1639206/182135063-62b6fc61-45f2-4f7a-ac80-5f6306d9a829.gif)

You can add some params:

```html
<x-swiper-banners :banners="$banners"
    link-target="_blank"
    :pagination="true"
    height="400px"
></x-swiper-banners>
```

### Parameters

| Param Name     | Type              | Default  | Description                                                                                      |
|----------------|-------------------|----------|--------------------------------------------------------------------------------------------------|
| banners        | `Banner[]`        | null     | The banner items, must be a iterable with `Banner` entity.                                       |
| category-alias | `?string`         | null     | If not provides banner items, component will find banners by this condition.                     |
| category-id    | `string` or `int` | null     | If not provides banner items, component will find banners by this condition.                     |                                                                              |
| type           | `string`          | _default | If not provides banner items, use this type name to find banners & size & ratio settings.        |
| link-target    | `string`          | null     | The link target, can be `_blank`                                                                 |
| height         | `string`          | null     | Force banner height, ignore ratio settings.                                                      |
| ratio          | `float`           | null     | The widrth / height ratio. for example: 16:9 is `1.7778`. Leave empty yto let component calc it. |
| options        | `array`            | []       | The options for `Swiper`                                                                         |

### Examples

Load by type

```html
<x-swiper-banners :type="$type"
    link-target="_blank"
    :pagination="true"
    height="400px"
></x-swiper-banners>
```

Load by category alias

```html
<x-swiper-banners :category-alias="$categoryAlias"
    link-target="_blank"
    :pagination="true"
    height="400px"
></x-swiper-banners>
```

Load by category ID

```html
<x-swiper-banners :category-id="$category->getId()"
    link-target="_blank"
    :pagination="true"
    height="400px"
></x-swiper-banners>
```

## Custom Banner Item HTML

Use `item` slot with `@scope()`, you will get `Banner` entity and index `$i`.

Then kust build you own HTML.

```html
<x-swiper-banners :banners="$banners"
>
    <x-slot name="item">
        @scope($banner, $i)
        
        <div class="c-banner-item"
            style="background-image: url({{ $banner->getImage()) }})">
            <h2>
                {{ $banner->getTitle() }}
            </h2>
        </div>
    </x-slot>
</x-swiper-banners>
```

## The Size Settings.

Open `etc/packages/banner.php`, you will see:

```php
return [
    'banner' => [
        // ...
        
        'types' => [
            '_default' => [
                'desktop' => [
                    'width' => 1920,
                    'height' => 800,
                    'crop' => true,
                    'ajax' => false,
                    'profile' => 'image',
                ],
                'mobile' => [
                    'width' => 720,
                    'height' => 720,
                    'crop' => true,
                    'ajax' => false,
                    'profile' => 'image',
                ]
            ]
        ]
    ]
];

```

The `_default` type has 2 sizes settings, `desktop` and `mobile`, this means admin upload images will use this size:

![screenshot 2022-08-01 下午7 23 22](https://user-images.githubusercontent.com/1639206/182137666-0eef8a27-0f97-4ea1-b4e6-977e03bfe6a9.jpg)

You can change all uploading settings here.

### Custom Size for Type or Category Alias

If you have a category with alias (`promote`), you can add a `promote` size settings with different size.

```php
return [
    'banner' => [
        // ...
        'types' => [
            '_default' => [
                // ...
            ],
            'promote' => [
                'desktop' => [
                    'width' => 800,
                    'height' => 400,
                    'crop' => true,
                    'ajax' => false,
                    'profile' => 'image',
                ],
                'mobile' => [
                    'width' => 200,
                    'height' => 200,
                    'crop' => true,
                    'ajax' => false,
                    'profile' => 'image',
                ]
            ],
        ]
    ]
];
```

Make sure your category alias is same:

![screenshot 2022-08-01 下午7 28 59](https://user-images.githubusercontent.com/1639206/182138618-4f09226f-a020-43df-b4c9-006190360872.jpg)

Then the banners in this category will use the new size:

![screenshot 2022-08-01 下午7 27 55](https://user-images.githubusercontent.com/1639206/182138449-2b157be9-1943-47ce-a679-62137f5fe1ac.jpg)

## Create Default Categories/Type

If you use Category mode, you may want to create some default categories in migration:

```php
$catMapper = $orm->mapper(Category::class);
$catMapper->createOne(
    [
        'title' => '首頁作品',
        'alias' => 'works',
        'parent_id' => 1
    ]
);
```

If you use Type mode, just change the `BannerType` enum cases:

## BannerScript

Directly use Swiper or Youtube Background

```php
use Lyrasoft\Banner\Script\BannerScript;

$app->service(BannerScript::class)->swiper('#swiper', $options);

$app->service(BannerScript::class)->youtubeBackground();
```

## Widget

If you ever added `BannerWidget::class` to `widget.php`, you'll see this widget in admin:

![screenshot 2022-08-01 下午7 31 59](https://user-images.githubusercontent.com/1639206/182139089-363cd0a4-a4d7-476f-974b-4948518b99bc.jpg)

After you added this widget and save. Use this code to render position, for example (`demo` position):

```html
<?php
$widgetService = $app->service(WidgetService::class);
?>
@if ($widgetService->hasWidgets('demo'))
    <div class="l-demo-position">
        @foreach ($widgetService->loadWidgets('demo') as $widget)
        <div class="l-demo-position__widget mb-3">
            <x-widget :widget="$widget"></x-widget>
        </div>
        @endforeach
    </div>
@endif
```
