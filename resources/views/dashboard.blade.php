@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Dashboard</p>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">Welcome to the CMS workspace</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Use the quick actions below to manage access and bring new sites into the system. This dashboard is the landing page for admins after sign in.
            </p>
        </div>

        @php
            $user = auth()->user();
        @endphp

        @if ($user?->role === 'admin')
            <div class="grid gap-6 lg:grid-cols-2">
                <a
                    href="{{ route('users.create') }}"
                    class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Access</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">Manage users</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Create users, assign roles, and trigger password setup emails from one place.
                    </p>
                </a>

                <a
                    href="{{ route('sites.create') }}"
                    class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Sites</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">Create a new site</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Add a site, generate its slug, and prepare it for future assignments, content, and publishing workflows.
                    </p>
                </a>
            </div>
        @endif
    </div>
@endsection
