@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">Reports</h1>
                    <a href="{{ route('admin.reports.generate') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Generate New Report
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="font-bold">Total Equipment</h2>
                        <p class="text-2xl">{{ $equipmentCount }}</p>
                    </div>
                    
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="font-bold">Total Bookings</h2>
                        <p class="text-2xl">{{ $bookingCount }}</p>
                    </div>
                    
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="font-bold">Maintenance Records</h2>
                        <p class="text-2xl">{{ $maintenanceCount }}</p>
                    </div>
                </div>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Available Reports
                        </h3>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    Equipment Usage Report
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <form action="{{ route('admin.reports.reports.generate') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="report_type" value="equipment-usage">
                                        <input type="hidden" name="format" value="pdf">
                                        <input type="hidden" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                                        <button type="submit" class="text-blue-600 hover:text-blue-900">
                                            Generate Report
                                        </button>
                                    </form>
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    Booking Statistics
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <form action="{{ route('admin.reports.generate') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="report_type" value="booking-statistics">
                                        <input type="hidden" name="format" value="pdf">
                                        <input type="hidden" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                                        <button type="submit" class="text-blue-600 hover:text-blue-900">
                                            Generate Report
                                        </button>
                                    </form>
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    Maintenance Report
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <form action="{{ route('admin.reports.generate') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="report_type" value="condition-history">
                                        <input type="hidden" name="format" value="pdf">
                                        <input type="hidden" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                                        <button type="submit" class="text-blue-600 hover:text-blue-900">
                                            Generate Report
                                        </button>
                                    </form>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <form action="{{ route('admin.reports.generate') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-500 p-4 rounded-md mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                            <select name="report_type" id="report_type" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="equipment-usage">Equipment Usage</option>
                                <option value="condition-history">Condition History</option>
                                <option value="availability">Availability</option>
                                <option value="booking-statistics">Booking Statistics</option>
                            </select>
                        </div>

                        <div>
                            <label for="format" class="block text-sm font-medium text-gray-700">Format</label>
                            <select name="format" id="format" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Prevent accidental GET requests
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.closest('form').method.toLowerCase() !== 'post') {
                e.preventDefault();
                console.error('Form method must be POST');
            }
        });
    });
</script>
@endpush 