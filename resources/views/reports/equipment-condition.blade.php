<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Equipment Condition Report') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Reports
                </a>
                <button onclick="exportToExcel()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Export to Excel
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Condition Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <div class="text-sm font-medium text-gray-500">New</div>
                                <div class="mt-1 text-2xl font-semibold text-green-600">
                                    {{ $equipment->where('condition', 'new')->sum('total') }}
                                </div>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <div class="text-sm font-medium text-gray-500">Good</div>
                                <div class="mt-1 text-2xl font-semibold text-blue-600">
                                    {{ $equipment->where('condition', 'good')->sum('total') }}
                                </div>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <div class="text-sm font-medium text-gray-500">Fair</div>
                                <div class="mt-1 text-2xl font-semibold text-yellow-600">
                                    {{ $equipment->where('condition', 'fair')->sum('total') }}
                                </div>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <div class="text-sm font-medium text-gray-500">Poor</div>
                                <div class="mt-1 text-2xl font-semibold text-red-600">
                                    {{ $equipment->where('condition', 'poor')->sum('total') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Condition Chart -->
                    <div class="mb-6">
                        <canvas id="conditionChart"></canvas>
                    </div>

                    <!-- Condition Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Condition
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Available
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        In Use
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Maintenance
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unavailable
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($equipment as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $item->condition === 'new' ? 'bg-green-100 text-green-800' : 
                                                   ($item->condition === 'good' ? 'bg-blue-100 text-blue-800' : 
                                                   ($item->condition === 'fair' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($item->condition) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->total }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-green-600">{{ $item->available }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-blue-600">{{ $item->in_use }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-yellow-600">{{ $item->maintenance }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-red-600">{{ $item->unavailable }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Category Statistics -->
                    <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Condition by Category</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        New
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Good
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fair
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Poor
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($categoryStats as $stat)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $stat->category->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $stat->total }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-green-600">{{ $stat->new }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-blue-600">{{ $stat->good }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-yellow-600">{{ $stat->fair }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-red-600">{{ $stat->poor }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Condition Chart
        const ctx = document.getElementById('conditionChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['New', 'Good', 'Fair', 'Poor'],
                datasets: [{
                    data: [
                        {{ $equipment->where('condition', 'new')->sum('total') }},
                        {{ $equipment->where('condition', 'good')->sum('total') }},
                        {{ $equipment->where('condition', 'fair')->sum('total') }},
                        {{ $equipment->where('condition', 'poor')->sum('total') }}
                    ],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(234, 179, 8)',
                        'rgb(239, 68, 68)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Equipment Condition Distribution'
                    }
                }
            }
        });

        function exportToExcel() {
            window.location.href = "{{ route('reports.export-equipment-condition') }}";
        }
    </script>
    @endpush
</x-app-layout> 