@extends('installer.layouts.master')

@section('template_title')
    {{ trans('installer.welcome.templateTitle') }}
@endsection

@section('title')
    {{ trans('installer.welcome.title') }}
@endsection

@section('container')
    <ul class="installer-track">
        <li class="active"><i class="lab-fill-home"></i></li>
        <li><i class="lab-fill-server"></i></li>
        <li><i class="lab-fill-permission"></i></li>
        <li><i class="lab-fill-license"></i></li>
        <li><i class="lab-fill-site"></i></li>
        <li><i class="lab-fill-database"></i></li>
        <li><i class="lab-fill-final"></i></li>
    </ul>

    <span class="my-6 w-full h-[1px] bg-[#EFF0F6]"></span>

    <div class="text-center">
        <h4 class="text-sm font-medium mb-7">{{ trans('installer.welcome.message') }}</h4>
        <a href="{{ route('installer.requirement') }}" class="p-3 px-6 rounded-lg inline-flex items-center justify-center gap-1 bg-primary text-white">
            {{ trans('installer.welcome.next') }}
            <i class="lab-line-chevron-right text-sm"></i>
        </a>
    </div>
@endsection
