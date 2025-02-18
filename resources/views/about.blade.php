@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-info-circle"></i> About
    </h1>

    <div class="card mb-4">
        <div class="card-body">
            <!-- App Information -->
            <div class="mb-3">
                <strong>Application Name:</strong>
                <p>Issuing Asset System (IAS)</p>
            </div>

            <div class="mb-3">
                <strong>Application Version:</strong>
                <p>{{ $appVersion }}</p>
            </div>

            <!-- Plugins -->
            <div class="mb-3">
                <strong>Plugins Used:</strong>
                <ul>
                    @foreach ($plugins as $plugin => $version)
                        <li>{{ $plugin }} - Version {{ $version }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Plugins -->
            <div class="mb-3">
                <strong>Programming Language Used:</strong>
                <ul>
                    @foreach ($programmingLanguages as $programmingLanguage => $version)
                        @if ($programmingLanguage === 'PHP')
                            <li>{{ $programmingLanguage }} - Version {{ $version }}</li>
                        @else
                        <li>{{ $programmingLanguage }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <!-- Developer Credits -->
            <div class="mb-3">
                <strong>Developer Information:</strong>
                <p>Developer - <i>{{ $developerName }}</i></p>
                <p>Supervisor - <i>Francis Mendoza</i></p>
            </div>
        </div>
    </div>
</div>
@endsection
