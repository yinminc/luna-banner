<?php

/**
 * Part of eva project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Widget\Banner;

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Banner\Repository\BannerRepository;
use Lyrasoft\Banner\Service\BannerService;
use Lyrasoft\Luna\Field\CategoryListField;
use Lyrasoft\Luna\Widget\AbstractWidget;
use Unicorn\Field\ButtonRadioField;
use Unicorn\Field\InlineField;
use Unicorn\Field\MultiUploaderField;
use Unicorn\Field\RepeatableField;
use Unicorn\Field\SwitcherField;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Runtime\Config;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\NumberField;
use Windwalker\Form\Field\TextareaField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Field\UrlField;
use Windwalker\Form\Form;
use function Windwalker\collect;
use function Windwalker\uid;

/**
 * The BannerWidget class.
 */
class BannerWidget extends AbstractWidget implements ViewModelInterface
{
    use TranslatorTrait;

    public function __construct(protected Config $config, protected BannerService $bannerService)
    {
    }

    public static function getTypeIcon(): string
    {
        return 'fa fa-gallery-thumbnails';
    }

    public static function getTypeTitle(LangService $lang): string
    {
        return $lang('banner.widget.title');
    }

    public static function getTypeDescription(LangService $lang): string
    {
        return $lang('banner.widget.description');
    }

    public function getLayout(): string
    {
        return 'banners';
    }

    public function define(Form $form): void
    {
        $form->group(
            'params',
            function (Form $form) {
                $form->fieldset(
                    'banners',
                    function (Form $form) {
                        $form->add('show_type', ButtonRadioField::class)
                            ->label($this->trans('banner.widget.field.show.type'))
                            ->tap(
                                function (ButtonRadioField $field) {
                                    if ($this->bannerService->getTypeEnum()) {
                                        $field->option(
                                            $this->trans('banner.widget.field.show.type.option.type'),
                                            'type'
                                        );
                                        $field->defaultValue('type');
                                    } else {
                                        $field->option(
                                            $this->trans('banner.widget.field.show.type.option.category'),
                                            'category'
                                        );
                                        $field->defaultValue('category');
                                    }
                                }
                            )
                            ->option($this->trans('banner.widget.field.show.type.option.files'), 'files');

                        $typeEnum = $this->bannerService->getTypeEnum();

                        if ($typeEnum) {
                            $form->add('type', ListField::class)
                                ->label($this->trans('banner.field.type'))
                                ->registerFromEnums($typeEnum)
                                ->set('showon', ['params/show_type' => 'type']);
                        } else {
                            $form->add('category_id', CategoryListField::class)
                                ->label($this->trans('banner.field.category'))
                                ->categoryType('banner')
                                ->set('showon', ['params/show_type' => 'category']);
                        }

                        $form->add('banners', MultiUploaderField::class)
                            ->label($this->trans('banner.widget.field.banners'))
                            ->uploadProfile(
                                $this->config->getDeep('banner.widget.upload_profile') ?? 'image'
                            )
                            ->configureForm(
                                function (Form $form) {
                                    $form->add('title', TextField::class)
                                        ->label($this->trans('banner.field.title'));

                                    $form->add('subtitle', TextField::class)
                                        ->label($this->trans('banner.field.subtitle'));

                                    $form->add('link', UrlField::class)
                                        ->label($this->trans('banner.field.link'));

                                    $form->add('description', TextareaField::class)
                                        ->label($this->trans('banner.field.description'));
                                }
                            )
                            ->set('showon', ['params/show_type' => 'files'])
                            ->maxFiles(15);
                    }
                )
                    ->title($this->trans('banner.widget.tab.banners'));

                $form->fieldset(
                    'display',
                    function (Form $form) {
                        $form->add('height', TextField::class)
                            ->label($this->trans('banner.widget.field.height'))
                            ->help($this->trans('banner.widget.field.height.help'));

                        $form->add('ratio', InlineField::class)
                            ->label($this->trans('banner.widget.field.ratio'))
                            ->help($this->trans('banner.widget.field.ratio.help'))
                            ->showLabel(true)
                            ->configureForm(
                                function (Form $form) {
                                    $form->add('width_ratio', NumberField::class)
                                        ->label($this->trans('banner.widget.field.ratio.width'))
                                        ->min(1);

                                    $form->add('height_ratio', NumberField::class)
                                        ->label($this->trans('banner.widget.field.ratio.height'))
                                        ->min(1);
                                }
                            );

                        // Items
                        $form->add('items', NumberField::class)
                            ->label($this->trans('banner.widget.field.items'))
                            ->defaultValue(1);

                        $form->add('space_between', NumberField::class)
                            ->label($this->trans('banner.widget.field.space.between'));

                        // RWD
                        $form->add('responsive', RepeatableField::class)
                            ->label($this->trans('banner.widget.field.responsive'))
                            ->help($this->trans('banner.widget.field.responsive.help'))
                            ->sortable(true)
                            ->configureForm(
                                function (Form $form) {
                                    $form->add('screen_width', NumberField::class)
                                        ->label($this->trans('banner.widget.field.screen.width'));

                                    $form->add('items', NumberField::class)
                                        ->label($this->trans('banner.widget.field.items'));

                                    $form->add('space_between', NumberField::class)
                                        ->label($this->trans('banner.widget.field.space.between'));
                                }
                            );
                    }
                )
                    ->title($this->trans('banner.widget.tab.display'));

                $form->fieldset(
                    'control',
                    function (Form $form) {
                        $form->add('effect', ListField::class)
                            ->label($this->trans('banner.widget.field.effect'))
                            ->option('slide', 'slide')
                            ->option('fade', 'fade')
                            ->option('cube', 'cube')
                            ->option('coverflow', 'coverflow')
                            ->option('flip', 'flip')
                            ->option('creative', 'creative')
                            ->option('cards', 'cards')
                            ->defaultValue('slide');

                        $form->add('loop', SwitcherField::class)
                            ->label($this->trans('banner.widget.field.loop'))
                            ->circle(true)
                            ->color('primary')
                            ->defaultValue('0');

                        $form->add('rewind', SwitcherField::class)
                            ->label($this->trans('banner.widget.field.rewind'))
                            ->circle(true)
                            ->color('primary')
                            ->defaultValue('0');

                        $form->add('navigation', SwitcherField::class)
                            ->label($this->trans('banner.widget.field.navigation'))
                            ->circle(true)
                            ->color('primary')
                            ->defaultValue('1');

                        $form->add('pagination', SwitcherField::class)
                            ->label($this->trans('banner.widget.field.pagination'))
                            ->circle(true)
                            ->color('primary')
                            ->defaultValue('0');

                        $form->add('scrollbar', SwitcherField::class)
                            ->label($this->trans('banner.widget.field.scrollbar'))
                            ->circle(true)
                            ->color('primary')
                            ->defaultValue('0');

                        $form->add('auto_height', SwitcherField::class)
                            ->label($this->trans('banner.widget.field.auto.height'))
                            ->circle(true)
                            ->color('primary')
                            ->defaultValue('1');
                    }
                )
                    ->title($this->trans('banner.widget.tab.control'));

                $form->fieldset(
                    'html',
                    function (Form $form) {
                        $form->add('html_id', TextField::class)
                            ->label($this->trans('banner.widget.field.html.id'));

                        $form->add('html_class', TextField::class)
                            ->label($this->trans('banner.widget.field.html.class'));

                        $form->add('open_new_window', SwitcherField::class)
                            ->label($this->trans('banner.widget.field.open.new.window'))
                            ->circle(true)
                            ->color('primary')
                            ->defaultValue('1');

                        $form->add('component', TextField::class)
                            ->label($this->trans('banner.widget.field.component'))
                            ->defaultValue('swiper-banners');
                    }
                )
                    ->title($this->trans('banner.widget.tab.html'));
            }
        );
    }

