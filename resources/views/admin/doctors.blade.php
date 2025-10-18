@extends('layouts.app')

@section('title', 'Doctors')

@section('content')
<h1 class="text-2xl font-bold mb-6">Doctors</h1>

<div class="bg-blue-100 shadow-lg rounded-lg p-6">
    @if($doctors->count())
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Specialization</th>
                    <th class="px-6 py-3 text-center">Actions</th> <!-- 👈 Added -->
                </tr>
            </thead>
            <tbody>
                @foreach($doctors as $doctor)
                <tr>
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">{{ $doctor->user->name }}</td>
                    <td class="px-6 py-4">{{ $doctor->user->email }}</td>
                    <td class="px-6 py-4">{{ $doctor->specialization ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this doctor?');" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $doctors->links() }}</div>
    @else
        <p class="text-gray-500">No doctors found.</p>
    @endif
</div>
@endsection
