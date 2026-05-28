@extends('layouts.app')

@section('content')
    <div class="admin-layout">
        @include('components.navbar')

        <main class="admin-layout__content">
            @yield('admin_content')
        </main>
    </div>
@endsection
