<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 150px; margin-bottom: 10px; }
        .slip-info { margin: 20px 0; }
        .details { margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px; border-bottom: 1px solid #eee; }
        .section { margin: 20px 0; }
        .section-title { font-weight: bold; margin-bottom: 10px; }
        .total { font-weight: bold; font-size: 18px; margin-top: 20px; padding-top: 10px; border-top: 2px solid #000; }
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
        <h2>Salary Slip</h2>
    </div>

    <div class="slip-info">
        <div class="detail-row">
            <strong>Slip No:</strong>
            <span>#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="detail-row">
            <strong>Payment Date:</strong>
            <span>{{ $payment->payment_date->format('d M Y') }}</span>
        </div>
        @if($structure)
            <div class="detail-row">
                <strong>Period:</strong>
                <span>{{ \Carbon\Carbon::create()->month($structure->month)->format('F') }} {{ $structure->year }}</span>
            </div>
        @endif
    </div>

    <div class="details">
        <h3>Employee Information</h3>
        <div class="detail-row">
            <strong>Name:</strong>
            <span>{{ $payment->staff->name }}</span>
        </div>
        <div class="detail-row">
            <strong>Role:</strong>
            <span>{{ $payment->staff->role }}</span>
        </div>
        @if($payment->staff->badge_number)
            <div class="detail-row">
                <strong>Badge No:</strong>
                <span>{{ $payment->staff->badge_number }}</span>
            </div>
        @endif
    </div>

    @if($structure)
        <div class="section">
            <div class="section-title">Earnings</div>
            <div class="detail-row">
                <span>Base Salary:</span>
                <span>₹{{ number_format($structure->base_salary, 2) }}</span>
            </div>
            @if($structure->allowances)
                @foreach($structure->allowances as $type => $amount)
                    <div class="detail-row">
                        <span>{{ $type }}:</span>
                        <span>₹{{ number_format($amount, 2) }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="section">
            <div class="section-title">Deductions</div>
            @if($structure->deductions)
                @foreach($structure->deductions as $type => $amount)
                    <div class="detail-row">
                        <span>{{ $type }}:</span>
                        <span>₹{{ number_format($amount, 2) }}</span>
                    </div>
                @endforeach
            @else
                <div class="detail-row">
                    <span>No deductions</span>
                    <span>₹0.00</span>
                </div>
            @endif
        </div>

        <div class="total">
            @if(($payment->advance_salary ?? 0) > 0)
                <div class="detail-row">
                    <strong>Advance Salary:</strong>
                    <span>- ₹{{ number_format($payment->advance_salary, 2) }}</span>
                </div>
            @endif
            <div class="detail-row">
                <strong>Net Salary:</strong>
                <span>₹{{ number_format(max(0, ($structure->total_salary ?? 0) - ($payment->advance_salary ?? 0)), 2) }}</span>
            </div>
        </div>
    @else
        <div class="total">
            @if(($payment->advance_salary ?? 0) > 0)
                <div class="detail-row">
                    <strong>Advance Salary:</strong>
                    <span>- ₹{{ number_format($payment->advance_salary, 2) }}</span>
                </div>
            @endif
            <div class="detail-row">
                <strong>Amount Paid:</strong>
                <span>₹{{ number_format($payment->amount_paid, 2) }}</span>
            </div>
        </div>
    @endif

    @if($payment->payment_method)
        <div class="details">
            <div class="detail-row">
                <strong>Payment Method:</strong>
                <span>{{ $payment->payment_method }}</span>
            </div>
            @if($payment->transaction_id)
                <div class="detail-row">
                    <strong>Transaction ID:</strong>
                    <span>{{ $payment->transaction_id }}</span>
                </div>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated salary slip. No signature required.</p>
    </div>
</body>
</html>
