# University Lost & Found - Staff Member Package

This ZIP is the separate group-member package for the **Staff** role.

## Role work
Handles the Staff side: verifying reports, reviewing approved claims for handover, and marking items returned.

### Distinct features
- Verify/Reject Item Reports
- Review Admin-Approved Claims
- Mark Item as Returned
- View Pending Reports

## Demo account
University ID: `21-40001-1`
Password: `123456`

## XAMPP run
1. Copy this folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open `http://localhost/phpmyadmin/`.
4. Import `database/university_lost_found_db.sql`.
5. Open `http://localhost/UniversityLostFound_Staff_Member/`.
6. Login with the demo account above.

## MVC
- `View/` = pages shown to the user
- `Controller/` = receives form/AJAX requests
- `Model/` = database operations

## Group merge
Each package contains the common MVC files needed by this role. When combining the four members' work, keep one common `Model/`, `assets/`, `View/login.php`, `View/registration.php`, and shared authentication controllers. Then merge the role-specific controllers and dashboard sections into the final project.

## Database
All four packages use the same database name: `university_lost_found_db`. Do not create four different databases when the final group project is merged.
