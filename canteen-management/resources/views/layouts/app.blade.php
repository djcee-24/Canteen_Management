<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Canteen Management') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-red-600 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <div class="bg-yellow-400 text-red-600 px-3 py-1 rounded-full font-bold text-xl">
                                🍔 Canteen
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-8 md:flex md:space-x-8">
                        <a href="{{ route('menu') }}" class="text-white hover:text-yellow-300 px-3 py-2 text-sm font-medium transition duration-150">
                            Menu
                        </a>
                        <a href="{{ route('order.track') }}" class="text-white hover:text-yellow-300 px-3 py-2 text-sm font-medium transition duration-150">
                            Track Order
                        </a>
                        <a href="#" class="text-white hover:text-yellow-300 px-3 py-2 text-sm font-medium transition duration-150">
                            Locations
                        </a>
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- Shopping Cart -->
                    <livewire:shopping-cart />

                    <!-- Authentication -->
                    @auth
                        <div class="relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-white hover:text-yellow-300 transition duration-150">
                                        <div class="flex items-center">
                                            @if(Auth::user()->avatar)
                                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" />
                                            @else
                                                <div class="h-8 w-8 bg-yellow-400 rounded-full flex items-center justify-center text-red-600 font-semibold">
                                                    {{ substr(Auth::user()->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="ml-2 text-sm font-medium">{{ Auth::user()->name }}</span>
                                            <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('profile.show') }}">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    @if(auth()->user()->hasRole(['admin', 'tenant']))
                                        <x-dropdown-link href="{{ auth()->user()->hasRole('admin') ? '/admin' : '/tenant' }}">
                                            {{ __('Dashboard') }}
                                        </x-dropdown-link>
                                    @endif

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <x-dropdown-link href="{{ route('logout') }}"
                                                @click.prevent="$root.submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-white hover:text-yellow-300 text-sm font-medium transition duration-150">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="bg-yellow-400 text-red-600 px-4 py-2 rounded-full text-sm font-semibold hover:bg-yellow-300 transition duration-150">
                            Sign up
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-white hover:text-yellow-300 focus:outline-none focus:text-yellow-300" x-data="{ open: false }" @click="open = !open">
                        <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                            <path x-show="!open" fill-rule="evenodd" clip-rule="evenodd" d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
                            <path x-show="open" fill-rule="evenodd" clip-rule="evenodd" d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-4 mt-4 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if (session('warning'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 mx-4 mt-4 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="bg-yellow-400 text-red-600 px-3 py-1 rounded-full font-bold text-xl mr-3">
                            🍔 Canteen
                        </div>
                        <span class="text-lg font-semibold">Management System</span>
                    </div>
                    <p class="text-gray-300 max-w-md">
                        Your favorite canteen management solution. Fresh food, fast service, and real-time order tracking.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('menu') }}" class="text-gray-300 hover:text-white transition">Menu</a></li>
                        <li><a href="{{ route('order.track') }}" class="text-gray-300 hover:text-white transition">Track Order</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Nutrition</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Feedback</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-700">
                <p class="text-gray-400 text-sm text-center">
                    © {{ date('Y') }} Canteen Management System. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Laravel Echo for real-time updates -->
    <script>
        window.Echo.channel('orders')
            .listen('.order.new', (e) => {
                // Handle new order notifications
                console.log('New order received:', e);
            })
            .listen('.order.status.updated', (e) => {
                // Handle order status updates
                console.log('Order status updated:', e);
                
                // Dispatch Livewire events
                Livewire.dispatch('orderStatusUpdated', e);
            });
    </script>
</body>
</html>
