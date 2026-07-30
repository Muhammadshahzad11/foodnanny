@extends('installer.layouts.master')

@section('template_title')
    {{ trans('installer.permission.templateTitle') }}
@endsection

@section('title')
    {{ trans('installer.permission.title') }}
@endsection

@section('container')
    <ul class="installer-track">
        <li onclick="handleLinkForInstaller('{{ route('installer.index') }}')" class="done">
            <i class="lab-fill-home"></i>
        </li>
        <li onclick="handleLinkForInstaller('{{ route('installer.requirement') }}')" class="done">
            <i class="lab-fill-server"></i>
        </li>
        <li class="active"><i class="lab-fill-permission"></i></li>
        <li><i class="lab-fill-license"></i></li>
        <li><i class="lab-fill-site"></i></li>
        <li><i class="lab-fill-database"></i></li>
        <li><i class="lab-fill-final"></i></li>
    </ul>

    <span class="my-6 w-full h-[1px] bg-[#EFF0F6]"></span>

    <ul class="w-full rounded-lg overflow-hidden mb-8 border border-[#D9DBE9]">
        <li class="flex items-center justify-between py-3.5 px-6 border-b border-[#EFF0F6] last:border-none bg-[#F7F7FC]">
            <h3 class="text-sm font-semibold capitalize">{{ trans('installer.permission.permission_checking') }}</h3>
        </li>
        @foreach($permissions['permissions'] as $permission)
            <li class="flex items-center justify-between py-3.5 px-6 border-b border-[#EFF0F6] last:border-none">
                <span class="text-sm font-medium text-heading">{{ $permission['folder'] }} - {{ $permission['permission'] }}</span>
                <i class="lab-{{ $permission['isSet'] ? 'fill-save' : 'fill-info' }} circle-check text-sm text-[#{{ $permission['isSet'] ? '1AB759' : 'E93C3C' }}]"></i>
            </li>
        @endforeach
    </ul>

    @if ( ! isset($permissions['errors']))
        <a href="{{ route('installer.license') }}"
           class="w-fit mx-auto p-3 px-6 rounded-lg flex items-center justify-center gap-1 bg-primary text-white">
            <span class="text-sm font-medium capitalize">{{ trans('installer.permission.next') }}</span>
            <i class="lab-line-chevron-right text-sm"></i>
        </a>
    @endif
@endsection
