<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
    <title>Print Asset Details</title>
</head>
<body>

    <div class="right">
        Document No: _____________________
    </div>

    <div class="section-break center bold">AMERICAN UNIVERSITY OF PHNOM PENH</div>
    <div class="section-break center underline">ISSUED EVENT DEVICES FORM</div>

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

    <!-- Auto-trigger print -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
                window.print();
        });
    </script>

</body>
</html>
