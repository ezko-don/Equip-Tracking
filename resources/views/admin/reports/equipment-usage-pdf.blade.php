<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Equipment Usage Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .date-range {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Equipment Usage Report</h1>
        <div class="date-range">
            Period: {{ \Carbon\Carbon::parse($start_date)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('F d, Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Equipment Name</th>
                <th>Category</th>
                <th>Total Bookings</th>
                <th>Maintenance Count</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipment as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->bookings_count }}</td>
                <td>{{ $item->maintenanceLogs->count() }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ now()->format('F d, Y H:i:s') }}
    </div>
</body>
</html> 