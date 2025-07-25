<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Print Asset Details</title>
    <style>
        @page {
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            font: size 10px;
            line-height: 1.3;
        }
        .bold {
            font-weight: bold;
        }
        .underline {
            text-decoration: underline;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .left {
            text-align: left;
        }
        .section-break {
            margin-top: 20px;
        }
        .table-borderless {
            border-collapse: collapse;
            width: 100%;
        }
        .table-borderless td {
            padding: 5px;
        }
        hr {
            border: none;
            border-top: 1px solid black;
            margin: 20px 0;
        }
        .signature-line {
            margin-top: 40px;
        }
        /* Custom styles for the Asset Information table */
        td:first-child, th:first-child {
        width: 28%;
        }
        td:nth-child(2), th:nth-child(2) {
            width: 25%;
        }
        td:nth-child(3), th:nth-child(3) {
            width: 25%;
        }
        td:last-child, th:last-child {
        width: 22%;
        }
        /* Custom styles for the User Information table */
        .user-info-table td:first-child {
            width: 50%;
        }
        .user-info-table td:last-child {
            width: 50%; 
        }
    </style>
</head>
<body>

    <div class="right">
        Document No: _____________________
    </div>

    <div class="section-break center bold">AMERICAN UNIVERSITY OF PHNOM PENH</div>
    <div class="section-break center underline">EVENT ISSUED DEVICE FORM</div>

    <br>

    <div class="underline bold">User Information</div>

    <!-- User Information Table with adjusted column widths -->
    <table class="table-borderless user-info-table">
        <tr>
            <td><span class="bold">Issued to:&nbsp;</span> <span class="underline">&nbsp;&nbsp;{{ $user->name }}&nbsp;&nbsp;</span></td>
            <td><span class="bold">Position:&nbsp;</span> <span class="underline">&nbsp;&nbsp;{{ $user->position->name }}&nbsp;&nbsp;</span></td>
        </tr>
        <tr>
            <td><span class="bold">Email:&nbsp;</span> <span class="underline">&nbsp;&nbsp;{{ $user->email }}&nbsp;&nbsp;</span></td>
            <td><span class="bold">Contact No:&nbsp;</span> <span class="underline">&nbsp;&nbsp;{{ $user->contact_number }}&nbsp;&nbsp;</span></td>
        </tr>
        <tr>
            <td><span class="bold">Checkout:&nbsp;</span> <span class="underline">&nbsp;&nbsp;{{ $todayDate }}&nbsp;&nbsp;</span></td>
            <td><span class="bold">Checkin: _____________________ &nbsp;</span> 
        </tr>
    </table>

    <br>

    <div class="underline bold">Asset Information</div>
    <br>
    @if($userAssets->isEmpty())
        <p>No assets assigned.</p>
    @else
        <table class="table-borderless">
            <tr>
                <th class="left">Asset Tag</th>
                <th class="left">Device Type</th>
                <th class="left">Check-out Date</th>
                <th class="left">Check-In Date</th>
            </tr>
            @foreach($userAssets as $key => $asset)
                <tr>
                    <td>{{ $key + 1 }}. {{ $asset->asset_tag }}&nbsp;&nbsp;</td>
                    <td>{{ $asset->deviceType->name }}&nbsp;&nbsp;</&nbsp;&nbsp;></td>
                    <td>
                        @if($asset->latestIssueLog)
                            {{ \Carbon\Carbon::parse($asset->latestIssueLog->created_at)->format('d-M-Y') }}
                        @else
                            ______________
                        @endif
                    </td>
                    <td>______________</td>
                </tr>
            @endforeach
        </table>
    @endif

    <!-- Add Blank Rows for Additional Devices -->
    <table class="table-borderless">
        @php
            $remainingRows = 10 - $userAssets->count();  // Calculate the number of blank rows to add
        @endphp

        @for ($i = 0; $i < $remainingRows; $i++)
            <tr>
                <td>{{ $userAssets->count() + $i + 1 }}. ______________</td>
                <td>_______________</td>
                <td>_______________</td>
                <td>______________</td>
            </tr>
        @endfor
    </table>

    <br>

    <div class="underline bold">Agreement</div>

    <p>
        &nbsp;&nbsp;&nbsp;&nbsp;By signing below, I hereby acknowledge that by accepting the asset(s) listed above, I agree that they were tested by the IT department and verified to be working properly and without any damages. 
        I will take full responsibility for any loss or damage that may occur. <br>
        &nbsp;&nbsp;&nbsp;&nbsp;I agree to return the said asset(s) in good working condition, and if I fail to do so, I will pay the assessed value of the asset(s).
    </p>

    <div class="signature-line">
        <table class="table-borderless">
            <tr>
                <td><span class="bold">Checked by IT Department</span></td>
                <td><span class="bold">Checkout Staff/Faculty</span></td>
            </tr>
            <tr>
                <td>Name: <span class="underline">&nbsp;&nbsp;{{ $loggedInUser->name }}&nbsp;&nbsp;</span></td>
                <td>Name: <span class="underline">&nbsp;&nbsp;{{ $user->name }}&nbsp;&nbsp;</span></td>
            </tr>
            <tr>
                <td>Date: ______________________</td>
                <td>Date: ______________________</td>
            </tr>
            <tr>
                <td>Signature: ______________________</td>
                <td>Signature: ______________________</td>
            </tr>
        </table>
    </div>
</body>
</html>
