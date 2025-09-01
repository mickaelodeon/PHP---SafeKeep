# Role-Based Navigation Structure - SafeKeep

## Navigation Headers by Role

### 🎓 Student Role Navigation
When a student successfully logs in, they will see this consistent navigation across all pages:
- **Dashboard** (active on dashboard.php)
- **Browse Items** (active on browse.php)  
- **Messages** (active on messages.php)
- **Profile Dropdown** containing:
  - Profile
  - Logout

### 👨‍💼 Admin Role Navigation  
When an admin successfully logs in, they will see this consistent navigation across all pages:
- **Admin Panel** (active on admin.php)
- **User Dashboard** (link to dashboard.php)
- **Browse Items** (active on browse.php)
- **Profile Dropdown** containing:
  - Profile
  - Admin Panel (if accessed from non-admin pages)
  - Logout

### 👤 Guest (Not Logged In) Navigation
For visitors who haven't logged in:
- **Home** 
- **Browse Items**
- **Login**
- **Register**

## Implementation Details

### Files Updated for Consistent Navigation:

1. **dashboard.php** ✅
   - Student navigation: Dashboard | Browse Items | Messages | Profile
   - Added PHP session check and variables

2. **browse.php** ✅
   - Dynamic navigation that switches based on authentication
   - Student: Dashboard | Browse Items | Messages | Profile
   - Admin: Admin Panel | User Dashboard | Browse Items | Profile
   - Guest: Home | Browse Items | Login | Register

3. **messages.php** ✅
   - Student navigation: Dashboard | Browse Items | Messages | Profile
   - Added PHP session check and variables

4. **admin.php** ✅
   - Admin navigation: Admin Panel | User Dashboard | Browse Items | Profile
   - Added PHP session check and variables

5. **profile.php** ✅
   - Student navigation: Dashboard | Browse Items | Messages | Profile
   - Already has proper PHP session handling

## Key Features:

### ✅ Session Management
- All pages now check user authentication status
- Automatic redirect to login if not authenticated
- Role-based access control (admin.php requires admin role)

### ✅ Dynamic Navigation
- browse.php intelligently switches navigation based on user role
- JavaScript handles showing/hiding appropriate navigation items
- Consistent experience regardless of entry point

### ✅ Active States
- Current page is highlighted with "active" class
- Users always know where they are in the system

### ✅ Role-Aware Dropdowns
- Profile dropdown shows appropriate options based on role
- Admin Panel link only visible to administrators
- Consistent logout functionality across all pages

## Navigation Flow Examples:

### Student Login Journey:
1. Student logs in from login.html
2. Redirected to dashboard.php with navigation: Dashboard | Browse Items | Messages | Profile
3. Clicks "Browse Items" → browse.php shows: Dashboard | Browse Items | Messages | Profile
4. Clicks "Messages" → messages.php shows: Dashboard | Browse Items | Messages | Profile
5. Navigation remains consistent across all pages

### Admin Login Journey:
1. Admin logs in from login.html  
2. Redirected to admin.php with navigation: Admin Panel | User Dashboard | Browse Items | Profile
3. Clicks "Browse Items" → browse.php shows: Admin Panel | User Dashboard | Browse Items | Profile
4. Clicks "User Dashboard" → dashboard.php shows: Dashboard | Browse Items | Messages | Profile (with Admin Panel in dropdown)
5. Navigation adapts appropriately while maintaining admin access

## Technical Implementation:

- **PHP Session Variables**: $username, $role available on all authenticated pages
- **JavaScript Role Detection**: browse.php uses API to determine current user role
- **Bootstrap Navigation**: Consistent styling and responsive design
- **Security**: All authenticated pages verify session before rendering content

This ensures that users see the correct navigation header based on their role, and it remains consistent as they navigate through the system.
