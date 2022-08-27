<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Module\Admin\Banner\Form;

use Lyrasoft\Banner\Service\BannerService;
use Lyrasoft\Luna\Field\CategoryListField;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\SearchField;
use Windwalker\Form\FieldDefinitionInterface;
use Windwalker\Form\Form;

/**
 * The GridForm class.
 */
class GridForm implements FieldDefinitionInterface
{
    use TranslatorTrait;

    public function __construct(
        protected BannerService $bannerService
    ) {
    }

    /**
     * Define the form fields.
     *
     * @param  Form  $form  The Windwalker form object.
     *
     * @return  void
     */
    public function define(Form $form): void
    {
        $form->ns(
            'search',
            function (Form $form) {
                $form->add('*', SearchField::class)
                    ->label($this->trans('unicorn.grid.search.label'))
                    ->placeholder($this->trans('unicorn.grid.search.label'))
                    ->onchange('this.form.submit()');
            }
        );

        $form->ns(
            'filter',
            function (Form $form) {
                $form->add('banner.state', ListField::class)
                    ->label($this->trans('unicorn.field.state'))
                    ->option($this->trans('unicorn.select.placeholder'), '')
                    ->registerOptions(BasicState::getTransItems($this->lang))
                    ->onchange('this.form.submit()');

                if ($enum = $this->bannerService->getTypeEnum()) {
                    $form->add('banner.type', ListField::class)
                        ->label($this->trans('banner.field.type'))
                        ->registerFromEnums($enum)
                        ->onchange('this.form.submit()');
                } else {
                    $form->add('banner.category_id', CategoryListField::class)
                        ->label($this->trans('banner.field.category'))
                        ->option($this->trans('unicorn.select.placeholder'), '')
                        ->categoryType('banner')
                        ->onchange('this.form.submit()');
                }
            }
        );

        $form->ns(
            'batch',
            function (Form $form) {
                $form->add('state', ListField::class)
                    ->label($this->trans('unicorn.field.state'))
                    ->option($this->trans('unicorn.select.no.change'), '')
                    ->registerOptions(BasicState::getTransItems($this->lang));
            }
        );
    }
}
