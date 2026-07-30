@extends('installer.layouts.master')

@section('template_title')
    {{ trans('installer.license.templateTitle') }}
@endsection

@section('title')
    {{ trans('installer.license.title') }}
@endsection

@section('container')
    <ul class="installer-track">
        <li onclick="handleLinkForInstaller('{{ route('installer.index') }}')" class="done">
            <i class="lab-fill-home"></i>
        </li>
        <li onclick="handleLinkForInstaller('{{ route('installer.requirement') }}')" class="done">
            <i class="lab-fill-server"></i>
        </li>
        <li onclick="handleLinkForInstaller('{{ route('installer.permission') }}')" class="done">
            <i class="lab-fill-permission"></i>
        </li>
        <li class="active"><i class="lab-fill-license"></i></li>
        <li><i class="lab-fill-site"></i></li>
        <li><i class="lab-fill-database"></i></li>
        <li><i class="lab-fill-final"></i></li>
    </ul>

    <span class="my-6 w-full h-[1px] bg-[#EFF0F6]"></span>

    <form method="post" action="{{ route('installer.licenseStore') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="mb-4">
            <label class="text-sm font-medium block mb-1.5 text-heading">
                {{ trans('installer.license.label.license_code') }} <span class="text-[#E93C3C]">*</span>
                <span id="installer-link" class="text-primary modal-show underline cursor-pointer">({{ trans('installer.license.active_process') }})</span>
            </label>
            <input name="license_key" type="text" value="{{ old('license_key') }}"
                class="w-full h-12 rounded-lg px-4 border border-[#D9DBE9]">
            @if ($errors->has('license_key'))
                <small class="block mt-2 text-sm font-medium text-[#E93C3C]">{{ $errors->first('license_key') }}</small>
            @endif
            @if ($errors->has('global'))
                <small class="block mt-2 text-sm font-medium text-[#E93C3C]">{{ $errors->first('global') }}</small>
            @endif
        </div>

        <button type="submit" class="w-fit mx-auto p-3 px-6 rounded-lg flex items-center justify-center gap-1 bg-primary text-white">
            <span class="text-sm font-medium capitalize">{{ trans('installer.license.next') }}</span>
            <i class="lab-line-chevron-right text-sm"></i>
        </button>
    </form>

    <div id="installer-modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('installer.license.active_process') }}</h3>
                <button id="installer-modal-close" class="lab-line-close text-xl text-slate-400 hover:text-red-500"></button>
            </div>
            <div class="modal-body">
                <section class="mb-6">
                    <h6>{{ __('1. Goto inilabs official site') }} <a class="text-blue-500" target="_blank" href="http://inilabs.net">inilabs.net</a></h6>
                    <h6 class="mb-2">{{ __('2. Now create an account in our site.') }}</h6>
                    <picture>
                        <img src="{{ asset('images/default/installer/register.png') }}" class="border border-solid border-blue-300" alt="register">
                    </picture>
                </section>

                <section class="mb-6">
                    <h6 class="mb-2">{{ __('3. Click the below link and verify your email.') }}</h6>
                    <picture>
                        <img src="{{ asset('images/default/installer/verify.png') }}" class="border border-solid border-blue-300" alt="verify">
                    </picture>
                </section>

                <section class="mb-6">
                    <h6>{{ __('4. Now click') }} <b>{{ __('Active Purchase Key') }}</b> {{ __('from home page and fill you information.') }}</h6>
                    <h6 class="pl-3 mb-1">{{ __('a). Select your product.') }}</h6>
                    <h6 class="pl-3 mb-1">{{ __('b). Enter your domain, Which domain you would be using this product.') }}</h6>
                    <h6 class="pl-3 mb-2">{{ __('c). Enter your envato purchase key and click submit.') }}</h6>
                    <picture>
                        <img src="{{ asset('images/default/installer/active-product.png') }}" class="border border-solid border-blue-300" alt="active-product">
                    </picture>
                </section>

                <section class="mb-6">
                    <h6 class="mb-2">{{ __('5. Now copy your') }} <b>{{ __('Active License') }}</b> {{ __('and install the product by this license.') }}</h6>
                    <picture>
                        <img src="{{ asset('images/default/installer/license.png') }}" class="border border-solid border-blue-300" alt="license">
                    </picture>
                </section>
            </div>
        </div>
    </div>
@endsection
