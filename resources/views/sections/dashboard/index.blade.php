@extends('fair-queue::vendor.indigo-layout.main')

@section('meta_title',  __('fair-queue::dashboard.startup_baselinker') . ' // ' .config('app.name') )
@section('meta_description', _p('pages.dashboard.meta_description', 'Check your dashboard with all important metrics and values.'))

@push('head')
    @include('fair-queue::integration.favicons')
    @include('fair-queue::integration.ga')
@endpush

@section('content')


@endsection
