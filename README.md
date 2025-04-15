Mini Task Collaboration App:

Description:

This is a Mini Task Collaboration App developed for a Web Developer Internship assessment. The application allows users to manage tasks collaboratively. It features two types of users: Admin and User. The Admin can view and delete tasks, while the User can create tasks. This app includes user authentication, task management, AJAX interactivity, and a MySQL database.

Features:
User Authentication: Admin and User login functionality.
Admin Dashboard: Admin can view all tasks and delete tasks, but cannot assign tasks.
User Dashboard: Users can create tasks and view tasks assigned to them.
Task Management: Admin can manage tasks by deleting them, and users can create tasks.
AJAX Interactivity: Real-time updates and seamless user experience.
Preloader Animation: A custom preloader animation appears when loading the dashboard page.
Technologies Used

Frontend: HTML, CSS, JavaScript, AJAX
Backend: PHP
Database: MySQL
Libraries: GSAP for animations
Preloader Animation: Custom animation showing a running figure on a progress bar with the text: "Hold Up Tight, Let's Plan Tasks."
Installation

To run this app on your local machine, follow the instructions below:

Clone the repository:
git clone https://github.com/your-username/mini-task-collaboration-app.git
Navigate into the project folder:
cd mini-task-collaboration-app
Create a MySQL database and import the schema (database.sql).
Update the database connection settings in config.php:
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'task_collaboration');
Open the project in your browser (if using PHP's built-in server):
php -S localhost:8000

How to Use:

Admin Login
Navigate to the login page.
Use the admin credentials to log in (default: admin/admin).
Mail:admin@gmail.com,Password:Admin@123
Upon successful login, you'll be redirected to the Admin Dashboard where you can:
View all tasks.
Delete tasks.
Admin cannot assign tasks.
User Login:
Navigate to the login page.
Use the user credentials to log in (default: user/user).
Mail:user@gmail.com,Password:User@123
After logging in, users can:
Create new tasks.
View tasks assigned to them.
Update the status of their tasks.
Task Management
Admin can view and delete tasks, but cannot assign them.
User can create new tasks and view their assigned tasks.
Future Improvements

Add user registration functionality.
Implement task prioritization and categorization.
Enhance task filtering and searching features.
Improve security features like password hashing.
License

This project is open-source and available under the MIT License.