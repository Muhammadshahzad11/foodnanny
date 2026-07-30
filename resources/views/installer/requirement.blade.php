@extends('installer.layouts.master')

@section('template_title')
    {{ trans('installer.requirement.templateTitle') }}
@endsection

@section('title')
    {{ trans('installer.requirement.title') }}
@endsection

@section('container')
    <ul class="installer-track">
        <li onclick="handleLinkForInstaller('{{ route('installer.index') }}')" class="done">
            <i class="lab-fill-home"></i>
        </li>
        <li class="active"><i class="lab-fill-server"></i></li>
        <li><i class="lab-fill-permission"></i></li>
        <li><i class="lab-fill-license"></i></li>
        <li><i class="lab-fill-site"></i></li>
        <li><i class="lab-fill-database"></i></li>
        <li><i class="lab-fill-final"></i></li>
    </ul>

    <span class="my-6 w-full h-[1px] bg-[#EFF0F6]"></span>

    @foreach($requirements['requirements'] as $type => $requirement)
        <ul class="w-full rounded-lg overflow-hidden mb-8 border border-[#D9DBE9]">
            <li class="flex items-center justify-between gap-2 py-3.5 px-6 border-b border-[#EFF0F6] last:border-none bg-[#F7F7FC]">
                @if($type == 'php')
                    <h3 class="text-sm font-semibold capitalize">{{ ucfirst($type) }}
                        <span
                            class="text-xs font-medium lowercase">( {{ trans('installer.requirement.version') }} {{ $phpSupportInfo['minimum'] }} {{ trans('installer.requirement.required') }})</span>
                    </h3>
                    <span class="flex items-center gap-1 text-[#1AB759]">
                        <span class="text-sm font-semibold">{{ $phpSupportInfo['current'] }}</span>
                        <i class="lab-{{ $phpSupportInfo['supported'] ? 'fill-save' : 'fill-info' }} text-sm text-[#{{ $phpSupportInfo['supported'] ? '1AB759' : 'E93C3C' }}]"></i>
                    </span>
                @endif
            </li>

            @foreach($requirements['requirements'][$type] as $extension => $enabled)
                <li class="flex items-center justify-between py-3.5 px-6 border-b border-[#EFF0F6] last:border-none">
                    <span class="text-sm font-medium capitalize text-heading">{{ $extension }}</span>
                    <i class="lab-{{ $enabled ? 'fill-save' : 'fill-info' }} text-sm text-[#{{ $enabled ? '1AB759' : 'E93C3C' }}]"></i>
                </li>
            @endforeach
        </ul>
    @endforeach

    @if (!$requirements['errors'] && $phpSupportInfo['supported'] )
        <a href="{{ route('installer.permission') }}" class="w-fit mx-auto p-3 px-6 rounded-lg flex items-center justify-center gap-1 bg-primary text-white">
            <span class="text-sm font-medium capitalize">{{ trans('installer.requirement.next') }}</span>
            <i class="lab-line-chevron-right text-sm"></i>
        </a>
    @endif
@endsection
