# 🎨 Dashboard Logo Customization Guide

## ✅ **CUSTOMIZATION COMPLETE!**

Your dashboard now supports custom logos and branding. Here's how to customize it:

---

## 🖼️ **HOW TO ADD YOUR CUSTOM LOGO:**

### **Option 1: Add Your Logo File**
1. **Save your logo** as `logo.png` in the `assets/uploadImage/Logo/` folder
2. **Recommended size**: 200x200 pixels (will be automatically resized)
3. **Format**: PNG with transparent background works best
4. **The logo will automatically appear** in the dashboard header

### **Option 2: Use Different Logo Path**
If you want to use a different logo file or path, edit this line in `dashboard_super.php`:
```html
<img src="assets/uploadImage/Logo/logo.png" alt="MdLink Rwanda Logo" class="custom-logo">
```
Change `"assets/uploadImage/Logo/logo.png"` to your logo's path.

---

## 📝 **HOW TO CHANGE THE TEXT:**

### **Current Text:**
- **Title**: "MdLink Rwanda"
- **Subtitle**: "Super Admin Dashboard - Complete Pharmacy Management System"

### **To Change Text:**
Edit these lines in `dashboard_super.php`:
```html
<h1 class="dashboard-title">
    MdLink Rwanda  <!-- Change this to your company name -->
</h1>
<p class="dashboard-subtitle">
    Super Admin Dashboard - Complete Pharmacy Management System  <!-- Change this -->
</p>
```

---

## 🎨 **CUSTOMIZATION OPTIONS:**

### **✅ Logo Styling:**
- **Size**: Automatically resized to 80px (60px on mobile)
- **Shape**: Circular with white background
- **Shadow**: Professional drop shadow
- **Fallback**: Shows hospital icon if logo not found

### **✅ Text Styling:**
- **Title**: Large, bold, white text
- **Subtitle**: Smaller, lighter text
- **Responsive**: Adjusts size on mobile devices

### **✅ Layout:**
- **Desktop**: Logo on left, text on right
- **Mobile**: Logo on top, text below (centered)
- **Professional**: Clean, modern design

---

## 🚀 **EXAMPLES OF CUSTOMIZATION:**

### **Example 1: Company Logo**
```
Title: "Your Company Name"
Subtitle: "Pharmacy Management System"
Logo: your-company-logo.png
```

### **Example 2: Medical Logo**
```
Title: "MediCare Rwanda"
Subtitle: "Advanced Healthcare Management"
Logo: medical-logo.png
```

### **Example 3: Simple Text**
```
Title: "Pharmacy Pro"
Subtitle: "Complete Management Solution"
Logo: (uses fallback hospital icon)
```

---

## 🎯 **RESULT:**

Your dashboard now has:
- ✅ **Custom Logo Support** - Add your own logo
- ✅ **Customizable Text** - Change title and subtitle
- ✅ **Professional Design** - Clean, modern appearance
- ✅ **Responsive Layout** - Works on all devices
- ✅ **Fallback System** - Shows icon if logo not found

**Your dashboard is now fully customizable and ready for your branding!** 🎉
