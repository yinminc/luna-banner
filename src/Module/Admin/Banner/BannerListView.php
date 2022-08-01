<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Module\Admin\Banner;

use Lyrasoft\Banner\Module\Admin\Banner\Form\GridForm;
use Lyrasoft\Banner\Repository\BannerRepository;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Form\FormFactory;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

/**
 * The BannerListView class.
 */
#[ViewModel(
    layout: [
        'default' => 'banner-list',
        'modal' => 'banner-modal',
    ],
    js: 'banner-list.js'
)]
class BannerListView implements ViewModelInterface
{
    use TranslatorTrait;

    public function __construct(
        protected ORM $orm,
        #[Autowire]
        protected BannerRepository $repository,
        protected FormFactory $formFactory
    ) {
    }

    /**
     * Prepare view data.
     *
     * @param  AppContext  $app   The request app context.
     * @param  View        $view  The view object.
     *
     * @return  array
     */
    public function prepare(AppContext $app, View $view): array
    {
        $state = $this->repository->getState();

        // Prepare Items
        $page     = $state->rememberFromRequest('page');
        $limit    = $state->rememberFromRequest('limit');
        $filter   = (array) $state->rememberFromRequest('filter');
        $search   = (array) $state->rememberFromRequest('search');
        $ordering = $state->rememberFromRequest('list_ordering') ?? $this->getDefaultOrdering();

        $items = $this->repository->getListSelector()
            ->setFilters($filter)
            ->searchTextFor(
                $search['*'] ?? '',
                $this->getSearchFields()
            )
            ->ordering($ordering)
            ->page($page)
            ->limit($limit);

        $pagination = $items->getPagination();

        // Prepare Form
        $form = $this->formFactory->create(GridForm::class);
        $form->fill(compact('search', 'filter'));

        $showFilters = $this->showFilterBar($filter);

        $this->prepareMetadata($app, $view);

        return compact('items', 'pagination', 'form', 'showFilters', 'ordering');
    }

    public function prepareItem(Collection $item): object
    {
        return $this->repository->getEntityMapper()->toEntity($item);
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'banner.category_id, banner.ordering ASC';
    }

    /**
     * Get search fields.
     *
     * @return  string[]
     */
    public function getSearchFields(): array
    {
        return [
            'banner.id',
            'banner.title',
            'banner.subtitle',
            'banner.description',
        ];
    }

    /**
     * Is reorder enabled.
     *
     * @param  string  $ordering
     *
     * @return  bool
     */
    public function reorderEnabled(string $ordering): bool
    {
        return $ordering === 'banner.category_id, banner.ordering ASC';
    }

    /**
     * Can show Filter bar
     *
     * @param  array  $filter
     *
     * @return  bool
     */
    public function showFilterBar(array $filter): bool
    {
        foreach ($filter as $value) {
            if ($value !== null && (string) $value !== '') {
                return true;
            }
        }

        return false;
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
                $this->trans('unicorn.title.grid', title: $this->trans('luna.banner.title'))
            );
    }
}
