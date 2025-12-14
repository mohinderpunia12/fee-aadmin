<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student ID Card</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .card { width: 350px; height: 220px; border: 2px solid #000; padding: 15px; margin: 0 auto; position: relative; }
        .header { text-align: center; margin-bottom: 15px; }
        .logo { max-width: 80px; max-height: 60px; }
        .school-name { font-weight: bold; font-size: 16px; margin: 5px 0; }
        .content { display: flex; }
        .photo { width: 80px; height: 100px; border: 1px solid #ccc; margin-right: 15px; text-align: center; line-height: 100px; background: #f0f0f0; }
        .photo img { max-width: 80px; max-height: 100px; }
        .info { flex: 1; }
        .info-row { margin: 5px 0; font-size: 12px; }
        .info-label { font-weight: bold; }
        .footer { text-align: center; margin-top: 10px; font-size: 10px; color: #666; }
        .qr-code { position: absolute; bottom: 10px; right: 10px; width: 50px; height: 50px; border: 1px solid #ccc; text-align: center; line-height: 50px; font-size: 8px; }
    </style>
</head>
<body>
    <div class="card">
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
            <div class="school-name">{{ $school->name }}</div>
        </div>
        <div class="content">
            <div class="photo">
                @if($student->profile_photo)
                    @php
                        $photoPath = storage_path('app/public/' . $student->profile_photo);
                        $photoUrl = file_exists($photoPath) ? 'file://' . $photoPath : '';
                    @endphp
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Photo">
                    @else
                        Photo
                    @endif
                @else
                    Photo
                @endif
            </div>
            <div class="info">
                <div class="info-row">
                    <span class="info-label">Name:</span> {{ $student->name }}
                </div>
                <div class="info-row">
                    <span class="info-label">Enrollment:</span> {{ $student->enrollment_no }}
                </div>
                <div class="info-row">
                    <span class="info-label">Class:</span> {{ $student->class }} @if($student->section) - {{ $student->section }} @endif
                </div>
                @if($student->classroom)
                    <div class="info-row">
                        <span class="info-label">Room:</span> {{ $student->classroom->name }}
                    </div>
                @endif
            </div>
        </div>
        <div class="qr-code">QR</div>
        <div class="footer">
            Valid for Academic Year {{ date('Y') }}-{{ date('Y', strtotime('+1 year')) }}
        </div>
    </div>
</body>
</html>
