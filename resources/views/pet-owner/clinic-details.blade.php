@extends('layouts.app')

@section('title', 'Clinic Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-blue-100 shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">Clinic Information</h1>
        <p class="text-gray-600">Contact us and visit our clinic</p>
    </div>

    <!-- Clinic Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Main Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">PetPro Veterinary Clinic</h2>
            
            <div class="space-y-6">
                <!-- Phone -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500">
                            <i class="fas fa-phone text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Phone Number</h3>
                        <p class="mt-2 text-base text-gray-600">
                            <a href="tel:092323232" class="text-blue-600 hover:text-blue-800">092323232</a>
                        </p>
                    </div>
                </div>

                <!-- Email -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-green-500">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Email Address</h3>
                        <p class="mt-2 text-base text-gray-600">
                            <a href="mailto:petpro@gmail.com" class="text-blue-600 hover:text-blue-800">petpro@gmail.com</a>
                        </p>
                    </div>
                </div>

                <!-- Address -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-purple-500">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Address</h3>
                        <p class="mt-2 text-base text-gray-600">
                            Malabanban Sur, Sdasdas
                        </p>
                    </div>
                </div>

                <!-- Facebook -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-600">
                            <i class="fab fa-facebook-f text-white"></i>
                        </div> -->
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Follow Us on Facebook</h3>
                        <p class="mt-2 text-base text-gray-600">
                                PetPro Veterinary Clinic
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Contact Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Business Hours</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Monday - Friday</span>
                    <span class="text-gray-600">8:00 AM - 6:00 PM</span>
                </div>
                <div class="border-t border-blue-200"></div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Saturday</span>
                    <span class="text-gray-600">9:00 AM - 4:00 PM</span>
                </div>
                <div class="border-t border-blue-200"></div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Sunday</span>
                    <span class="text-gray-600">10:00 AM - 2:00 PM</span>
                </div>
                <div class="border-t border-blue-200"></div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Emergency</span>
                    <span class="text-red-600 font-semibold">24/7 Available</span>
                </div>
            </div>

         
        </div>
    </div>

    <!-- Location/Map Info -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Visit Us</h2>
        <p class="text-gray-600 mb-4">
            We're conveniently located in Malabanban Sur, Sdasdas. Our modern facilities and experienced team are ready to provide the best care for your pets.
        </p>
        
       
    </div>
</div>
@endsection
