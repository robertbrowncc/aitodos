@extends('layouts.app')

@section('title', 'Todo App 🐱')

@section('content')
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <meta name="asset-url" content="{{ asset('') }}">
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
            @vite(['resources/css/app.css', 'resources/css/background.css', 'resources/css/custom.css', 'resources/js/app.js'])
        </head>
        <body>
            <div id="app"></div>
        </body>
    </html>
@endsection
