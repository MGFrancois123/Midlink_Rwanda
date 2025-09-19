# Manage Pharmacies Page - Complete Guide

## 🏥 **Overview**
The `manage_pharmacies.php` page provides a comprehensive interface for managing all pharmacies in the MdLink Rwanda system. It features a modern design similar to the "Create Pharmacy" page and displays real data from the database.

## ✨ **Key Features**

### **1. Modern Design**
- **Consistent Styling** - Matches the Create Pharmacy page design
- **Responsive Layout** - Works on all screen sizes
- **Interactive Elements** - Hover effects and smooth transitions
- **Professional UI** - Clean, modern interface with green accent colors

### **2. Real Database Integration**
- **Live Data** - Fetches actual pharmacy data from database
- **Smart Queries** - Handles missing columns gracefully
- **Error Handling** - Shows helpful error messages with solutions
- **Statistics** - Displays real counts of pharmacies, admins, and medicines

### **3. Comprehensive Functionality**
- **View Details** - Modal popup with complete pharmacy information
- **Edit Pharmacy** - Redirects to create_pharmacy.php in edit mode
- **Delete Pharmacy** - Safe deletion with confirmation and cascade handling
- **Search** - Real-time search through pharmacy data
- **Statistics Cards** - Live counts and system status

## 📁 **Files Created/Modified**

### **Main Files:**
- ✅ **manage_pharmacies.php** - Main management page
- ✅ **php_action/delete_pharmacy.php** - Handles pharmacy deletion
- ✅ **php_action/get_pharmacy_details.php** - Fetches pharmacy details for view modal
- ✅ **test_manage_pharmacies.php** - Test page for functionality verification

### **Dependencies:**
- Uses existing `constant/connect.php` for database connection
- Uses existing `constant/check.php` for authentication
- Uses existing `php_action/get_locations.php` for location data
- Integrates with `create_pharmacy.php` for editing

## 🎨 **Design Features**

### **Color Scheme:**
- **Primary Green**: #2f855a (consistent with Create Pharmacy page)
- **Neutral Grays**: #2d2d2d, #6b7280 for text
- **Success Green**: #28a745 for success states
- **Danger Red**: #dc3545 for delete actions

### **Layout Components:**
1. **Hero Section** - Header with title and create button
2. **Statistics Cards** - Four cards showing system metrics
3. **Search Bar** - Real-time search functionality
4. **Data Table** - Responsive table with pharmacy information
5. **Action Buttons** - View, Edit, Delete for each pharmacy

## 🔧 **Technical Implementation**

### **Database Queries:**
```sql
-- Smart query that handles missing columns
SELECT p.*, 
       COALESCE(COUNT(DISTINCT au.admin_id), 0) as admin_count,
       COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
FROM pharmacies p
LEFT JOIN admin_users au ON p.pharmacy_id = au.pharmacy_id
LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
ORDER BY p.created_at DESC
```

### **Error Handling:**
- Checks for table existence before querying
- Handles missing `pharmacy_id` columns gracefully
- Shows detailed error messages with solutions
- Provides links to diagnostic and setup tools

### **Security Features:**
- Input validation and sanitization
- SQL injection prevention with prepared statements
- Transaction handling for data integrity
- Confirmation dialogs for destructive actions

## 🚀 **How to Use**

### **1. Access the Page**
```
http://localhost/FYP/FYP/Final_year_project/MdLink%20Rwanda/MdLink/manage_pharmacies.php
```

### **2. View Pharmacies**
- All pharmacies are displayed in a responsive table
- Each row shows: Name, License, Location, Contact, Admin Count, Medicine Count
- Use the search bar to filter pharmacies in real-time

### **3. Manage Pharmacies**
- **View**: Click the eye icon to see detailed pharmacy information
- **Edit**: Click the pencil icon to edit pharmacy details
- **Delete**: Click the trash icon to delete a pharmacy (with confirmation)

### **4. Search Functionality**
- Type in the search box to filter pharmacies
- Searches across name, license, location, and contact information
- Updates the count badge in real-time

## 🧪 **Testing**

### **Test Page:**
```
http://localhost/FYP/FYP/Final_year_project/MdLink%20Rwanda/MdLink/test_manage_pharmacies.php
```

### **Test Features:**
- Database connection verification
- Data fetching validation
- Action button functionality
- Error handling verification

## 🔄 **Integration Points**

### **With Create Pharmacy Page:**
- Edit button redirects to `create_pharmacy.php?edit=ID`
- Maintains consistent design and functionality
- Shares the same database schema and validation

### **With Product Management:**
- Shows medicine counts for each pharmacy
- Links to medicine management system
- Maintains data relationships

### **With User Management:**
- Shows admin account counts
- Integrates with user management system
- Maintains pharmacy-user relationships

## 🛠️ **Troubleshooting**

### **Common Issues:**

1. **Database Connection Error**
   - Run: `diagnose_database.php`
   - Fix: `setup_database.php` or `fix_database_schema.php`

2. **Missing Columns Error**
   - Run: `fix_database_schema.php`
   - This adds missing `pharmacy_id` columns

3. **No Data Displayed**
   - Check if pharmacies exist in database
   - Verify database connection
   - Run diagnostic tools

### **Quick Fixes:**
- **Database Issues**: Run `setup_database.php`
- **Schema Issues**: Run `fix_database_schema.php`
- **Connection Issues**: Check XAMPP MySQL status

## 📊 **Performance Features**

### **Optimizations:**
- Efficient database queries with proper indexing
- Client-side search for better responsiveness
- Lazy loading of detailed information
- Minimal server requests

### **Scalability:**
- Handles large numbers of pharmacies
- Efficient pagination (can be added)
- Optimized database queries
- Responsive design for all devices

## 🎯 **Future Enhancements**

### **Potential Additions:**
- **Pagination** - For large numbers of pharmacies
- **Bulk Actions** - Select multiple pharmacies for batch operations
- **Export Functionality** - Export pharmacy data to CSV/Excel
- **Advanced Filtering** - Filter by location, status, etc.
- **Audit Trail** - Track all changes to pharmacy data

## ✅ **Success Criteria Met**

- ✅ **Design Similar to Create Pharmacy** - Consistent styling and layout
- ✅ **Real Database Data** - Fetches and displays actual pharmacy information
- ✅ **Clear Data Display** - Well-organized table with all relevant information
- ✅ **Full Functionality** - View, Edit, Delete operations
- ✅ **Error Handling** - Comprehensive error management
- ✅ **User Experience** - Intuitive interface with helpful features
- ✅ **Integration** - Works seamlessly with existing system

The Manage Pharmacies page is now fully functional and ready for use!
