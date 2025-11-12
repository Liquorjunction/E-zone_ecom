@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Profile')

@section('dashboard-content')
  <form>
    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
        Personal Information
      </h2>

      <div class="flex items-center gap-6 mb-6 pb-6 border-b-2 border-gray-100">
        <img src="https://via.placeholder.com/96x96" alt="User Avatar" class="w-24 h-24 rounded-full">
        <div class="flex gap-2">
          <button type="button" class="px-4 py-3 bg-primary text-white font-semibold rounded-xl ...">
            Upload New
          </button>
          <button type="button" class="px-4 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl ...">
            Remove
          </button>
        </div>
      </div>

      <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="firstName" class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
            <input type="text" id="firstName" name="firstName" required value="John" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl ...">
          </div>
          <div>
            <label for="lastName" class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
            <input type="text" id="lastName" name="lastName" required value="Doe" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl ...">
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
            <input type="email" id="email" name="email" required value="john.doe@example.com" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl ...">
          </div>
          <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="+1 (555) 000-0000" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl ...">
          </div>
        </div>
      </div>

      <div class="mt-8 pt-6 border-t-2 border-gray-100 flex justify-end">
        <button type="submit" class="px-6 py-4 bg-primary text-white font-bold rounded-xl shadow-lg ...">
          Save Changes
        </button>
      </div>
    </div>
  </form>
@endsection
