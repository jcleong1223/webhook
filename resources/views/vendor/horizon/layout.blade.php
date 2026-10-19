<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAipJREFUeNrEV8txwjAQtQ2HHCmB3JKbSQOYCoA0gD0pgFBBwpEToQAGKmDglpwgFdg5kZtNB1BBsuusZ4RY2ZZjYGd2jGWh97Q/rUwjpziPT3V4dECboDZoXZoSka5Al5vFNMqzrpkD2IFHn8B1ZAM6BCKbQgQAuAaPWQFgjoinsoipAEcTr0FrRjmyJxLLTAI5wXFXAehBGMPYcDKIIIm5kkAGOJpwAjqHRfYpbkOXvTBBypIwpT+HCvA3Cqi9Rta8EhHOHS1YCy1oWMKHmQIcGQ90wGMfLaZIoEGAoiDGOHmxhFTr5PGZJgncZYszEGC6ogX6nNn/Ay6RGDCfYveYVOFCJuAaumbPiIk1kyUNS2H6SZngyZrMWM+i/JVlXjK4QUVI3pRTpYPlaG6yeyGvm0Jef1ItiArwQBKu8G5bTMEIhKLkU3q65D+HgieE7+MCBHbygMVMOlCK+CnVDOUZ5s00ghCt2T45C+DDD2MBW/O066YFLYGvuXU5C9i6GYaLUzqr+olQtS5aIMwwtW6QfQnv7awNVanolEWgo9nABBb1cNeSmMDyigRWZkqdPrdEkDm3SRYMr7D7odwRXdIK8e7lOuAxh8W5pHtSiOhw8S4A7iX9IErlyC5b/7t+/7Ar4TKiEuyyRuJA5cQ5Wz8gEhgPNyXvfCQPVtgI+SPxAT/vSqiSEbXh70Uvp27GRSMNeJjV2Jp5V6MGpUeuUR0wAemKuwdy8ivAAJcc0R2NFxWtAAAAAElFTkSuQmCC">

    <title>Horizon{{ config('horizon.name') ? ' - ' . config('horizon.name') : '' }}</title>

    <!-- Style sheets-->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600" rel="stylesheet" />
    {{ Laravel\Horizon\Horizon::css() }}
    <link rel="stylesheet" href="{{ asset('admin/css/horizon-theme.css') }}">
    {{ Laravel\Horizon\Horizon::js() }}
