<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\Banner\Module\Admin\Banner\Form;

use Lyrasoft\Banner\Enum\BannerVideoType;
use Lyrasoft\Banner\Service\BannerService;
use Lyrasoft\Luna\Field\CategoryListField;
use Lyrasoft\Luna\Field\LanguageListField;
use Lyrasoft\Luna\Field\UserModalField;
use Lyrasoft\Luna\Locale\LocaleAwareTrait;
use Unicorn\Field\ButtonRadioField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\FileDragField;
use Unicorn\Field\SingleImageDragField;
use Unicorn\Field\SwitcherField;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Field\HiddenField;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\TextareaField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Field\UrlField;
use Windwalker\Form\FieldDefinitionInterface;
use Windwalker\Form\Form;

/**
 * The EditForm class.
 */
class EditForm implements FieldDefinitionInterface
{
    use TranslatorTrait;
    use LocaleAwareTrait;

    public function __construct(
        protected BannerService $bannerService,
        protected ?string $type = null
    ) {
    }

    /**
     * Define the form fields.
     *
     * @param Form $form The Windwalker form object.
     *
     * @return  void
     */
    public function define(Form $form): void
    {
        $form->fieldset(
            'basic',
            function (Form $form) {
                $form->add('title', TextField::class)
                    ->label('Title')
                    ->addFilter('trim')
                    ->required(true);

                $form->add('subtitle', TextField::class)
                    ->label($this->trans('banner.field.subtitle'));

                $form->add('link', UrlField::class)
                    ->label($this->trans('banner.field.link'));

                $form->add('description', TextareaField::class)
                    ->label($this->trans('unicorn.field.description'))
                    ->rows(3);
            }
        );

        $form->fieldset(
            'images',
            function (Form $form) {
                $typeConfig = $this->bannerService->getTypeConfig($this->type);

                $form->add('image', SingleImageDragField::class)
                    ->label($this->trans('unicorn.field.image'))
                    ->crop($typeConfig['desktop']['crop'] ?? false)
                    ->width($typeConfig['desktop']['width'] ?? 1080)
                    ->height($typeConfig['desktop']['height'] ?? 800)
                    ->ajax($typeConfig['desktop']['ajax'] ?? false)
                    ->uploadProfile($typeConfig['desktop']['profile'] ?? 'image')
                    ->showSizeNotice(true);

                $form->add('mobile_image', SingleImageDragField::class)
                    ->label($this->trans('banner.field.mobile_image'))
                    ->crop($typeConfig['mobile']['crop'] ?? false)
                    ->width($typeConfig['mobile']['width'] ?? 720)
                    ->height($typeConfig['mobile']['height'] ?? 720)
                    ->ajax($typeConfig['desktop']['ajax'] ?? false)
                    ->uploadProfile($typeConfig['desktop']['profile'] ?? 'image')
                    ->showSizeNotice(true);
            }
        );

        $form->add('video_type', ButtonRadioField::class)
            ->label($this->trans('banner.field.video_type'))
            ->registerFromEnums(BannerVideoType::class, $this->lang)
            ->defaultValue(BannerVideoType::EMBED);

        $form->add('video', UrlField::class)
            ->label($this->trans('banner.field.video'));

        $form->add('video_upload', FileDragField::class)
            ->label($this->trans('banner.field.video.upload'))
            ->set('showon', ['video_type' => 'file'])
            ->accept('video/*');

        $form->add('mobile_video', UrlField::class)
            ->label($this->trans('banner.field.mobile_video'));

        $form->add('mobile_video_upload', FileDragField::class)
            ->label($this->trans('banner.field.mobile_video.upload'))
            ->set('showon', ['video_type' => 'file'])
            ->accept('video/*');

        $form->fieldset(
            'meta',
            function (Form $form) {
                $typeEnum = $this->bannerService->getTypeEnum();

                if ($typeEnum) {
                    $form->add('type', ListField::class)
                        ->label($this->trans('banner.field.type'))
                        ->registerFromEnums($typeEnum);
                } else {
                    $form->add('category_id', CategoryListField::class)
                        ->label($this->trans('banner.field.category'))
                        ->categoryType('banner');
                }

                $form->add('state', SwitcherField::class)
                    ->label($this->trans('banner.field.published'))
                    ->circle(true)
                    ->color('success')
                    ->defaultValue('0');

                if ($this->isLocaleEnabled()) {
                    $form->add('language', LanguageListField::class)
                        ->label($this->trans('banner.field.language'));
                }

                $form->add('created', CalendarField::class)
                    ->label($this->trans('unicorn.field.created'));

                $form->add('modified', CalendarField::class)
                    ->label($this->trans('unicorn.field.modified'));

                $form->add('created_by', UserModalField::class)
                    ->label($this->trans('unicorn.field.author'));

                $form->add('modified_by', UserModalField::class)
                    ->label($this->trans('unicorn.field.modified_by'));
            }
        );

        $form->add('id', HiddenField::class);
    }
}
