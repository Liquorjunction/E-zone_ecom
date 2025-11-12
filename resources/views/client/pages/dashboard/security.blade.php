@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'Security')

@section('dashboard-content')
  <form>
    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
        Change Your Password
      </h2>

      <div class="space-y-4 max-w-lg">
        <div>
          <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Current Password *</label>
          <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl ...">
        </div>
        <div>
          <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">New Password *</label>
          <input type="password" id="new_password" name="new_password" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl ...">
        </div>
        <div>
          <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password *</Slabel>
          <input type="password" id="confirm_password" name="confirm_password" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl ...">
        </div>
      </div>

      <div class="mt-8 pt-6 border-t-2 border-gray-100 flex justify-start">
        <button type="submit" class="px-6 py-4 bg-primary text-white font-bold rounded-xl shadow-lg ...">
          Update Password
        </button>
      </div>
    </div>
  </form>
@endsection
