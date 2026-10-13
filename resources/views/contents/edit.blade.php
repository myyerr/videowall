<x-app-layout :pageTitle="'Edit Request — ' . $content->title">
    <x-slot name="slot">
        @include('contents._form')
    </x-slot>
</x-app-layout>
