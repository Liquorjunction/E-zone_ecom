@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Profile')

@section('dashboard-content')
  {{-- Profile Header Card --}}
  <div class="bg-gradient-to-br from-primary/5 to-primary/10 rounded-xl lg:rounded-2xl shadow-sm border-2 border-primary/20 p-6 lg:p-8 mb-4 lg:mb-6">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
      {{-- Avatar with Upload Overlay --}}
      <div class="relative group">
        <div class="w-24 h-24 lg:w-32 lg:h-32 rounded-full overflow-hidden border-4 border-white shadow-lg">
          <img src="https://via.placeholder.com/150x150" alt="User Avatar" class="w-full h-full object-cover">
        </div>
        <button type="button" class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
        {{-- Online Status Badge --}}
        <div class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-4 border-white rounded-full"></div>
      </div>

      {{-- User Info --}}
      <div class="flex-1 text-center md:text-left">
        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">John Doe</h2>
        <p class="text-sm lg:text-base text-gray-600 mb-4">john.doe@example.com</p>
        <div class="flex flex-wrap gap-2 justify-center md:justify-start">
          <span class="px-3 py-1.5 bg-white text-primary text-xs font-semibold rounded-full border border-primary/20">
            Premium Member
          </span>
          <span class="px-3 py-1.5 bg-white text-gray-700 text-xs font-semibold rounded-full border border-gray-200">
            Member since 2024
          </span>
        </div>
      </div>

      {{-- Quick Stats --}}
      <div class="flex gap-4 lg:gap-6">
        <div class="text-center">
          <p class="text-2xl lg:text-3xl font-bold text-primary">24</p>
          <p class="text-xs lg:text-sm text-gray-600">Orders</p>
        </div>
        <div class="w-px bg-gray-200"></div>
        <div class="text-center">
          <p class="text-2xl lg:text-3xl font-bold text-primary">1.2k</p>
          <p class="text-xs lg:text-sm text-gray-600">Points</p>
        </div>
      </div>
    </div>
  </div>

  <form>
    {{-- Personal Information Section --}}
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
      <div class="flex items-center justify-between mb-4 lg:mb-6">
        <h3 class="text-lg lg:text-xl font-bold text-gray-900 flex items-center">
          <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
          Personal Information
        </h3>
        <button type="button" class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
          Edit
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
        {{-- First Name --}}
        <div>
          <label for="firstName" class="block text-sm font-semibold text-gray-700 mb-2">
            First Name *
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <input
              type="text"
              id="firstName"
              name="firstName"
              required
              value="John"
              class="w-full pl-12 pr-4 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
              placeholder="Enter your first name"
            >
          </div>
        </div>

        {{-- Last Name --}}
        <div>
          <label for="lastName" class="block text-sm font-semibold text-gray-700 mb-2">
            Last Name *
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <input
              type="text"
              id="lastName"
              name="lastName"
              required
              value="Doe"
              class="w-full pl-12 pr-4 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
              placeholder="Enter your last name"
            >
          </div>
        </div>

        {{-- Email --}}
        <div>
          <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
            Email Address *
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <input
              type="email"
              id="email"
              name="email"
              required
              value="john.doe@example.com"
              class="w-full pl-12 pr-4 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
              placeholder="your.email@example.com"
            >
          </div>
        </div>

        {{-- Phone --}}
        <div>
          <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
            Phone Number
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
            </div>
            <input
              type="tel"
              id="phone"
              name="phone"
              value="+1 (555) 000-0000"
              class="w-full pl-12 pr-4 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
              placeholder="+1 (555) 000-0000"
            >
          </div>
        </div>

        {{-- Date of Birth --}}
        <div>
          <label for="dob" class="block text-sm font-semibold text-gray-700 mb-2">
            Date of Birth
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <input
              type="date"
              id="dob"
              name="dob"
              value="1990-01-15"
              class="w-full pl-12 pr-4 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base"
            >
          </div>
        </div>

        {{-- Gender --}}
        <div>
          <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
            Gender
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <select
              id="gender"
              name="gender"
              class="w-full pl-12 pr-4 py-3 lg:py-3.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm lg:text-base appearance-none"
            >
              <option value="">Prefer not to say</option>
              <option value="male" selected>Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Preferences Section --}}
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
      <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-4 lg:mb-6 flex items-center">
        <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
        Preferences
      </h3>

      <div class="space-y-4">
        {{-- Newsletter --}}
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
          <input
            type="checkbox"
            id="newsletter"
            name="newsletter"
            checked
            class="w-5 h-5 text-primary border-2 border-gray-300 rounded focus:ring-2 focus:ring-primary/20 mt-0.5"
          >
          <div class="flex-1">
            <label for="newsletter" class="block font-semibold text-gray-900 cursor-pointer mb-1">
              Email Newsletter
            </label>
            <p class="text-sm text-gray-600">
              Receive updates about new products, special offers, and exclusive deals
            </p>
          </div>
        </div>

        {{-- SMS Notifications --}}
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
          <input
            type="checkbox"
            id="sms"
            name="sms"
            class="w-5 h-5 text-primary border-2 border-gray-300 rounded focus:ring-2 focus:ring-primary/20 mt-0.5"
          >
          <div class="flex-1">
            <label for="sms" class="block font-semibold text-gray-900 cursor-pointer mb-1">
              SMS Notifications
            </label>
            <p class="text-sm text-gray-600">
              Get order updates and delivery notifications via text message
            </p>
          </div>
        </div>

        {{-- Order Updates --}}
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
          <input
            type="checkbox"
            id="order_updates"
            name="order_updates"
            checked
            class="w-5 h-5 text-primary border-2 border-gray-300 rounded focus:ring-2 focus:ring-primary/20 mt-0.5"
          >
          <div class="flex-1">
            <label for="order_updates" class="block font-semibold text-gray-900 cursor-pointer mb-1">
              Order Updates
            </label>
            <p class="text-sm text-gray-600">
              Receive notifications about your order status and delivery
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-end">
      <button
        type="button"
        class="px-6 py-3 lg:py-4 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-sm lg:text-base"
      >
        Cancel
      </button>
      <button
        type="submit"
        class="px-6 py-3 lg:py-4 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-primary/90 hover:shadow-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        Save Changes
      </button>
    </div>
  </form>
@endsection
