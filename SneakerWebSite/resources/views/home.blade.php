@extends('layouts.app')

@section('content')
@if (auth()->user()->isAdmin == true)
    <div id="app">
        <div class="container">
            <manage></manage>
        </div>
    </div>
@else
    <div id="app">
        <div class="container">
            <client :user-id="{{auth()->user()->id}}"></client>
        </div>
    </div>
@endif
@endsection