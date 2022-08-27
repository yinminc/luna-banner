<?php

/**
 * Part of eva project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\Banner;

use Lyrasoft\Banner\Repository\BannerRepository;
use Lyrasoft\Banner\Script\BannerScript;
use Lyrasoft\Banner\Service\BannerService;
use Windwalker\Core\Package\AbstractPackage;
use Windwalker\Core\Package\PackageInstaller;
use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;

/**
 * The BannerPackage class.
 */
class BannerPackage extends AbstractPackage implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->prepareSharedObject(BannerService::class);
        $container->prepareSharedObject(BannerScript::class);
        $container->prepareSharedObject(BannerRepository::class);

        $container->mergeParameters(
            'renderer.paths',
            [
                static::path('views'),
            ],
            Container::MERGE_OVERRIDE
        );

        $container->mergeParameters(
            'renderer.edge.components',
            [
                'swiper-banners' => 'banner.swiper-banners',
                'banner-item' => 'banner.banner-item',
            ]
        );
    }

    public function install(PackageInstaller $installer): void
    {
        $installer->installConfig(static::path('etc/*.php'), 'config');
        $installer->installLanguages(static::path('resources/languages/**/*.ini'), 'lang');
        $installer->installMigrations(static::path('resources/migrations/**/*'), 'migrations');
        $installer->installSeeders(static::path('resources/seeders/**/*'), 'seeders');
        $installer->installRoutes(static::path('routes/**/*.php'), 'routes');

        // Modules
        $installer->installModules(
            [
                static::path("src/Module/Admin/Banner/**/*") => "@source/Module/Admin/Banner",
            ],
            ['Lyrasoft\\Banner\\Module\\Admin' => 'App\\Module\\Admin'],
            ['modules', 'banner_admin'],
        );

        $installer->installModules(
            [
                static::path("src/Entity/Banner.php") => '@source/Entity',
                static::path("src/Repository/BannerRepository.php") => '@source/Repository',
            ],
            [
                'Lyrasoft\\Banner\\Entity' => 'App\\Entity',
                'Lyrasoft\\Banner\\Repository' => 'App\\Repository',
            ],
            ['modules', 'banner_model']
        );
    }
}
