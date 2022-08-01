<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        \Lyrasoft\Banner\Module\Admin\Banner\BannerEditView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

declare(strict_types=1);

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Banner\Module\Admin\Banner\BannerEditView;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Form\Form;

/**
 * @var Form      $form
 * @var Banner $item
 */
?>

@extends('admin.global.body-edit')

@section('toolbar-buttons')
    @include('edit-toolbar')
@stop

@section('content')
    <form name="admin-form" id="admin-form"
        uni-form-validate='{"scroll": true}'
        action="{{ $nav->to('banner_edit') }}"
        method="POST" enctype="multipart/form-data">

        <div class="row">
            <div class="col-md-7">
                <x-fieldset name="basic" :title="$lang('unicorn.fieldset.basic')"
                    :form="$form"
                    class="mb-4"
                    is="card"
                >
                </x-fieldset>
            </div>
            <div class="col-md-5">
                <x-fieldset name="meta" :title="$lang('unicorn.fieldset.meta')"
                    :form="$form"
                    class="mb-4"
                    is="card"
                >
                </x-fieldset>
            </div>
        </div>

        <x-card name="images" :title="$lang('banner.fieldset.images')"
            class="mb-4"
        >
            <div class="row row-cols-2">
                <x-field :field="$form['image']" class="mb-4"></x-field>
                <x-field :field="$form['mobile_image']" class="mb-4"></x-field>
            </div>
        </x-card>

        <x-card :title="$lang('banner.fieldset.video')">
            <x-field :field="$form['video_type']" class="mb-4">

            </x-field>

            <div class="row row-cols-2">
                <div class="">
                    <x-field :field="$form['video']" class="mb-4"></x-field>
                    <x-field :field="$form['video_upload']" class="mb-4"></x-field>

                    <x-video-preview :field="$form['video']" :item="$item" class="c-preview-desktop"></x-video-preview>
                </div>
                <div class="">
                    <x-field :field="$form['mobile_video']" class="mb-4"></x-field>
                    <x-field :field="$form['mobile_video_upload']" class="mb-4"></x-field>

                    <x-video-preview :field="$form['mobile_video']" :item="$item" class="c-preview-mobile"></x-video-preview>
                </div>
            </div>
        </x-card>

        <div class="d-none">
            @if ($idField = $form?->getField('id'))
                <input name="{{ $idField->getInputName() }}" type="hidden" value="{{ $idField->getValue() }}" />
            @endif

            <x-csrf></x-csrf>
        </div>
    </form>
@stop
