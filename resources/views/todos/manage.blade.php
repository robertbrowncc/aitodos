@extends('layouts.app')

@section('title', 'Todos')

@section('header')
    <header class="fixed top-0 left-0 right-0 bg-white shadow-md z-50">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold">Todos</h1>
                <a href="{{ route('home') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    Back to Home
                </a>
            </div>
        </div>
    </header>
@endsection

@section('app-class', 'pt-24 pb-8')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold">Todos</h1>
                <div id="todo-list"></div>
            </div>
        </div>
    </div>
@endsection
