@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            Privacy Policy
          </h1>
          <p class="text-gray-600">Your privacy is important to us.</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">Privacy Policy</span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
      <div class="prose max-w-none prose-p:text-gray-700 prose-p:leading-relaxed prose-h2:font-bold prose-h2:text-gray-900 prose-h3:font-semibold prose-h3:text-gray-800 prose-a:text-primary hover:prose-a:text-primary/80 prose-ul:list-disc prose-ul:ml-6 prose-ol:list-decimal prose-ol:ml-6">

        <p class_="text-sm text-gray-500">Last updated: November 12, 2025</p>

        <h2>1. Introduction</h2>
        <p>[Your Company Name] ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>

        <h2>2. Information We Collect</h2>
        <p>We may collect personal information about you in a variety of ways. The information we may collect on the Site includes:</p>

        <h3>a. Personal Data</h3>
        <p>Personally identifiable information, such as your name, shipping address, email address, and telephone number, that you voluntarily give to us when you register with the Site or when you choose to participate in various activities related to the Site, such as online chat and message boards.</p>

        <h3>b. Derivative Data</h3>
        <p>Information our servers automatically collect when you access the Site, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the Site.</p>

        <h2>3. Use of Your Information</h2>
        <p>Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the Site to:</p>
        <ul>
          <li>Create and manage your account.</li>
          <li>Process your orders and manage payments.</li>
          <li>Email you regarding your account or order.</li>
          <li>Send you promotional information about new products, special offers, and other information we think you may find interesting.</li>
        </ul>

        <h2>4. Security of Your Information</h2>
        <p>We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable.</p>

        </div>
    </div>
  </div>
</div>
@endsection
