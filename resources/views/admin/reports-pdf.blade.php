<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ORDON Reports</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; }
        h1, h2 { color: #0b3650; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        th, td { border: 1px solid #d1e5f3; padding: 8px; font-size: 12px; text-align: left; }
        th { background: #eff8ff; }
        .meta { font-size: 12px; color: #4b5563; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>ORDON Reports Summary</h1>
    <p class="meta">Generated on {{ now()->format('d M Y, h:i A') }}</p>

    <table>
        <tr><th>Metric</th><th>Value</th></tr>
        <tr><td>Total Transplants</td><td>{{ $totalTransplants }}</td></tr>
        <tr><td>Completed Transplants</td><td>{{ $completedTransplants }}</td></tr>
        <tr><td>Approval Rate</td><td>{{ $approvalRate }}%</td></tr>
        <tr><td>Open Issue Reports</td><td>{{ $openIssueReports }}</td></tr>
        <tr><td>Resolved Issue Reports</td><td>{{ $resolvedIssueReports }}</td></tr>
    </table>

    <h2>Recipient Queue by Organ Type</h2>
    <table>
        <tr><th>Organ Type</th><th>Count</th></tr>
        @foreach ($queueByOrgan as $row)
            <tr>
                <td>{{ $row->organ_needed }}</td>
                <td>{{ $row->total }}</td>
            </tr>
        @endforeach
    </table>

    <h2>Recent Issue Reports</h2>
    <table>
        <tr><th>User</th><th>Role</th><th>Scope</th><th>Subject</th><th>Message</th><th>Status</th></tr>
        @foreach ($issueReports as $report)
            <tr>
                <td>{{ $report->user->name ?? 'User' }}</td>
                <td>{{ $report->role }}</td>
                <td>{{ $report->scope }}</td>
                <td>{{ $report->subject }}</td>
                <td>{{ $report->message }}</td>
                <td>{{ $report->status }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
