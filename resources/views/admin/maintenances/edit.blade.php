@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Maintenance Record</h1>
        <a href="{{ route('admin.maintenances.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to List
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.maintenances.update', $maintenance) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="equipment_id" class="block text-sm font-medium text-gray-700">Equipment</label>
                    <select name="equipment_id" id="equipment_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Equipment</option>
                        @foreach($equipment as $item)
                            <option value="{{ $item->id }}" 
                                    {{ old('equipment_id', $maintenance->equipment_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('equipment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Maintenance Type</label>
                    <select name="type" id="type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Type</option>
                        <option value="preventive" {{ old('type', $maintenance->type) == 'preventive' ? 'selected' : '' }}>Preventive</option>
                        <option value="corrective" {{ old('type', $maintenance->type) == 'corrective' ? 'selected' : '' }}>Corrective</option>
                        <option value="predictive" {{ old('type', $maintenance->type) == 'predictive' ? 'selected' : '' }}>Predictive</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="maintenance_date" class="block text-sm font-medium text-gray-700">Maintenance Date</label>
                    <input type="date" name="maintenance_date" id="maintenance_date" 
                           value="{{ old('maintenance_date', $maintenance->maintenance_date->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('maintenance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="performed_by" class="block text-sm font-medium text-gray-700">Performed By</label>
                    <input type="text" name="performed_by" id="performed_by" 
                           value="{{ old('performed_by', $maintenance->performed_by) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('performed_by')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700">Cost</label>
                    <input type="number" name="cost" id="cost" step="0.01"
                           value="{{ old('cost', $maintenance->cost) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Status</option>
                        <option value="scheduled" {{ old('status', $maintenance->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in-progress" {{ old('status', $maintenance->status) == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $maintenance->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $maintenance->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $maintenance->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Receipt Upload Section -->
            <div class="mt-6">
                <label for="receipt" class="block text-sm font-medium text-gray-700">Receipt (Image or PDF)</label>
                
                @if($maintenance->receipt_path)
                    <div class="mb-4 border p-4 rounded-md bg-gray-50">
                        <p class="text-sm text-gray-700 mb-2">Current Receipt:</p>
                        @php
                            $extension = pathinfo($maintenance->receipt_path, PATHINFO_EXTENSION);
                            $isPdf = strtolower($extension) === 'pdf';
                        @endphp
                        
                        @if($isPdf)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $maintenance->receipt_path) }}" 
                                   class="text-blue-600 hover:text-blue-800"
                                   target="_blank">View PDF Receipt</a>
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $maintenance->receipt_path) }}" 
                                 alt="Current Receipt" 
                                 class="h-32 w-auto border rounded shadow-sm">
                        @endif
                    </div>
                @endif
                
                <div class="mt-1 flex items-center">
                    <input type="file" name="receipt" id="receipt" 
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100">
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Upload a new receipt to replace the current one (optional)
                </p>
                @error('receipt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Update Maintenance Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 