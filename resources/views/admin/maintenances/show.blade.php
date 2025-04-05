@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Maintenance Record Details</h1>
        <div class="flex space-x-4">
            <a href="{{ route('admin.maintenances.edit', $maintenance) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Edit Record
            </a>
            <a href="{{ route('admin.maintenances.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Equipment Information</h2>
                <div class="space-y-3">
                    <p><span class="font-medium">Name:</span> {{ $maintenance->equipment->name ?? 'N/A' }}</p>
                    <p><span class="font-medium">Category:</span> {{ $maintenance->equipment->category->name ?? 'N/A' }}</p>
                    <p><span class="font-medium">Status:</span> {{ $maintenance->equipment->status ?? 'N/A' }}</p>
                    <p><span class="font-medium">Condition:</span> {{ $maintenance->equipment->condition ?? 'N/A' }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Maintenance Details</h2>
                <div class="space-y-3">
                    <p><span class="font-medium">Type:</span> {{ ucfirst($maintenance->type) }}</p>
                    <p><span class="font-medium">Date:</span> {{ $maintenance->maintenance_date->format('M d, Y') }}</p>
                    <p><span class="font-medium">Performed By:</span> {{ $maintenance->performed_by }}</p>
                    <p><span class="font-medium">Cost:</span> ${{ number_format($maintenance->cost, 2) }}</p>
                    <p>
                        <span class="font-medium">Status:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $maintenance->status_badge_class }}">
                            {{ ucfirst($maintenance->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
            <p class="text-gray-700">{{ $maintenance->description }}</p>
        </div>

        @if($maintenance->receipt_path)
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Receipt</h2>
                <div class="border rounded-lg p-4 bg-gray-50">
                    @php
                        $extension = pathinfo($maintenance->receipt_path, PATHINFO_EXTENSION);
                        $isPdf = strtolower($extension) === 'pdf';
                    @endphp

                    @if($isPdf)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $maintenance->receipt_path) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center"
                               target="_blank">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View PDF Receipt
                            </a>
                        </div>
                        <embed src="{{ asset('storage/' . $maintenance->receipt_path) }}" 
                               type="application/pdf" 
                               width="100%" 
                               height="500px"
                               class="border">
                    @else
                        <img src="{{ asset('storage/' . $maintenance->receipt_path) }}" 
                             alt="Maintenance Receipt" 
                             class="max-w-full h-auto rounded shadow-sm">
                    @endif
                </div>
            </div>
        @endif

        @if($maintenance->notes)
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
                <p class="text-gray-700">{{ $maintenance->notes }}</p>
            </div>
        @endif

        <div class="mt-8 flex justify-end">
            <form action="{{ route('admin.maintenances.destroy', $maintenance) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this maintenance record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Delete Record
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 