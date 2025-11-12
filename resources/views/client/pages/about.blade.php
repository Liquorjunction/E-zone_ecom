@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            About Us
          </h1>
          <p class="text-gray-600">Learn more about our company, mission, and team.</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">About</span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 mb-6">
      <div class="prose max-w-none prose-p:text-gray-700 prose-p:leading-relaxed prose-h2:font-bold prose-h2:text-gray-900">
        <h2>Who We Are</h2>
        <p>Welcome to our store! Founded in 2025, we set out with a simple mission: to provide the best products with the best service. We believe that shopping online should be simple, fast, and secure. That's why we've invested in a state-of-the-art platform, a dedicated customer support team, and a curated selection of products we truly believe in.</p>
        <p>From humble beginnings, we have grown into a trusted name for customers across the country. Our team is passionate about technology, design, and finding the next great product so you don't have to. We're more than just a retailer; we're a team of enthusiasts dedicated to enhancing your lifestyle.</p>

        <h2>Our Mission</h2>
        <blockquote class="border-l-4 border-primary bg-primary/10 p-4 rounded-r-lg text-xl font-medium text-gray-800 not-italic">
          "To provide an unparalleled shopping experience by offering a curated selection of high-quality products, exceptional customer service, and a seamless, secure platform."
        </blockquote>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
        Meet Our Team
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="text-center">
          <img src="https://via.placeholder.com/150x150" alt="Team Member 1" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="font-bold text-gray-900 text-lg">Jane Doe</h3>
          <p class="text-sm text-primary font-medium">CEO & Founder</p>
        </div>
        <div class="text-center">
          <img src="https://via.placeholder.com/150x150" alt="Team Member 2" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="font-bold text-gray-900 text-lg">John Smith</h3>
          <p class="text-sm text-primary font-medium">Head of Technology</p>
        </div>
        <div class="text-center">
          <img src="https://via.placeholder.com/150x150" alt="Team Member 3" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="font-bold text-gray-900 text-lg">Alexa Johnson</h3>
          <p class="text-sm text-primary font-medium">Marketing Director</p>
        </div>
        <div class="text-center">
          <img src="https://via.placeholder.com/150x150" alt="Team Member 4" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="font-bold text-gray-900 text-lg">Mike Brown</h3>
          <p class="text-sm text-primary font-medium">Operations Manager</p>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
