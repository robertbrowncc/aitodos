@extends('layouts.app')

@section('title', 'Todo Manager')

@section('app-class', 'py-8')

@section('content')
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Todo Manager</h1>
            <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                Back to Home
            </a>
        </div>
        <todo-list></todo-list>
    </div>
@endsection
