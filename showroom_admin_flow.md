# Showroom Admin Management Flow Diagram

## Overview
This document outlines the flow for managing "Our Showrooms" in the admin panel. It uses a single `showrooms` table with optional fields for detailed pages, and a separate `showroom_images` table for multiple images. The form is split into 2 steps: Step 1 (mandatory for cards) and Step 2 (optional for detailed page).

## Table Structures

### showrooms Table
- id (primary key, auto-increment)
- name (text, translatable - JSON for multi-language support)
- slug (string, unique, auto-generated from name using Spatie Sluggable)
- address (json: {"address": "string", "phone": "string", "email": "string"})
- location (text, optional - iframe embed code for map)
- cover_image (string, path to image file)
- opening_hours (json, optional - {"monday": "6:00 AM - 9:00 PM", "tuesday": "...", ...})
- status (enum: 'active', 'inactive', default 'active')
- sort_order (integer, default 0)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, soft deletes)

### showroom_images Table
- id (primary key, auto-increment)
- showroom_id (foreign key to showrooms.id)
- image (string, path to image file)
- sort_order (integer, default 0)
- created_at (timestamp)
- updated_at (timestamp)

## Admin Flow Diagram

```
Admin Dashboard
    |
    v
Showrooms Management Page
    |
    +---------------------+
    | List All Showrooms  |
    | - Display cards     |
    | - Edit/Delete       |
    | - Add New           |
    +---------------------+
                |
                v
        Add/Edit Showroom Form
                |
                +---------------------+
                |     Step 1          |
                | (Mandatory Fields)  |
                +---------------------+
                | - Showroom Name     |
                |   (translatable)    |
                | - Address (JSON)    |
                |   - Address         |
                |   - Phone           |
                |   - Email           |
                | - Cover Image       |
                | - Status            |
                | - Sort Order        |
                +---------------------+
                        |
                        v
                +---------------------+
                |     Step 2          |
                | (Optional Fields)   |
                | Can be skipped      |
                +---------------------+
                | - Location (iframe) |
                | - Opening Hours     |
                |   (JSON)            |
                | - Multiple Images   |
                |   (showroom_images) |
                +---------------------+
                        |
                        v
            Save Showroom Record
                |
                +---------------------+
                | If Step 2 completed |
                | Detailed page       |
                | available           |
                +---------------------+
                |
                +---------------------+
                | If Step 2 skipped   |
                | Only card display   |
                | No detailed page    |
                +---------------------+
                        |
                        v
            Redirect to Showrooms List
```

## Key Features

### Step 1 (Mandatory - For Cards Display)
- Showroom Name: Translatable field (supports multiple languages)
- Address: JSON object with address, phone, email
- Cover Image: Single image for card display
- Status: Active/Inactive
- Sort Order: For ordering in lists

### Step 2 (Optional - For Detailed Page)
- Location: Text field for iframe embed (e.g., Google Maps embed code)
- Opening Hours: JSON object with days and times
- Multiple Images: Stored in separate showroom_images table with sort order

### Validation Rules
- Step 1: All fields required
- Step 2: All fields optional, can be left empty
- Slug: Auto-generated from name (English translation), unique
- Images: Validate file types, sizes, store in public/images/showrooms/

### Client-Side Display Logic
- Showrooms Cards: Always display if status = 'active', ordered by sort_order
- Detailed Page: Only if location or opening_hours is not null/empty
- Route: /showrooms/{slug} - check if detailed fields exist, else 404 or redirect

### Permissions
- Use existing permission system (Spatie Permission)
- Create permissions: view_showrooms, create_showrooms, edit_showrooms, delete_showrooms
- Assign to admin roles

## File Structure (Proposed)
```
app/
├── Models/
│   ├── Showroom.php
│   └── ShowroomImage.php
├── Http/Controllers/Admin/
│   └── ShowroomController.php
└── Http/Requests/Admin/
    └── ShowroomRequest.php

database/migrations/
├── create_showrooms_table.php
└── create_showroom_images_table.php

resources/views/admin/
└── showrooms/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    └── _form.blade.php

routes/
└── admin.php (add showroom routes)
```

## Next Steps
1. Create migrations and models
2. Set up permissions
3. Create controller and views
4. Add routes
5. Implement form with steps (using JavaScript for step navigation)
6. Add image upload handling
7. Update client-side views to use database data instead of hardcoded arrays
