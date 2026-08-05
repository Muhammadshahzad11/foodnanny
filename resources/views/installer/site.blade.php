@extends('installer.layouts.master')

@section('template_title')
    {{ trans('installer.site.templateTitle') }}
@endsection

@section('title')
    {{ trans('installer.site.title') }}
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
         <li onclick="handleLinkForInstaller('{{ route('installer.license') }}')" class="done">
            <i class="lab-fill-license"></i>
        </li>
        <li class="active"><i class="lab-fill-site"></i></li>
        <li><i class="lab-fill-database"></i></li>
        <li><i class="lab-fill-final"></i></li>
    </ul>

    <span class="my-6 w-full h-[1px] bg-[#EFF0F6]"></span>

    <form method="post" action="{{ route('installer.siteStore') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="mb-4">
            <label class="text-sm font-medium block mb-1.5 text-heading">
                {{ trans('installer.site.label.app_name') }} <span class="text-[#E93C3C]">*</span>
            </label>
            <input name="app_name" type="text" value="{{ old('app_name') }}" class="w-full h-12 rounded-lg px-4 border border-[#D9DBE9]">
            @if ($errors->has('app_name'))
                <small class="block mt-2 text-sm font-medium text-[#E93C3C]">{{ $errors->first('app_name') }}</small>
            @endif
        </div>

        <div class="mb-8">
            <label class="text-sm font-medium block mb-1.5 text-heading">
                {{ trans('installer.site.label.app_url') }} <span class="text-[#E93C3C]">*</span>
            </label>
            <input name="app_url" type="text" value="{{ old('app_url') }}" class="w-full h-12 rounded-lg px-4 border border-[#D9DBE9]">
            @if ($errors->has('app_url'))
                <small class="block mt-2 text-sm font-medium text-[#E93C3C]">{{ $errors->first('app_url') }}</small>
            @endif
        </div>

        <button type="submit" class="w-fit mx-auto p-3 px-6 rounded-lg flex items-center justify-center gap-1 bg-primary text-white">
            <span class="text-sm font-medium capitalize">{{ trans('installer.site.next') }}</span>
            <i class="lab-line-chevron-right text-sm"></i>
        </button>
    </form>
@endsection
