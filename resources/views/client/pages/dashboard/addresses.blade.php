@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'Saved Addresses')

@section('dashboard-content')
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Manage Your Addresses</h2>
    <button class="px-6 py-3 bg-primary text-white font-bold rounded-xl shadow-lg ...">
      <span class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
        Add New Address
      </span>
    </button>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border-2 border-primary p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900">Home</h3>
        <span class="px-3 py-1 text-xs font-semibold text-primary bg-primary/10 rounded-full">Default</span>
      </div>
      <div class="text-gray-700 space-y-2">
        <p class="font-semibold">John Doe</p>
        <p>123 Main Street, Apt 4B<br>New York, NY 10001<br>United States</p>
        <p>+1 (555) 000-0000</p>
      </div>
      <div class="flex items-center gap-2 mt-6 pt-6 border-t-2 border-gray-100">
        <button class="text-sm font-semibold text-primary hover:opacity-80">Edit</button>
        <span class="text-gray-300">|</span>
        <button class="text-sm font-semibold text-red-500 hover:opacity-80">Delete</button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900">Work</h3>
        <button class="text-sm font-semibold text-gray-600 hover:text-primary">Set as Default</button>
      </div>
      <div class="text-gray-700 space-y-2">
        <p class="font-semibold">John Doe</p>
        <p>456 Business Ave, Suite 500<br>New York, NY 10005<br>United States</p>
        <p>+1 (555) 000-1111</p>
      </div>
      <div class="flex items-center gap-2 mt-6 pt-6 border-t-2 border-gray-100">
        <button class="text-sm font-semibold text-primary hover:opacity-80">Edit</button>
        <span class="text-gray-300">|</span>
        <button class="text-sm font-semibold text-red-500 hover:opacity-80">Delete</button>
      </div>
    </div>

    <a href="#" class="flex flex-col items-center justify-center w-full min-h-[200px] border-4 border-dashed border-gray-200 rounded-2xl text-gray-400 hover:border-primary hover:text-primary transition-colors">
      <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
      <span class="font-semibold mt-2">Add New Address</span>
    </a>
  </div>
@endsection