</head>
<body>
<div id="horizon" v-cloak>
    <alert :message="alert.message"
           :type="alert.type"
           :auto-close="alert.autoClose"
           :confirmation-proceed="alert.confirmationProceed"
           :confirmation-cancel="alert.confirmationCancel"
           v-if="alert.type"></alert>

    <div class="container mb-5">
        <div class="d-flex align-items-center py-4 header">
            <router-link to="/" class="logo d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
                    <path class="" fill="red" d="M5.26176342 26.4094389C2.04147988 23.6582233 0 19.5675182 0 15c0-4.1421356 1.67893219-7.89213562 4.39339828-10.60660172C7.10786438 1.67893219 10.8578644 0 15 0c8.2842712 0 15 6.71572875 15 15 0 8.2842712-6.7157288 15-15 15-3.716753 0-7.11777662-1.3517984-9.73823658-3.5905611zM4.03811305 15.9222506C5.70084247 14.4569342 6.87195416 12.5 10 12.5c5 0 5 5 10 5 3.1280454 0 4.2991572-1.9569336 5.961887-3.4222502C25.4934253 8.43417206 20.7645408 4 15 4 8.92486775 4 4 8.92486775 4 15c0 .3105915.01287248.6181765.03811305.9222506z"/>
                </svg>

                <h1 class="h4 mb-0 ms-2">
                    <strong>{{  config('horizon.name') }}</strong>
                </h1>
            </router-link>

            <div class="ms-auto">
                <scheme-toggler></scheme-toggler>

                <button class="btn btn-muted ms-2" :class="{active: autoLoadsNewEntries}" v-on:click.prevent="autoLoadNewEntries" title="Auto Load New Entries">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon" fill="currentColor">
                        <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <router-link active-class="active" to="/dashboard" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v2.5A2.25 2.25 0 004.25 9h2.5A2.25 2.25 0 009 6.75v-2.5A2.25 2.25 0 006.75 2h-2.5zm0 9A2.25 2.25 0 002 13.25v2.5A2.25 2.25 0 004.25 18h2.5A2.25 2.25 0 009 15.75v-2.5A2.25 2.25 0 006.75 11h-2.5zm9-9A2.25 2.25 0 0011 4.25v2.5A2.25 2.25 0 0013.25 9h2.5A2.25 2.25 0 0018 6.75v-2.5A2.25 2.25 0 0015.75 2h-2.5zm0 9A2.25 2.25 0 0011 13.25v2.5A2.25 2.25 0 0013.25 18h2.5A2.25 2.25 0 0018 15.75v-2.5A2.25 2.25 0 0015.75 11h-2.5z" clip-rule="evenodd" />
                            </svg>
                            <span>Dashboard</span>
                        </router-link>
                    </li>

                    @canany(['monitoring.view', 'monitoring.view-any'])
                    <li class="nav-item">
                        <router-link active-class="active" to="/monitoring" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                            <span>Monitoring</span>
                        </router-link>
                    </li>
                    @endcanany

                    @canany(['metric.view', 'metric.view-any'])
                    <li class="nav-item">
                        <router-link active-class="active" to="/metrics" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M15.5 2A1.5 1.5 0 0014 3.5v13a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-1zM9.5 6A1.5 1.5 0 008 7.5v9A1.5 1.5 0 009.5 18h1a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 6h-1zM3.5 10A1.5 1.5 0 002 11.5v5A1.5 1.5 0 003.5 18h1A1.5 1.5 0 006 16.5v-5A1.5 1.5 0 004.5 10h-1z" />
                            </svg>
                            <span>Metrics</span>
                        </router-link>
                    </li>
                    @endcanany

                    @canany(['batch.view', 'batch.view-any'])
                    <li class="nav-item">
                        <router-link active-class="active" to="/batches" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2 3.75A.75.75 0 012.75 3h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 3.75zm0 4.167a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75zm0 4.166a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75zm0 4.167a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span>Batches</span>
                        </router-link>
                    </li>
                    @endcanany

                    @canany(['pending-job.view', 'pending-job.view-any'])
                    <li class="nav-item">
                        <router-link active-class="active" to="/jobs/pending" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm5-2.25A.75.75 0 017.75 7h.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-.5a.75.75 0 01-.75-.75v-4.5zm4 0a.75.75 0 01.75-.75h.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-.5a.75.75 0 01-.75-.75v-4.5z" clip-rule="evenodd" />
                            </svg>
                            <span>Pending Jobs</span>
                        </router-link>
                    </li>
                    @endcanany

                    @canany(['completed-job.view', 'completed-job.view-any'])
                    <li class="nav-item">
                        <router-link active-class="active" to="/jobs/completed" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <span>Completed Jobs</span>
                        </router-link>
                    </li>
                    @endcanany

                    @canany(['silenced-job.view', 'silenced-job.view-any'])
                    <li class="nav-item">
                        <router-link active-class="active" to="/jobs/silenced" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M4 8c0-.26.017-.517.049-.77l7.722 7.723a33.56 33.56 0 01-3.722-.01 2 2 0 003.862.15l1.134 1.134a3.5 3.5 0 01-6.53-1.409 32.91 32.91 0 01-3.257-.508.75.75 0 01-.515-1.076A11.448 11.448 0 004 8zM17.266 13.9a.756.756 0 01-.068.116L6.389 3.207A6 6 0 0116 8c.001 1.887.455 3.665 1.258 5.234a.75.75 0 01.01.666zM3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06L3.28 2.22z" />
                            </svg>
                            <span>Silenced Jobs</span>
                        </router-link>
                    </li>
                    @endcanany

                    @canany(['failed-job.view', 'failed-job.view-any'])
                    <li class="nav-item">
                        <router-link active-class="active" to="/failed" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            <span>Failed Jobs</span>
                        </router-link>
                    </li>
                    @endcanany


                    <li class="nav-item mt-4">
                        <span class="nav-link px-0 text-uppercase small fw-semibold text-muted">Report</span>
                    </li>
                    @canany(['api-client.view', 'api-client.view-any'])
                    <li class="nav-item">
                        <a href="{{ route('admin.api-client.index') }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span>API Client</span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['webhook-event.view', 'webhook-event.view-any'])
                    <li class="nav-item">
                        <a href="{{ route('admin.webhook-event.index') }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" class="me-10">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.57l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.57l7-10a1 1 0 011.12-.384z" clip-rule="evenodd" />
                            </svg>
                            <span>Webhook Event</span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['webhook-delivery.view', 'webhook-delivery.view-any'])
                    <li class="nav-item">
                        <a href="{{ route('admin.webhook-delivery.index') }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 512 512" fill="currentColor" class="me-10 flex-shrink-0" aria-hidden="true">
                                <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4l217.6 163.2c11.4 8.5 27 8.5 38.4 0l217.6-163.2c12.1-9.1 19.2-23.3 19.2-38.4 0-26.5-21.5-48-48-48H48zM0 176v224c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V176L294.4 338.4c-22.8 17.1-54 17.1-76.8 0L0 176z"/>
                            </svg>
                            <span>Webhook Delivery</span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['endpoint-health.view', 'endpoint-health.view-any'])
                    <li class="nav-item">
                        <a href="{{ route('admin.endpoint-health.index') }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 576 512"
                                width="1em"
                                height="1em"
                                fill="currentColor"
                                class="me-10 fa-fw"
                                aria-hidden="true"
                            >
                                <path d="M142.4 21.9c5.6 16.8-3.5 34.9-20.2 40.5L96 71.1 96 192c0 53 43 96 96 96s96-43 96-96l0-120.9-26.1-8.7c-16.8-5.6-25.8-23.7-20.2-40.5s23.7-25.8 40.5-20.2l26.1 8.7C334.4 19.1 352 43.5 352 71.1L352 192c0 77.2-54.6 141.6-127.3 156.7C231 404.6 278.4 448 336 448c61.9 0 112-50.1 112-112l0-70.7c-28.3-12.3-48-40.5-48-73.3c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3l0 70.7c0 97.2-78.8 176-176 176c-92.9 0-168.9-71.9-175.5-163.1C87.2 334.2 32 269.6 32 192L32 71.1c0-27.5 17.6-52 43.8-60.7l26.1-8.7c16.8-5.6 34.9 3.5 40.5 20.2zM480 224a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/>
                            </svg>
                            <span>Endpoint Health</span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['delivery-attempt.view', 'delivery-attempt.view-any'])
                    <li class="nav-item">
                        <a href="{{ route('admin.delivery-attempt.index') }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 512 512"
                                fill="currentColor"
                                class="me-10 fa-fw"
                                aria-hidden="true"
                            >
                                <path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm24-328c0-13.3-10.7-24-24-24s-24 10.7-24 24v128c0 8 4 15.5 10.7 20l80 56c10.8 7.5 25.7 4.8 33.3-6s4.8-25.7-6-33.3L280 270.4V128z"/>
                            </svg>
                            <span>Attempt History</span>
                        </a>
                    </li>
                    @endcanany


                    <li class="nav-item mt-4">
                        <span class="nav-link px-0 text-uppercase small fw-semibold text-muted">Admin</span>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.user.index') }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="1em" height="1em" fill="currentColor" class="me-10 fa-fw" aria-hidden="true">
                                <path d="M96 128a80 80 0 1 0 0-160 80 80 0 1 0 0 160zm0 32C43 160 0 203 0 256v48c0 17.7 14.3 32 32 32h128c17.7 0 32-14.3 32-32v-48c0-53-43-96-96-96zm448-32a80 80 0 1 0 0-160 80 80 0 1 0 0 160zm-32 32c-53 0-96 43-96 96v48c0 17.7 14.3 32 32 32h128c17.7 0 32-14.3 32-32v-48c0-53-43-96-96-96zM320 256a96 96 0 1 0 0-192 96 96 0 1 0 0 192zm0 32c-70.7 0-128 57.3-128 128v32c0 17.7 14.3 32 32 32h192c17.7 0 32-14.3 32-32v-32c0-70.7-57.3-128-128-128z"/>
                            </svg>
                            <span>Users</span>
                        </a>
                    </li>

                    @canany(['role.view', 'role.view-any'])
                    <li class="nav-item">
                        <a href="{{ route('admin.role-permission.index') }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1em" height="1em" fill="currentColor" class="me-10 fa-fw" aria-hidden="true">
                                <path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 48c44.5 0 85.3 14.6 118.4 39.3L95.3 374.4C70.6 341.3 56 300.5 56 256 56 145.5 145.5 56 256 56zm0 400c-44.5 0-85.3-14.6-118.4-39.3L416.7 137.6C441.4 170.7 456 211.5 456 256c0 110.5-89.5 200-200 200z"/>
                            </svg>
                            <span>Roles & Permissions</span>
                        </a>
                    </li>
                    @endcanany

                    <li class="nav-item">
                        <a href="{{ route('admin.user.edit', Auth::user()->id) }}" class="nav-link d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor" class="me-10 fa-fw" aria-hidden="true">
                                <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3 0 498.7 13.3 512 29.7 512h388.6c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304h-91.4z"/>
                            </svg>
                            <span>Profile</span>
                        </a>
                    </li>

                    <li class="nav-item ms-3 mt-1">
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="nav-link d-flex align-items-center w-100 border-0 bg-transparent text-start p-0 m-0 shadow-none font-inherit" style="cursor: pointer; color: inherit; font: inherit;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="#eb3525" stroke="#eb3525" stroke-width="1" viewBox="0 0 20 20" style="width: 1.25rem; height: 1.25rem;" class="me-2" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v1a.75.75 0 01-1.5 0v-1a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-1a.75.75 0 011.5 0v1A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M19.78 9.47a.75.75 0 010 1.06l-3 3a.75.75 0 11-1.06-1.06l1.72-1.72H7a.75.75 0 010-1.5h10.44l-1.72-1.72a.75.75 0 111.06-1.06l3 3z" clip-rule="evenodd" />
                                </svg>
                                <span class="ms-1 text-danger">Sign Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            <div class="col-10">
                @if ($isDownForMaintenance)
                    <div class="alert alert-warning">
                        This application is in "maintenance mode". Queued jobs may not be processed unless your worker is using the "force" flag.
                    </div>
                @endif

                <router-view></router-view>
            </div>
        </div>
    </div>
</div>
</body>
</html>
