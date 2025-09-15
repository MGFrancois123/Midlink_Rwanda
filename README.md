# MdLink Rwanda - Medicines and Categories Management

This application provides functionality for managing medicines and categories in a pharmacy management system.

## Features

### Medicines Management
- **Add/Update Medicines**: Add new medicines with details including:
  - Medicine name and image
  - Quantity and pricing (Rate, MRP)
  - Batch number and expiry date
  - Manufacturer (Brand) and Category
  - Status (Available/Not Available)

- **View Medicines**: Display all medicines in a table format with:
  - Medicine details and image
  - Expiry date status (highlighted for expired medicines)
  - Edit and delete actions

### Categories Management
- **Add Categories**: Create new medicine categories
- **View Categories**: Display all categories with edit/delete options
- **Edit Categories**: Modify existing category names and status

## Setup Instructions

### 1. Database Setup
1. Import the main database schema: `database/mdlink.sql`
2. Run the additional tables script: `database/create_missing_tables.sql`
   ```sql
   mysql -u root -p mdlink < database/create_missing_tables.sql
   ```

### 2. File Structure
The application is organized as follows:
- `dawapharma/` - Main application directory
- `dawapharma/add-product.php` - Add/Update medicines form
- `dawapharma/categories.php` - View categories
- `dawapharma/add-category.php` - Add new categories
- `dawapharma/product.php` - View all medicines
- `dawapharma/php_action/` - Backend processing scripts
- `dawapharma/custom/js/` - JavaScript functionality

### 3. Access URLs
- **Add/Update Medicines**: `http://localhost/MdLink%20Rwanda/dawapharma/placeholder.php?title=Add%20%2F%20Update%20Medicines`
- **Categories**: `http://localhost/MdLink%20Rwanda/dawapharma/placeholder.php?title=Categories`

## Database Tables

### Categories Table
- `categories_id` - Primary key
- `categories_name` - Category name
- `categories_active` - Active status (1=Active, 2=Inactive)
- `categories_status` - Record status (1=Active, 2=Deleted)

### Brands Table
- `brand_id` - Primary key
- `brand_name` - Manufacturer/brand name
- `brand_active` - Active status
- `brand_status` - Record status

### Product Table
- `product_id` - Primary key
- `product_name` - Medicine name
- `product_image` - Image file path
- `brand_id` - Foreign key to brands table
- `categories_id` - Foreign key to categories table
- `quantity` - Available stock
- `rate` - Selling price
- `mrp` - Maximum retail price
- `bno` - Batch number
- `expdate` - Expiry date
- `active` - Product availability status
- `status` - Record status

## Sample Data

The setup script includes sample categories and brands:
- **Categories**: Pain Relief, Antibiotics, Vitamins, Diabetes, Hypertension, Cough & Cold, Digestive Health, Skin Care
- **Brands**: Pfizer, Johnson & Johnson, GSK, Novartis, Roche, Merck, AstraZeneca, Sanofi

## Usage

### Adding a New Medicine
1. Navigate to Add/Update Medicines
2. Fill in the form with medicine details
3. Upload an image (optional)
4. Select manufacturer and category from dropdowns
5. Set quantity, pricing, and expiry date
6. Submit the form

### Adding a New Category
1. Navigate to Categories
2. Click "Add Categories" button
3. Enter category name
4. Select status (Available/Not Available)
5. Submit the form

### Managing Existing Records
- Use the edit buttons to modify existing medicines or categories
- Use the delete buttons to remove records (with confirmation)

## Technical Notes

- The application uses PHP with MySQL database
- JavaScript validation is included for form fields
- Image uploads are stored in `assets/myimages/` directory
- Foreign key constraints ensure data integrity
- Responsive design using Bootstrap framework

## Troubleshooting

### Common Issues
1. **Database Connection**: Ensure MySQL is running and credentials are correct
2. **Missing Tables**: Run the `create_missing_tables.sql` script if tables don't exist
3. **Image Uploads**: Check directory permissions for `assets/myimages/`
4. **Form Submission**: Ensure all required fields are filled and validation passes

### File Permissions
Make sure the web server has read/write access to:
- `assets/myimages/` - For image uploads
- `php_action/` - For backend processing

## Support

For technical support or questions about the application, please refer to the code comments or contact the development team.
