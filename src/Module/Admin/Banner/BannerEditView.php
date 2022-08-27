<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Module\Admin\Banner;

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Banner\Module\Admin\Banner\Form\EditForm;
use Lyrasoft\Banner\Repository\BannerRepository;
use Lyrasoft\Banner\Service\BannerService;
use Lyrasoft\Luna\Entity\Category;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Form\FormFactory;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

/**
 * The BannerEditView class.
 */
#[ViewModel(
    layout: 'banner-edit',
    js: 'banner-edit.js'
)]
class BannerEditView implements ViewModelInterface
{
    use TranslatorTrait;

    public function __construct(
        protected ORM $orm,
        protected FormFactory $formFactory,
        protected Navigator $nav,
        protected BannerService $bannerService,
        #[Autowire] protected BannerRepository $repository
    ) {
    }

    /**
     * Prepare
     *
     * @param  AppContext  $app
     * @param  View        $view
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): mixed
    {
        $id = $app->input('id');

        /** @var Banner $item */
        $item = $this->repository->getItem($id);

        $category = $this->orm->findOne(Category::class, $item?->getCategoryId());

        if ($this->bannerService->getTypeEnum()) {
            $type = $item->getType();
        } else {
            $type = $category?->getAlias();
        }

        $form = $this->formFactory
            ->create(EditForm::class, type: $type)
            ->setNamespace('item')
            ->fill(
                $this->repository->getState()->getAndForget('edit.data')
                    ?: $this->orm->extractEntity($item)
            );

        $this->prepareMetadata($app, $view);

        return compact('form', 'id', 'item', 'category');
    }

    /**
     * Prepare Metadata and HTML Frame.
     *
     * @param  AppContext  $app
     * @param  View        $view
     *
     * @return  void
     */
    protected function prepareMetadata(AppContext $app, View $view): void
    {
        $view->getHtmlFrame()
            ->setTitle(
                $this->trans('unicorn.title.edit', title: $this->trans('luna.banner.title'))
            );
    }
}
