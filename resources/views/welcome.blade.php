<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Equipment Management System') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav x-data="{ isOpen: false }" class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <x-strathmore-logo class="h-16 w-auto" />
                    </div>
                    <!-- Add Navigation Links -->
                    <div class="hidden md:flex md:ml-6 md:items-center space-x-4">
                        <a href="#about" class="text-blue-900 hover:text-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">About</a>
                        <a href="#contact" class="text-blue-900 hover:text-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Contact</a>
                    </div>
                </div>
                <div class="flex items-center">
                    @if (Route::has('login'))
                        <div class="space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-blue-900 hover:text-blue-700 px-4 py-2 rounded-md text-sm font-semibold transition duration-150">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-blue-900 hover:text-blue-700 px-4 py-2 rounded-md text-sm font-semibold transition duration-150">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-blue-900 text-white hover:bg-blue-800 px-6 py-2 rounded-md text-sm font-semibold shadow-lg transition duration-150">Register</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
                <div class="flex md:hidden">
                    <button @click="isOpen = !isOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-blue-900 hover:text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" :class="{'hidden': isOpen, 'block': !isOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" :class="{'block': isOpen, 'hidden': !isOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile menu -->
    <div class="md:hidden" id="mobile-menu" x-show="isOpen" x-transition:enter="transition ease-out duration-100 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75 transform" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="#about" class="text-blue-900 hover:text-blue-700 block px-3 py-2 rounded-md text-base font-medium">About</a>
            <a href="#contact" class="text-blue-900 hover:text-blue-700 block px-3 py-2 rounded-md text-base font-medium">Contact</a>
            @auth
                <a href="{{ url('/dashboard') }}" class="text-blue-900 hover:text-blue-700 block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-blue-900 hover:text-blue-700 block px-3 py-2 rounded-md text-base font-medium">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-blue-900 hover:text-blue-700 block px-3 py-2 rounded-md text-base font-medium">Register</a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Hero Section -->
    <div class="relative bg-blue-900 min-h-screen flex items-center">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover object-center" src="{{ asset('images/welcome.jpg') }}" alt="Media Equipment">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-blue-900/75 to-blue-900/90"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 mt-20">
            <div class="max-w-3xl">
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl leading-tight">
                    Equipment Management System
                </h1>
                <p class="mt-6 text-xl text-gray-300 max-w-3xl">
                    Streamline your equipment booking process with our state-of-the-art management system. Easy to use, efficient, and reliable.
                </p>
                <div class="mt-10">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-8 py-4 border border-transparent text-base font-medium rounded-lg text-blue-900 bg-white hover:bg-gray-50 shadow-xl transform transition duration-150 hover:scale-105">
                        Get Started
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="py-24 bg-white" id="about">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-blue-900 font-semibold tracking-wide uppercase">About Us</h2>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                    Excellence in Equipment Management
                </p>
                <p class="mt-6 text-xl text-gray-500 lg:mx-auto max-w-3xl">
                    Our system provides a comprehensive solution for managing and booking equipment at Strathmore University.
                </p>
            </div>

            <div class="mt-16">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-12 md:gap-y-12">
                    <div class="relative p-8 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                        <dt>
                            <div class="absolute flex items-center justify-center h-14 w-14 rounded-lg bg-blue-900 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <p class="ml-20 text-xl font-semibold text-gray-900">Easy Booking Process</p>
                        </dt>
                        <dd class="mt-4 ml-20 text-base text-gray-500">
                            Book equipment with just a few clicks. Our streamlined process makes it easy to reserve what you need, when you need it.
                        </dd>
                    </div>

                    <div class="relative p-8 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                        <dt>
                            <div class="absolute flex items-center justify-center h-14 w-14 rounded-lg bg-blue-900 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                            <p class="ml-20 text-xl font-semibold text-gray-900">Real-time Availability</p>
                        </dt>
                        <dd class="mt-4 ml-20 text-base text-gray-500">
                            Check equipment availability in real-time. No more double bookings or confusion about equipment status.
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="bg-gray-50 py-24" id="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-16">
                <h2 class="text-base text-blue-900 font-semibold tracking-wide uppercase">Contact Us</h2>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                    Get in Touch
                </p>
                <p class="mt-6 text-xl text-gray-500 lg:mx-auto max-w-3xl">
                    Have questions? We're here to help.
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white shadow-xl rounded-xl p-8 transform transition duration-300 hover:scale-105">
                        <h3 class="text-xl font-semibold text-gray-900">Send us a Message</h3>
                        @if(session('success'))
                            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('contact.send') }}" method="POST" class="mt-6 space-y-6">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea name="message" id="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                            </div>
                            <div>
                                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white shadow-xl rounded-xl p-8 transform transition duration-300 hover:scale-105">
                        <h3 class="text-xl font-semibold text-gray-900">Contact Information</h3>
                        <dl class="mt-6 space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-2 text-base text-gray-900">Ole Sangale Road, Madaraka Estate, P.O Box 59857, 00200, Nairobi, Kenya</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-2 text-base text-gray-900">+254 (0) 703 034000</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-2 text-base text-gray-900">media@strathmore.edu</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white shadow-xl rounded-xl p-8 transform transition duration-300 hover:scale-105">
                        <h3 class="text-xl font-semibold text-gray-900">Operating Hours</h3>
                        <dl class="mt-6 space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Monday - Friday</dt>
                                <dd class="mt-2 text-base text-gray-900">8:00 AM - 5:00 PM</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Saturday</dt>
                                <dd class="mt-2 text-base text-gray-900">8:00 AM - 12:00 PM</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sunday & Public Holidays</dt>
                                <dd class="mt-2 text-base text-gray-900">Closed</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-900">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
                <div class="flex items-center">
                    <x-strathmore-logo class="h-12 w-auto filter brightness-0 invert" />
                    <p class="ml-4 text-white text-sm">&copy; {{ date('Y') }} Strathmore University. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-white hover:text-gray-300 transform transition hover:scale-110">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <a href="#" class="text-white hover:text-gray-300 transform transition hover:scale-110">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                        </svg>
                    </a>
                    <a href="#" class="text-white hover:text-gray-300 transform transition hover:scale-110">
                        <span class="sr-only">LinkedIn</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button x-data="{ showButton: false }" 
            x-init="window.addEventListener('scroll', () => { showButton = window.pageYOffset > 500 })" 
            x-show="showButton" 
            @click="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="fixed bottom-8 right-8 bg-blue-900 text-white p-3 rounded-full shadow-lg hover:bg-blue-800 transition duration-300 transform hover:scale-110 focus:outline-none"
            aria-label="Scroll to top">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
</body>
</html>
