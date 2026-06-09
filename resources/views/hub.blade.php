@extends('layouts.app')

@section('content')
    <div x-data="{
                showAddModal: {{ $errors->has('name') || $errors->has('url') || $errors->has('icon_url') ? 'true' : 'false' }},
                showEditModal: {{ $errors->has('edit_name') || $errors->has('edit_url') || $errors->has('edit_icon_url') || $errors->has('edit_icon_file') ? 'true' : 'false' }},
                showSettingsModal: {{ $errors->has('company_name') || $errors->has('company_logo') || $errors->has('company_logo_file') || $errors->has('admin_email') ? 'true' : 'false' }},
                editingWebsite: {
                    id: '{{ old('edit_id') }}',
                    name: '{{ addslashes(old('edit_name')) }}',
                    url: '{{ addslashes(old('edit_url')) }}',
                    icon_url: '{{ addslashes(old('edit_icon_url')) }}'
                },
                showToast: false,
                toastMessage: '',
                toastType: 'success',
            }" x-init="
                @if(session('success'))
                    toastMessage = '{{ session('success') }}';
                    toastType = 'success';
                    showToast = true;
                    setTimeout(() => showToast = false, 3500);
                @endif
                @if($errors->any())
                    toastMessage = '{{ $errors->first() }}';
                    toastType = 'error';
                    showToast = true;
                    setTimeout(() => showToast = false, 4500);
                @endif
            " class="relative min-h-screen overflow-hidden">

        {{-- ═══════════════════════════════════════════════
        MESH GRADIENT BACKGROUND + FLOATING ORBS
        ═══════════════════════════════════════════════ --}}
        <div class="fixed inset-0 -z-20 mesh-bg"></div>

        {{-- Living orbs (RingNet green + red ambient blobs) --}}
        <div class="fixed -z-10 w-[600px] h-[600px] rounded-full animate-orb opacity-35 blur-3xl"
            style="top: -15%; left: -10%; background: radial-gradient(circle, rgba(46, 125, 50, 0.45), transparent 70%);">
        </div>
        <div class="fixed -z-10 w-[700px] h-[700px] rounded-full animate-orb opacity-25 blur-3xl"
            style="bottom: -20%; right: -10%; background: radial-gradient(circle, rgba(229, 57, 53, 0.35), transparent 70%); animation-delay: -3s;">
        </div>
        <div class="fixed -z-10 w-[400px] h-[400px] rounded-full animate-orb opacity-20 blur-3xl"
            style="top: 35%; right: 15%; background: radial-gradient(circle, rgba(76, 175, 80, 0.35), transparent 70%); animation-delay: -6s;">
        </div>
        <div class="fixed -z-10 w-[350px] h-[350px] rounded-full animate-mesh opacity-20 blur-3xl"
            style="bottom: 10%; left: 20%; background: radial-gradient(circle, rgba(239, 83, 80, 0.3), transparent 70%);">
        </div>

        {{-- ═══════════════════════════════════════════════
        TOP HEADER BAR (Fixed)
        ═══════════════════════════════════════════════ --}}
        <header class="fixed top-0 left-0 right-0 z-40 animate-fade-in">
            <div class="bg-white/35 backdrop-blur-2xl border-t-0 border-l-0 border-r-0 rounded-none shadow-sm"
                style="border-bottom: 1.5px solid rgba(255,255,255,0.45);">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16 sm:h-20">

                        {{-- Left: Logo + Company Name --}}
                        <a href="{{ route('hub.index') }}"
                            class="flex items-center gap-3 sm:gap-4 hover:opacity-90 active:scale-98 transition-all duration-200">
                            @if($companyLogo)
                                <div class="w-14 h-14 sm:w-20 sm:h-20 flex-shrink-0 flex items-center justify-center">
                                    <img src="{{ $companyLogo }}" alt="{{ $companyName }}"
                                        class="max-w-full max-h-full object-contain"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="hidden items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl
                                                             text-lg font-bold text-white brand-gradient shadow-md">
                                        {{ strtoupper(substr($companyName, 0, 1)) }}
                                    </span>
                                </div>
                            @else
                                <div
                                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl overflow-hidden
                                                        shadow-md flex-shrink-0
                                                        flex items-center justify-center text-lg font-bold text-white brand-gradient">
                                    {{ strtoupper(substr($companyName, 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <h1
                                    class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight leading-none mb-0.5 sm:mb-1">
                                    <span class="brand-gradient-text">{{ $companyName }}</span>
                                </h1>
                            </div>
                        </a>

                        {{-- Right: Actions button --}}
                        @if($isAdmin)
                            <div class="flex items-center gap-2">
                                <a href="{{ route('hub.index') }}" class="flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl
                                                       glass-card cursor-pointer
                                                       transition-all duration-300
                                                       hover:scale-105 active:scale-95
                                                       group">
                                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px] text-gray-500 group-hover:text-gray-700 transition-colors duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span
                                        class="hidden sm:inline text-xs font-medium text-gray-500 group-hover:text-gray-700 transition-colors duration-300">Exit
                                        Admin</span>
                                </a>

                                <button @click="showSettingsModal = true" id="btn-settings" class="flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl
                                                       glass-card cursor-pointer
                                                       transition-all duration-300
                                                       hover:scale-105 active:scale-95
                                                       group">
                                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px] text-gray-500 group-hover:text-gray-700 transition-all duration-300 group-hover:rotate-90"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.431.992a7.723 7.723 0 010 .255c-.007.378.138.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span
                                        class="hidden sm:inline text-xs font-medium text-gray-500 group-hover:text-gray-700 transition-colors duration-300">Settings</span>
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </header>

        {{-- ═══════════════════════════════════════════════
        MAIN CONTENT CONTAINER
        ═══════════════════════════════════════════════ --}}
        <div class="flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 pt-20 pb-12 sm:pb-16 min-h-screen">

            {{-- ── GLASS CONTAINER ── --}}
            <div class="w-full max-w-xs sm:max-w-xl md:max-w-3xl pt-10 lg:max-w-4xl xl:max-w-5xl animate-fade-in">

                {{-- ═══════════════════════════════════
                GLASS PANEL — WEBSITE CARDS
                ═══════════════════════════════════ --}}
                <div class="glass-panel rounded-[28px] sm:rounded-[32px] p-5 sm:p-8 md:p-10">

                    <div class="card-grid">

                        {{-- ── Website Cards ── --}}
                        @foreach($websites as $index => $website)
                            <div class="w-full animate-slide-in stagger-{{ ($index % 12) + 1 }} group">
                                <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer"
                                    id="card-{{ $website->id }}" class="relative flex flex-col items-center justify-center
                                                  aspect-square max-w-[160px] w-full mx-auto
                                                  rounded-[22px] sm:rounded-[26px]
                                                  glass-card p-3 sm:p-4
                                                  transition-all duration-300 ease-out
                                                  hover:scale-[1.06] hover:-translate-y-1.5
                                                  active:scale-[0.97]
                                                  cursor-pointer">

                                    {{-- Edit Button (visible on hover, desktop only) --}}
                                    @if($isAdmin)
                                        <button type="button" @click.prevent.stop="
                                                            editingWebsite = {
                                                                id: '{{ $website->id }}',
                                                                name: '{{ addslashes($website->name) }}',
                                                                url: '{{ addslashes($website->url) }}',
                                                                icon_url: '{{ addslashes($website->icon_url) }}'
                                                            };
                                                            showEditModal = true;
                                                        " class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2
                                                               opacity-0 group-hover:opacity-100
                                                               transition-all duration-200 z-10
                                                               flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full
                                                               bg-black/5 hover:bg-green-500/15 text-gray-400 hover:text-green-600
                                                               transition-all duration-200 backdrop-blur-sm">
                                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Delete × (visible on hover, desktop only) --}}
                                    @if($isAdmin)
                                        <form action="{{ route('websites.destroy', $website) }}" method="POST" class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2
                                                                 opacity-0 group-hover:opacity-100
                                                                 transition-all duration-200 z-10"
                                            onclick="event.stopPropagation();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="event.preventDefault(); event.stopPropagation(); if(confirm('Remove this website?')) this.closest('form').submit();"
                                                class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full
                                                                       bg-black/5 hover:bg-red-500/15 text-gray-400 hover:text-red-500
                                                                       transition-all duration-200 backdrop-blur-sm">
                                                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Icon container --}}
                                    <div class="flex items-center justify-center
                                                        w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16
                                                        rounded-[14px] sm:rounded-[18px] mb-2.5 sm:mb-3
                                                        bg-white/28 border border-white/35 backdrop-blur-md shadow-sm
                                                        group-hover:shadow-md group-hover:bg-white/40 group-hover:border-white/50
                                                        transition-all duration-300 overflow-hidden">
                                        @if($website->icon_url)
                                            <img src="{{ $website->icon_url }}" alt="{{ $website->name }}"
                                                class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 object-contain rounded-xl"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span class="hidden items-center justify-center
                                                                         w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10
                                                                         rounded-xl text-lg sm:text-xl font-bold text-white"
                                                style="background: linear-gradient(135deg, #2e7d32, #e53935);">
                                                {{ strtoupper(substr($website->name, 0, 1)) }}
                                            </span>
                                        @else
                                            <span class="flex items-center justify-center
                                                                         w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10
                                                                         rounded-xl text-lg sm:text-xl font-bold text-white"
                                                style="background: linear-gradient(135deg, #2e7d32, #e53935);">
                                                {{ strtoupper(substr($website->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Name --}}
                                    <span class="text-[11px] sm:text-xs md:text-sm font-medium text-gray-600
                                                         text-center leading-tight truncate w-full px-1
                                                         group-hover:text-gray-900 transition-colors duration-300">
                                        {{ $website->name }}
                                    </span>
                                </a>
                            </div>
                        @endforeach

                        {{-- ── ADD NEW CARD ── --}}
                        @if($isAdmin)
                            <div class="w-full animate-slide-in stagger-{{ min(($websites->count() % 12) + 1, 12) }}">
                                <button @click="showAddModal = true" id="btn-add-website" class="flex flex-col items-center justify-center
                                                       aspect-square max-w-[160px] w-full mx-auto
                                                       rounded-[22px] sm:rounded-[26px]
                                                       border-2 border-dashed border-gray-300/50
                                                       bg-white/20 backdrop-blur-sm
                                                       transition-all duration-300 ease-out
                                                       hover:border-green-500/50 hover:bg-white/40
                                                       hover:shadow-lg hover:shadow-green-100/30
                                                       hover:scale-[1.06] hover:-translate-y-1.5
                                                       active:scale-[0.97]
                                                       cursor-pointer group p-3 sm:p-4">
                                    <div class="flex items-center justify-center
                                                        w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16
                                                        rounded-[14px] sm:rounded-[18px] mb-2.5 sm:mb-3
                                                        bg-white/28 border border-white/35 backdrop-blur-md
                                                        group-hover:bg-green-500/20 group-hover:border-green-500/40
                                                        transition-all duration-300">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-400 group-hover:text-green-600 transition-colors duration-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] sm:text-xs md:text-sm font-medium text-gray-400
                                                         group-hover:text-green-600 transition-colors duration-300">
                                        Add New
                                    </span>
                                </button>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- ── Demo Registrations Panel (Admin Only) ── --}}
                @if($isAdmin)
                    <div class="mt-8 w-full animate-fade-in animate-delay-200">
                        <div class="glass-panel rounded-[28px] sm:rounded-[32px] p-6 sm:p-8">
                            <h3 class="text-sm sm:text-base font-bold text-gray-900/80 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Demo Accounts Registration Log
                            </h3>

                            @if($registrations->isEmpty())
                                <div class="text-center py-6 text-gray-400 font-light text-xs sm:text-sm">
                                    No demo registrations recorded yet.
                                </div>
                            @else
                                <div class="overflow-x-auto rounded-xl border border-gray-200/30">
                                    <table class="min-w-full divide-y divide-gray-200/20 text-left text-xs sm:text-sm">
                                        <thead
                                            class="bg-white/40 text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">Username</th>
                                                <th scope="col" class="px-4 py-3">Email</th>
                                                <th scope="col" class="px-4 py-3">Phone</th>
                                                <th scope="col" class="px-4 py-3">Website</th>
                                                <th scope="col" class="px-4 py-3">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200/10 text-gray-700">
                                            @foreach($registrations as $reg)
                                                <tr class="hover:bg-white/20 transition-colors duration-150">
                                                    <td class="px-4 py-3.5 font-medium text-gray-900">{{ $reg->username }}</td>
                                                    <td class="px-4 py-3.5 text-gray-600">{{ $reg->email }}</td>
                                                    <td class="px-4 py-3.5 text-gray-600">{{ $reg->phone }}</td>
                                                    <td class="px-4 py-3.5">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-green-500/10 text-green-700">
                                                            {{ $reg->site_name }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3.5 text-gray-400 text-[10px] sm:text-xs">
                                                        {{ $reg->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 px-1">
                                    {{ $registrations->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── Footer branding ── --}}
                <div class="text-center mt-6 sm:mt-8 animate-fade-in" style="animation-delay: 0.3s;">
                    <p class="text-[10px] sm:text-xs text-gray-400/60 font-light tracking-widest uppercase">
                        Powered by {{ $companyName }}
                    </p>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
        ADD WEBSITE MODAL
        ═══════════════════════════════════════════════ --}}
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            @keydown.escape.window="showAddModal = false" style="display: none;">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/10 backdrop-blur-md" @click="showAddModal = false"></div>

            {{-- Modal Panel --}}
            <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-[0.96] translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-[0.96] translate-y-3"
                class="relative w-full max-w-sm sm:max-w-md rounded-[28px] glass-modal p-6 sm:p-8">

                {{-- Close --}}
                <button @click="showAddModal = false" class="absolute top-3.5 right-3.5 sm:top-4 sm:right-4
                                   flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full
                                   bg-black/5 hover:bg-black/10 text-gray-400 hover:text-gray-600
                                   transition-all duration-200">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Header --}}
                <div class="text-center mb-5 sm:mb-6">
                    <div class="inline-flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 rounded-2xl mb-3 sm:mb-4
                                    shadow-lg shadow-green-200/40 brand-gradient">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900/90">Add New Website</h2>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500/70">Enter your demo website details</p>
                </div>

                {{-- Form --}}
                <form action="{{ route('websites.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-3.5 sm:space-y-4">
                    @csrf

                    <div>
                        <label for="website-name" class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">Website
                            Name</label>
                        <input type="text" id="website-name" name="name" required placeholder="e.g. My Portfolio"
                            value="{{ old('name') }}" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                          glass-input text-gray-900 placeholder:text-gray-400
                                          focus:outline-none transition-all duration-200 text-sm">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="website-url"
                            class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">URL</label>
                        <input type="url" id="website-url" name="url" required placeholder="https://example.com"
                            value="{{ old('url') }}" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                          glass-input text-gray-900 placeholder:text-gray-400
                                          focus:outline-none transition-all duration-200 text-sm">
                        @error('url')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website Icon (File or URL) --}}
                    <div x-data="{ iconType: 'file' }">
                        <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">Website Icon</label>

                        {{-- Toggle between File Upload and URL --}}
                        <div class="flex gap-2 mb-2">
                            <button type="button" @click="iconType = 'file'"
                                :class="iconType === 'file' ? 'bg-green-700 text-white shadow-sm' : 'bg-black/5 text-gray-600 hover:bg-black/10'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer">
                                Upload File
                            </button>
                            <button type="button" @click="iconType = 'url'"
                                :class="iconType === 'url' ? 'bg-green-700 text-white shadow-sm' : 'bg-black/5 text-gray-600 hover:bg-black/10'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer">
                                URL Link
                            </button>
                        </div>

                        {{-- File Input --}}
                        <div x-show="iconType === 'file'" class="animate-fade-in" style="animation-duration: 200ms;">
                            <input type="file" id="website-icon-file" name="icon_file" accept="image/*" class="w-full px-3.5 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl
                                              glass-input text-gray-900 text-sm focus:outline-none file:mr-4 file:py-1 file:px-3
                                              file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                              file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            @error('icon_file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- URL Input --}}
                        <div x-show="iconType === 'url'" class="animate-fade-in" style="animation-duration: 200ms;">
                            <input type="text" id="website-icon-url" name="icon_url"
                                placeholder="https://example.com/icon.png" value="{{ old('icon_url') }}" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                              glass-input text-gray-900 placeholder:text-gray-400
                                              focus:outline-none transition-all duration-200 text-sm">
                            @error('icon_url')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 sm:py-3 rounded-xl sm:rounded-2xl text-sm font-semibold text-white
                                       brand-btn
                                       active:scale-[0.98] transition-all duration-200">
                        Add Website
                    </button>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
        EDIT WEBSITE MODAL
        ═══════════════════════════════════════════════ --}}
        <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            @keydown.escape.window="showEditModal = false" style="display: none;">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/10 backdrop-blur-md" @click="showEditModal = false"></div>

            {{-- Modal Panel --}}
            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-[0.96] translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-[0.96] translate-y-3"
                class="relative w-full max-w-sm sm:max-w-md rounded-[28px] glass-modal p-6 sm:p-8">

                {{-- Close --}}
                <button @click="showEditModal = false" class="absolute top-3.5 right-3.5 sm:top-4 sm:right-4
                                   flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full
                                   bg-black/5 hover:bg-black/10 text-gray-400 hover:text-gray-600
                                   transition-all duration-200">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Header --}}
                <div class="text-center mb-5 sm:mb-6">
                    <div class="inline-flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 rounded-2xl mb-3 sm:mb-4
                                    shadow-lg shadow-green-200/40 brand-gradient">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900/90">Edit Website</h2>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500/70">Modify your demo website details</p>
                </div>

                {{-- Form --}}
                <form :action="'/websites/' + editingWebsite.id" method="POST" enctype="multipart/form-data"
                    class="space-y-3.5 sm:space-y-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="edit_id" :value="editingWebsite.id">

                    <div>
                        <label for="edit-website-name"
                            class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">Website Name</label>
                        <input type="text" id="edit-website-name" name="edit_name" required placeholder="e.g. My Portfolio"
                            x-model="editingWebsite.name" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                          glass-input text-gray-900 placeholder:text-gray-400
                                          focus:outline-none transition-all duration-200 text-sm">
                        @error('edit_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit-website-url"
                            class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">URL</label>
                        <input type="url" id="edit-website-url" name="edit_url" required placeholder="https://example.com"
                            x-model="editingWebsite.url" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                          glass-input text-gray-900 placeholder:text-gray-400
                                          focus:outline-none transition-all duration-200 text-sm">
                        @error('edit_url')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website Icon (File or URL) --}}
                    <div x-data="{ iconType: 'file' }"
                        x-init="$watch('editingWebsite', value => { iconType = value.icon_url && (value.icon_url.startsWith('http') || value.icon_url.startsWith('https')) ? 'url' : 'file' })">
                        <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">Website Icon</label>

                        {{-- Toggle between File Upload and URL --}}
                        <div class="flex gap-2 mb-2">
                            <button type="button" @click="iconType = 'file'"
                                :class="iconType === 'file' ? 'bg-green-700 text-white shadow-sm' : 'bg-black/5 text-gray-600 hover:bg-black/10'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer">
                                Upload File
                            </button>
                            <button type="button" @click="iconType = 'url'"
                                :class="iconType === 'url' ? 'bg-green-700 text-white shadow-sm' : 'bg-black/5 text-gray-600 hover:bg-black/10'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer">
                                URL Link
                            </button>
                        </div>

                        {{-- File Input --}}
                        <div x-show="iconType === 'file'" class="animate-fade-in" style="animation-duration: 200ms;">
                            <input type="file" id="edit-website-icon-file" name="edit_icon_file" accept="image/*" class="w-full px-3.5 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl
                                              glass-input text-gray-900 text-sm focus:outline-none file:mr-4 file:py-1 file:px-3
                                              file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                              file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            @error('edit_icon_file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- URL Input --}}
                        <div x-show="iconType === 'url'" class="animate-fade-in" style="animation-duration: 200ms;">
                            <input type="text" id="edit-website-icon-url" name="edit_icon_url"
                                placeholder="https://example.com/icon.png" x-model="editingWebsite.icon_url" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                              glass-input text-gray-900 placeholder:text-gray-400
                                              focus:outline-none transition-all duration-200 text-sm">
                            @error('edit_icon_url')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 sm:py-3 rounded-xl sm:rounded-2xl text-sm font-semibold text-white
                                       brand-btn
                                       active:scale-[0.98] transition-all duration-200">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
        SETTINGS MODAL
        ═══════════════════════════════════════════════ --}}
        <div x-show="showSettingsModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            @keydown.escape.window="showSettingsModal = false" style="display: none;">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/10 backdrop-blur-md" @click="showSettingsModal = false"></div>

            {{-- Modal Panel --}}
            <div x-show="showSettingsModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-[0.96] translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-[0.96] translate-y-3"
                class="relative w-full max-w-sm sm:max-w-md rounded-[28px] glass-modal p-6 sm:p-8">

                {{-- Close --}}
                <button @click="showSettingsModal = false" class="absolute top-3.5 right-3.5 sm:top-4 sm:right-4
                                   flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full
                                   bg-black/5 hover:bg-black/10 text-gray-400 hover:text-gray-600
                                   transition-all duration-200">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Header --}}
                <div class="text-center mb-5 sm:mb-6">
                    <div class="inline-flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 rounded-2xl mb-3 sm:mb-4
                                    bg-gradient-to-br from-gray-600 to-gray-800 shadow-lg shadow-gray-300/40">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.431.992a7.723 7.723 0 010 .255c-.007.378.138.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900/90">Settings</h2>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500/70">Configure your Hub preferences</p>
                </div>

                {{-- Form --}}
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-3.5 sm:space-y-4">
                    @csrf

                    {{-- Company Name --}}
                    <div>
                        <label for="setting-company-name"
                            class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">Company Name</label>
                        <input type="text" id="setting-company-name" name="company_name" placeholder="Your Company"
                            value="{{ old('company_name', $companyName) }}" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                          glass-input text-gray-900 placeholder:text-gray-400
                                          focus:outline-none transition-all duration-200 text-sm">
                        @error('company_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Company Logo Upload or URL --}}
                    <div x-data="{ logoType: 'file' }">
                        <label class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">Company Logo</label>

                        {{-- Toggle between File Upload and URL --}}
                        <div class="flex gap-2 mb-2">
                            <button type="button" @click="logoType = 'file'"
                                :class="logoType === 'file' ? 'bg-green-700 text-white shadow-sm' : 'bg-black/5 text-gray-600 hover:bg-black/10'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer">
                                Upload File
                            </button>
                            <button type="button" @click="logoType = 'url'"
                                :class="logoType === 'url' ? 'bg-green-700 text-white shadow-sm' : 'bg-black/5 text-gray-600 hover:bg-black/10'"
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer">
                                URL Link
                            </button>
                        </div>

                        {{-- File Input --}}
                        <div x-show="logoType === 'file'" class="animate-fade-in" style="animation-duration: 200ms;">
                            <input type="file" id="setting-company-logo-file" name="company_logo_file" accept="image/*"
                                class="w-full px-3.5 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl
                                              glass-input text-gray-900 text-sm focus:outline-none file:mr-4 file:py-1 file:px-3
                                              file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                              file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            @error('company_logo_file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- URL Input --}}
                        <div x-show="logoType === 'url'" class="animate-fade-in" style="animation-duration: 200ms;">
                            <input type="text" id="setting-company-logo-url" name="company_logo"
                                placeholder="https://example.com/logo.png" value="{{ old('company_logo', $companyLogo) }}"
                                class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                              glass-input text-gray-900 placeholder:text-gray-400
                                              focus:outline-none transition-all duration-200 text-sm">
                            @error('company_logo')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200/40 !mt-5 !mb-5"></div>

                    {{-- Admin Email --}}
                    <div>
                        <label for="admin-email" class="block text-xs sm:text-sm font-medium text-gray-500 mb-1.5">Admin
                            Email</label>
                        <input type="email" id="admin-email" name="admin_email" placeholder="admin@example.com"
                            value="{{ old('admin_email', $adminEmail) }}" class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl
                                          glass-input text-gray-900 placeholder:text-gray-400
                                          focus:outline-none transition-all duration-200 text-sm">
                        @error('admin_email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 sm:py-3 rounded-xl sm:rounded-2xl text-sm font-semibold text-white
                                       bg-gradient-to-r from-gray-700 to-gray-900
                                       shadow-lg shadow-gray-300/40
                                       hover:shadow-xl hover:shadow-gray-400/40 hover:from-gray-800 hover:to-black
                                       active:scale-[0.98] transition-all duration-200">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
        TOAST NOTIFICATION (macOS notification style)
        ═══════════════════════════════════════════════ --}}
        <div x-show="showToast" x-transition:enter="transition ease-out duration-400"
            x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-3 scale-95" class="fixed top-5 left-1/2 -translate-x-1/2 z-[60]
                        flex items-center gap-2.5 px-5 py-3 rounded-2xl
                        glass-modal text-sm font-medium text-gray-700 shadow-xl" style="display: none;">

            {{-- Success Icon --}}
            <div x-show="toastType === 'success'"
                class="flex items-center justify-center w-5 h-5 rounded-full bg-green-500/15">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            {{-- Error Icon --}}
            <div x-show="toastType === 'error'" class="flex items-center justify-center w-5 h-5 rounded-full bg-red-500/15">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <span x-text="toastMessage"></span>
        </div>

    </div>
@endsection