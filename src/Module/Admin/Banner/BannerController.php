<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Module\Admin\Banner;

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Banner\Module\Admin\Banner\Form\EditForm;
use Lyrasoft\Banner\Repository\BannerRepository;
use Lyrasoft\Banner\Service\BannerService;
use Lyrasoft\Luna\Entity\Category;
use Unicorn\Controller\CrudController;
use Unicorn\Controller\GridController;
use Unicorn\Upload\FileUploadManager;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Router\Navigator;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\Event\AfterSaveEvent;

/**
 * The BannerController class.
 */
#[Controller()]
class BannerController
{
    public function save(
        AppContext $app,
        CrudController $controller,
        Navigator $nav,
        #[Autowire] BannerRepository $repository,
        FileUploadManager $fileUploadManager,
        BannerService $bannerService,
    ): mixed {
        $form = $app->make(EditForm::class);

        $controller->afterSave(
            function (AfterSaveEvent $event) use ($bannerService, $repository, $fileUploadManager, $app) {
                /** @var Banner $entity */
                $entity   = $event->getEntity();
                $data     = &$event->getData();
                $orm      = $event->getORM();
                $category = $orm->findOne(Category::class, $entity->getCategoryId());

                $config = $bannerService->getTypeConfig($category?->getAlias() ?? '_default');

                if (!($config['desktop']['ajax'] ?? false)) {
                    $uploader = $fileUploadManager->get($config['desktop']['profile'] ?? 'image');

                    $data['image'] = $uploader->handleFileIfUploaded(
                        $app->file('item')['image'] ?? null,
                        'images/banner/' . md5((string) $data['id']) . '/image.{ext}',
                            [
                                'resize' => [
                                    'width' => $config['desktop']['width'] ?? 1080,
                                    'height' => $config['desktop']['width'] ?? 800,
                                    'crop' => $config['desktop']['crop'] ?? false,
                                ]
                            ]
                    )
                        ?->getUri(true) ?? $data['image'];
                }

                if (!($config['mobile']['ajax'] ?? false)) {
                    $uploader = $fileUploadManager->get($config['mobile']['profile'] ?? 'image');

                    $data['mobile_image'] = $uploader->handleFileIfUploaded(
                        $app->file('item')['mobile_image'] ?? null,
                        'images/banner/' . md5((string) $data['id']) . '/image-mobile.{ext}',
                            [
                                'resize' => [
                                    'width' => $config['mobile']['width'] ?? 1080,
                                    'height' => $config['mobile']['width'] ?? 800,
                                    'crop' => $config['mobile']['crop'] ?? false,
                                ]
                            ]
                    )
                        ?->getUri(true) ?? $data['mobile_image'];
                }

                $uploader = $fileUploadManager->get('image');

                $data['video'] = $uploader->handleFileIfUploaded(
                    $app->file('item')['video'] ?? null,
                    'images/banner/' . md5((string) $data['id']) . '/video.{ext}',
                )
                    ?->getUri(true) ?? $data['video'];

                $data['mobile_video'] = $uploader->handleFileIfUploaded(
                    $app->file('item')['mobile_video'] ?? null,
                    'images/banner/' . md5((string) $data['id']) . '/video-mobile.{ext}',
                )
                    ?->getUri(true) ?? $data['mobile_video'];

                $repository->save($data);
            }
        );

        $uri = $app->call([$controller, 'save'], compact('repository', 'form'));

        switch ($app->input('task')) {
            case 'save2close':
                return $nav->to('banner_list');

            case 'save2new':
                return $nav->to('banner_edit')->var('new', 1);

            case 'save2copy':
                $controller->rememberForClone($app, $repository);
                return $nav->self($nav::WITHOUT_VARS)->var('new', 1);

            default:
                return $uri;
        }
    }

    public function delete(
        AppContext $app,
        #[Autowire] BannerRepository $repository,
        CrudController $controller
    ): mixed {
        return $app->call([$controller, 'delete'], compact('repository'));
    }

    public function filter(
        AppContext $app,
        #[Autowire] BannerRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call([$controller, 'filter'], compact('repository'));
    }

    public function batch(
        AppContext $app,
        #[Autowire] BannerRepository $repository,
        GridController $controller
    ): mixed {
        $task = $app->input('task');
        $data = match ($task) {
            'publish' => ['state' => 1],
            'unpublish' => ['state' => 0],
            default => null
        };

        return $app->call([$controller, 'batch'], compact('repository', 'data'));
    }

    public function copy(
        AppContext $app,
        #[Autowire] BannerRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call([$controller, 'copy'], compact('repository'));
    }
}
