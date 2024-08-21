@extends('statamic::layout')
@section('title', __('Manage Campaign Monitor Settings'))

@section('content')
    <publish-form
            title="{{ __('Manage Campaign Monitor Settings') }}"
            action="{{ cp_route('campaign-monitor.config.update') }}"
            :blueprint='@json($blueprint)'
            :meta='@json($meta)'
            :values='@json($values)'
    ></publish-form>
@stop