<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 150px; margin-bottom: 10px; }
        .receipt-info { margin: 20px 0; }
        .details { margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; font-size: 16px; margin-top: 20px; padding-top: 10px; border-top: 2px solid #000; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        @if($school->logo)
            @php
                $logoPath = storage_path('app/public/' . $school->logo);
                $logoUrl = file_exists($logoPath) ? 'file://' . $logoPath : '';
            @endphp
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="logo">
            @endif
        @endif
        <h1>{{ $school->name }}</h1>
        @if($school->support_address)
            <p>{{ $school->support_address }}</p>
        @endif
        @if($school->support_phone)
            <p>Phone: {{ $school->support_phone }}</p>
        @endif
        <h2>Fee Receipt</h2>
    </div>

    <div class="receipt-info">
        <div class="detail-row">
            <strong>Receipt No:</strong>
            <span>{{ $transaction->receipt_no ?: ('#' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT)) }}</span>
        </div>
        <div class="detail-row">
            <strong>Date:</strong>
            <span>{{ $transaction->payment_date->format('d M Y') }}</span>
        </div>
        <div class="detail-row">
            <strong>Academic Year:</strong>
            <span>{{ $ledger->academic_year }}</span>
        </div>
    </div>

    <div class="details">
        <h3>Student Information</h3>
        <div class="detail-row">
            <strong>Name:</strong>
            <span>{{ $student->name }}</span>
        </div>
        <div class="detail-row">
            <strong>Enrollment No:</strong>
            <span>{{ $student->enrollment_no }}</span>
        </div>
        <div class="detail-row">
            <strong>Class:</strong>
            <span>{{ $student->class }} @if($student->section) - {{ $student->section }} @endif</span>
        </div>
        @if($student->parent_phone)
            <div class="detail-row">
                <strong>Parent Phone:</strong>
                <span>{{ $student->parent_phone }}</span>
            </div>
        @endif
    </div>

    <div class="details">
        <h3>Previous Year Balance</h3>
        <div class="detail-row">
            <strong>Previous Year Balance (Opening):</strong>
            <span>₹{{ number_format($ledger->opening_balance, 2) }}</span>
        </div>
    </div>

    <div class="details">
        <h3>Payment Details</h3>
        <div class="detail-row">
            <strong>Paid This Receipt:</strong>
            <span>₹{{ number_format($transaction->paid_amount, 2) }}</span>
        </div>
        <div class="detail-row">
            <strong>Total Paid Till Now:</strong>
            <span>₹{{ number_format($totalPaid, 2) }}</span>
        </div>
        <div class="detail-row">
            <strong>Remaining Balance:</strong>
            <span>₹{{ number_format($remaining, 2) }}</span>
        </div>
        @if($transaction->payment_method)
            <div class="detail-row">
                <strong>Payment Method:</strong>
                <span>{{ $transaction->payment_method }}</span>
            </div>
        @endif
        @if($transaction->transaction_id)
            <div class="detail-row">
                <strong>Transaction ID:</strong>
                <span>{{ $transaction->transaction_id }}</span>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This is a computer-generated receipt. No signature required.</p>
    </div>
</body>
</html>

