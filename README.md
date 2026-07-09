# UniReserve Facility Booking System

Welcome to the **UniReserve Facility Booking System**! This is a comprehensive web-based application designed to help students easily book university facilities, such as Badminton Courts, Basketball Courts, and more. It also provides a robust administrative dashboard for staff and admins to manage users, facilities, and maintenance reports.

## Features
- **Student Dashboard:** Easy-to-use interface for students to browse and book available facilities.
- **Maintenance Reporting:** Students can seamlessly report issues with facilities (e.g., broken equipment), and track the status of their reports.
- **Admin/Staff Dashboard:** Powerful backend for administrators to manage bookings, resolve maintenance reports, and oversee users.
- **Responsive Design:** A beautiful, dark-themed, and mobile-friendly interface built with modern CSS and fluid typography.
- **Audit Logs:** Secure tracking of administrative actions for accountability.

## Installation & Setup

1. **Clone the Repository**
   Download or clone this repository to your local web server environment (e.g., Laragon, XAMPP).
   
2. **Database Setup**
   - Create a new MySQL database named `facility_booking`.
   - Import the provided `database.sql` file into this new database using phpMyAdmin or your preferred database tool.

3. **Configuration**
   Open `config.php` and ensure the database connection credentials (username, password, database name) match your local setup.

4. **Run the Application**
   Navigate to the project folder in your web browser (e.g., `http://localhost/facility_booking`).

## Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP (PDO)
- **Database:** MySQL
