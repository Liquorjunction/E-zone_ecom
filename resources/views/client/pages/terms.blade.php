@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            Terms of Service
          </h1>
          <p class="text-gray-600">Please read our terms and conditions carefully.</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">Terms of Service</span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
      <div class="prose max-w-none prose-p:text-gray-700 prose-p:leading-relaxed prose-h2:font-bold prose-h2:text-gray-900 prose-h3:font-semibold prose-h3:text-gray-800 prose-a:text-primary hover:prose-a:text-primary/80 prose-ul:list-disc prose-ul:ml-6 prose-ol:list-decimal prose-ol:ml-6">

        <p class_="text-sm text-gray-500">Last updated: November 12, 2025</p>

        <h2>1. Introduction</h2>
        <p>Welcome to [Your Company Name] ("we," "our," or "us"). These Terms of Service govern your use of our website and services. By accessing or using our service, you agree to be bound by these terms.</p>

        <h2>2. Use of Our Service</h2>
        <p>You must be at least 18 years old to use our service. You agree not to use the service for any illegal or unauthorized purpose. You are responsible for all activity that occurs under your account.</p>

        <h3>a. Account Registration</h3>
        <p>To access certain features, you may be required to create an account. You must provide accurate and complete information. You are solely responsible for the security of your password.</p>

        <h2>3. Products and Pricing</h2>
        <p>We make every effort to display product information and pricing accurately. However, we reserve the right to correct any errors or inaccuracies and to change or update information at any time without prior notice.</p>

        <h2>4. Limitation of Liability</h2>
        <p>To the fullest extent permitted by applicable law, [Your Company Name] shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses, resulting from:</p>
        <ol>
          <li>Your access to or use of or inability to access or use the service.</li>
          <li>Any conduct or content of any third party on the service.</li>
          <li>Any content obtained from the service.</li>
        </ol>

        <h2>5. Governing Law</h2>
        <p>These Terms shall be governed by and construed in accordance with the laws of India, without regard to its conflict of law provisions.</p>

        </div>
    </div>
  </div>
</div>
@endsection