    public function prepare(AppContext $app, View $view): mixed
    {
        $widget = $this->getData();

        $params = collect($widget->getParams());

        if ($params['show_type'] === 'files') {
            $banners = collect();

            foreach ((array) $params['banners'] as $file) {
                $banner = new Banner();
                $banner->setLink($file['link']);
                $banner->setImage($file['url']);
                $banner->setTitle($file['title']);
                $banner->setSubtitle($file['subtitle']);
                $banner->setDescription($file['description']);

                $banners[] = $banner;
            }
        } else {
            $bannerRepo = $app->service(BannerRepository::class);

            if ($this->bannerService->getTypeEnum()) {
                $banners = $bannerRepo->getBannersByType((string) $params['type'])->all();
            } else {
                $banners = $bannerRepo->getBannersByCategoryId((int) $params['category_id'])->all();
            }
        }

        $height = $params['height'] ?: null;
        $ratio  = null;

        if (!$height) {
            $w = $params['width_ratio'] ?: 1;
            $h = $params['height_ratio'] ?: 1;

            $ratio = $w / $h;
        }

        $attrs = [
            'id' => $params['html_id'] ?: 'widget-banner-' . uid(),
            'class' => $params['html_class'],
            'navigation' => (bool) $params['navigation'],
            'pagination' => (bool) $params['pagination'],
            'scrollbar' => (bool) $params['scrollbar'],
            'height' => $height,
            'ratio' => $ratio,
            'linkTarget' => $params['open_new_window'] ? '_blank' : null
        ];

        $rwd = [];

        foreach ((array) $params['responsive'] as $bp) {
            $w = (string) ($bp['screen_width'] ?? '');

            if (!$w) {
                continue;
            }

            $bpdata = [];

            if ($bp['items'] ?? null) {
                $bpdata['slidesPerView'] = (int) $bp['items'];
            }

            if ($bp['space_between'] ?? null) {
                $bpdata['spaceBetween'] = (int) $bp['space_between'];
            }

            $rwd[$w] = $bpdata;
        }

        $options = [
            'height' => $height,
            'slidesPerView' => $params['items'] ?? 1,
            'spaceBetween' => (int) ($params['space_between'] ?: 0),
            'effect' => $params['effect'],
            'loop' => (bool) $params['loop'],
            'rewind' => (bool) $params['rewind'],
            'autoHeight' => (bool) $params['auto_height'],
            'breakpoints' => $rwd
        ];

        $component = $params['component'] ?: 'swiper-banners';

        return array_merge(
            compact(
                'banners',
                'params',
                'options',
                'component'
            ),
            $attrs
        );
    }
}
