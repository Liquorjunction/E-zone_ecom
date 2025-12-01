# Translation Fix for Quill Editor

## Problem
The translation with Quill editor was not working correctly. Google Translate was treating HTML as plain text, corrupting tags and attributes (e.g., style attributes getting translated).

## Solution Implemented

### 1. Added HTML-aware Translation Function
- Added `gtrans_html()` function in `app/Helpers/TranslationHelper.php`
- Parses HTML using DOMDocument, translates only text nodes, preserves HTML structure

### 2. Updated Translation Repository
- Modified `TranslationRepository::translateAll()` to accept `$isHtml` parameter
- Modified `translateBatch()` to detect HTML fields (containing 'content') and use `gtrans_html()`

### 3. Updated Frontend Translation Handler
- Modified `public/admin_assets/js/translationHandler.js` to send `is_html: true` for Quill fields

### 4. Updated Translation Controller
- Modified `TranslationController::translate()` to accept and pass `is_html` parameter

### 5. Updated Auto-Translate in Controllers
- Updated controllers to use `gtrans_html()` for HTML fields:
  - TncController: 'content' field
  - ShippingAndReturnController: 'content' field
  - PrivacyPolicyController: 'description' field
  - FaqController: 'answer' field

## Files Modified
- app/Helpers/TranslationHelper.php
- app/Repositories/Admin/Repositories/TranslationRepository.php
- public/admin_assets/js/translationHandler.js
- app/Http/Controllers/Admin/TranslationController.php
- app/Http/Controllers/Admin/TncController.php
- app/Http/Controllers/Admin/ShippingAndReturnController.php
- app/Http/Controllers/Admin/PrivacyPolicyController.php
- app/Http/Controllers/Admin/FaqController.php

## Testing
- Test translation of Quill content with HTML tags and styles
- Verify that HTML structure is preserved
- Check that text within tags is properly translated
- Ensure auto-translate on save works correctly

## Status
✅ All changes implemented
