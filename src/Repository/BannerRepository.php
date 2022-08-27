<?php

/**
 * Part of starter project.
 *
 * @copyright    Copyright (C) 2021 __ORGANIZATION__.
 * @license        MIT
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Repository;

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Locale\LocaleAwareTrait;
use MyCLabs\Enum\Enum;
use Unicorn\Attributes\ConfigureAction;
use Unicorn\Attributes\Repository;
use Unicorn\Repository\Actions\BatchAction;
use Unicorn\Repository\Actions\ReorderAction;
use Unicorn\Repository\Actions\SaveAction;
use Unicorn\Repository\ListRepositoryInterface;
use Unicorn\Repository\ListRepositoryTrait;
use Unicorn\Repository\ManageRepositoryInterface;
use Unicorn\Repository\ManageRepositoryTrait;
use Unicorn\Selector\ListSelector;
use Windwalker\Query\Query;

/**
 * The BannerRepository class.
 */
#[Repository(entityClass: Banner::class)]
class BannerRepository implements ManageRepositoryInterface, ListRepositoryInterface
{
    use LocaleAwareTrait;
    use ManageRepositoryTrait;
    use ListRepositoryTrait;

    public function getListSelector(): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(Banner::class)
            ->leftJoin(Category::class, 'category', 'category.id', 'banner.category_id');

        return $selector;
    }

    public function getFrontListSelector(): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(Banner::class)
            ->leftJoin(Category::class, 'category', 'category.id', 'banner.category_id')
            ->where('banner.state', 1)
            ->ordering('banner.ordering', 'ASC')
            ->limit(0)
            ->setDefaultItemClass(Banner::class);

        if ($this->isLocaleEnabled()) {
            $selector->where('banner.language', 'in', ['*', $this->getLocale()]);
        }

        return $selector;
    }

    public function getBannersByCategoryId(int $categoryId): ListSelector
    {
        $selector = $this->getFrontListSelector()
            ->where('category.state', 1)
            ->where('banner.category_id', $categoryId);

        return $selector;
    }

    public function getBannersByCategoryAlias(string $categoryAlias): ListSelector
    {
        $selector = $this->getFrontListSelector()
            ->where('category.state', 1)
            ->where('category.alias', $categoryAlias);

        return $selector;
    }

    public function getBannersByType(string|\UnitEnum|Enum $type): ListSelector
    {
        $selector = $this->getFrontListSelector()
            ->where('banner.type', $type);

        return $selector;
    }

    #[ConfigureAction(SaveAction::class)]
    protected function configureSaveAction(SaveAction $action): void
    {
        $this->newOrderLast($action);
    }

    #[ConfigureAction(ReorderAction::class)]
    protected function configureReorderAction(ReorderAction $action): void
    {
        $action->setReorderGroupHandler(
            function (Query $query, Banner $banner) {
                $query->where('category_id', $banner->getCategoryId());
                $query->where('type', $banner->getType());
            }
        );
    }

    #[ConfigureAction(BatchAction::class)]
    protected function configureBatchAction(BatchAction $action): void
    {
        //
    }
}
