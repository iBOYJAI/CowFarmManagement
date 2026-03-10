# Cow Farm Management System

A complete offline web application for managing cow farms, built with PHP, MySQL, HTML, CSS, and JavaScript. This system runs entirely on XAMPP without requiring internet connectivity.

## Features

- **Authentication & Role-Based Access Control**
  - Login/Logout system
  - Roles: Admin, Vet, Manager, Staff
  - Secure session management

- **Dashboard**
  - Key Performance Indicators (KPIs)
  - Recent activity logs
  - Top milk producers
  - Upcoming appointments

- **Cow Profiles**
  - Complete CRUD operations
  - Photo upload functionality
  - Tag number management
  - Breed, DOB, weight tracking
  - Lineage tracking (sire/dam)

- **Health & Vaccination**
  - Medical history records
  - Vaccination schedule tracking
  - Treatment records
  - Reminders for due vaccinations

- **Breeding & Pregnancy**
  - AI/Natural breeding records
  - Expected calving dates
  - Pregnancy status tracking
  - Calving history

- **Milk Production**
  - Daily milk yield logging
  - Morning/Evening sessions
  - Per-cow and herd reports
  - Production statistics

- **Feed & Inventory**
  - Feed type management
  - Stock tracking
  - Consumption logs
  - Low stock alerts

- **Staff & Users**
  - User management (Admin/Manager only)
  - Role assignment
  - User status control

- **Expenses & Sales**
  - Expense tracking by category
  - Milk sales records
  - Payment status tracking
  - Financial reports

- **Appointments**
  - Vet visit scheduling
  - Appointment status tracking
  - Purpose and notes

- **Alerts & Due Lists**
  - Upcoming vaccinations
  - Overdue vaccinations
  - Expected calvings
  - Due date reminders

- **Reports**
  - Milk production reports
  - Health records reports
  - Financial reports
  - Breeding reports
  - CSV export functionality

- **Database Backup/Restore**
  - Manual backup creation
  - Backup file management

## Requirements

- XAMPP (PHP 7.4+ and MySQL 5.7+)
- Web browser (Chrome, Firefox, Edge, etc.)

## Installation

1. **Install XAMPP**
   - Download and install XAMPP from https://www.apachefriends.org/
   - Start Apache and MySQL services from XAMPP Control Panel

2. **Extract Project Files**
   - Copy the entire project folder to `C:\xampp\htdocs\CowFarmManagement\`
   - Or extract to your desired location and update the BASE_URL in `config/config.php`

3. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database/schema.sql` file
   - This will create the database and all required tables with sample data

4. **Configure Database Connection**
   - Edit `config/database.php` if your MySQL credentials differ from default:
     - Default: username = 'root', password = '' (empty)

5. **Set Permissions**
   - Ensure the following directories are writable:
     - `uploads/cow_photos/`
     - `reports/`
     - `backups/`

6. **Access the Application**
   - Open your browser and navigate to: `http://localhost/CowFarmManagement/`
   - Default login credentials:
     - Username: `admin`
     - Password: `admin123`

## Default Users

The system comes with pre-configured users:

- **Admin**: admin / admin123
- **Vet**: vet1 / admin123
- **Manager**: manager1 / admin123
- **Staff**: staff1 / admin123

**Note**: All default passwords are `admin123`. Change them after first login for security.

## Project Structure

```
CowFarmManagement/
├── assets/
│   ├── css/
│   │   ├── style.css          # Main stylesheet
│   │   └── icons.css          # Icon styles
│   └── js/
│       └── main.js            # Main JavaScript
├── classes/
│   ├── Auth.php               # Authentication class
│   ├── Database.php           # Database helper
│   ├── FileUpload.php         # File upload handler
│   └── Helper.php             # Utility functions
├── config/
│   ├── config.php             # Application configuration
│   └── database.php          # Database connection
├── cows/
│   ├── index.php             # Cow list
│   ├── add.php               # Add cow
│   ├── edit.php              # Edit cow
│   ├── view.php              # View cow details
│   └── delete.php            # Delete cow
├── health/
│   ├── index.php             # Health records list
│   └── add.php               # Add health record
├── milk/
│   ├── index.php             # Milk production list
│   └── add.php               # Add milk record
├── breeding/
│   ├── index.php             # Breeding records
│   └── add.php               # Add breeding record
├── feed/
│   └── index.php             # Feed inventory
├── users/
│   └── index.php             # User management
├── expenses/
│   └── index.php             # Expenses & sales
├── appointments/
│   └── index.php             # Appointments
├── alerts/
│   └── index.php             # Alerts & due lists
├── reports/
│   └── index.php             # Reports
├── includes/
│   ├── header.php            # Header component
│   └── footer.php            # Footer component
├── database/
│   └── schema.sql            # Database schema
├── uploads/
│   └── cow_photos/           # Cow photos storage
├── reports/                  # Generated reports
├── backups/                  # Database backups
├── dashboard.php             # Main dashboard
├── login.php                  # Login page
├── logout.php                 # Logout handler
├── settings.php               # Settings page
└── README.md                  # This file
```

## Security Features

- Password hashing using PHP's `password_hash()`
- Prepared statements to prevent SQL injection
- Input sanitization and validation
- Session-based authentication
- Role-based access control
- File upload validation
- XSS protection through output escaping

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Edge
- Safari
- Opera

## Troubleshooting

### Database Connection Error
- Check if MySQL is running in XAMPP
- Verify database credentials in `config/database.php`
- Ensure database `cow_farm_db` exists

### File Upload Not Working
- Check directory permissions for `uploads/cow_photos/`
- Verify `upload_max_filesize` in php.ini (default: 5MB)

### Session Issues
- Ensure cookies are enabled in your browser
- Check PHP session configuration

### Page Not Found (404)
- Verify BASE_URL in `config/config.php` matches your installation path
- Check Apache rewrite module is enabled (if using .htaccess)

## Development Notes

- All code is written in plain PHP (no frameworks)
- Uses vanilla JavaScript (no external libraries)
- CSS is self-contained (no CDN dependencies)
- Icons are inline SVG (no external icon fonts)
- Fully offline capable

## License

This project is provided as-is for educational and commercial use.

## Support

For issues or questions, please refer to the code comments or documentation within the project files.

---

**Version**: 1.0.0  
**Last Updated**: 2024

