# Category Management Setup Instructions

## Overview
This document provides instructions for setting up the category management functionality in the MdLink Rwanda system.

## Database Setup

### 1. Add Status Field to Category Table
Run the following SQL command in your MySQL database:

```sql
ALTER TABLE `category` ADD COLUMN `status` ENUM('1', '2') DEFAULT '1' AFTER `description`;
UPDATE `category` SET `status` = '1' WHERE `status` IS NULL;
```

### 2. Database Table Structure
The category table should have the following structure:
- `category_id` (int, auto-increment, primary key)
- `category_name` (varchar(100), not null)
- `description` (text, nullable)
- `status` (ENUM('1', '2'), default '1')
- `created_at` (timestamp, default current_timestamp)
- `updated_at` (timestamp, default current_timestamp, on update current_timestamp)

## Files Modified

### 1. Backend Files
- `dawapharma/php_action/createCategories.php` - Updated to handle status field
- `dawapharma/php_action/manageCategory.php` - Updated to handle status field in CRUD operations

### 2. Frontend Files
- `dawapharma/add-category.php` - Updated form to include description field
- `dawapharma/custom/js/categories.js` - Updated validation messages
- `dawapharma/custom/js/category_management.js` - Updated to handle status field

### 3. Database Files
- `database/add_status_to_category.sql` - SQL script to add status field

## Usage

### Adding Categories
1. Navigate to: `http://localhost/MdLink%20Rwanda/dawapharma/placeholder.php?title=Categories`
2. Fill in the category name, description, and select status
3. Click "Add Category"
4. A success popup will appear: "Category added successfully"

### Status Values
- `1` = Available
- `2` = Not Available

## Features
- ✅ Add new categories with name, description, and status
- ✅ Update existing categories
- ✅ Delete categories (with validation for medicines using the category)
- ✅ Display categories in a table with status badges
- ✅ Success/error popup messages
- ✅ Form validation
- ✅ Responsive design

## Testing
1. Add a new category through the form
2. Verify the data is saved in the database
3. Check that the success popup appears
4. Verify the category appears in the table
5. Test editing and deleting categories

## Troubleshooting
- If the status field doesn't exist, run the SQL command above
- If popups don't appear, check browser console for JavaScript errors
- If form submission fails, check PHP error logs
