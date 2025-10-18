@extends('layouts.app')

@section('title', 'Services')

@section('content')
<h1 class="text-2xl font-bold mb-6">Services</h1>

<div class="bg-blue-100 shadow-lg rounded-lg p-6">
    @if($services->count())
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Description</th>
                    <th class="px-6 py-3">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">{{ $service->name }}</td>
                    <td class="px-6 py-4">{{ $service->description ?? 'N/A' }}</td>
                    <td class="px-6 py-4">₱{{ number_format($service->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $services->links() }}</div>
    @else
        <p class="text-gray-500">No services found.</p>
    @endif
</div>
@endsection
