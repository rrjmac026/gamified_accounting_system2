@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-4">Notifications</h2>

    <!-- Flash messages -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg divide-y">
        @forelse($notifications as $notification)
            <div class="p-4 flex justify-between items-center {{ $notification->is_read ? 'bg-gray-50' : 'bg-white' }}">
                <div>
                    <h3 class="font-semibold">{{ $notification->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                    <small class="text-gray-400">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <div class="flex space-x-2">
                    @if(!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-blue-600 hover:underline">Mark as read</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-4 text-gray-500">No notifications found.</div>
        @endforelse
    </div>

    <form method="POST" action="{{ route('notifications.readAll') }}" class="mt-4">
        @csrf
        @method('PATCH')
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Mark All as Read</button>
    </form>
</div>
@endsection
