PharmaDesk
PharmaDesk is a comprehensive pharmacy management system that handles inventory, stock, sales, and accounts, tailored to streamline daily operations in a pharmacy and related management processes.

Features
Stock Management: Efficient tracking of medicine and product stock levels.

Inventory Reports: Generate detailed stock reports including stock-in and stock-out data.

Purchases & Sales: Manage purchases from suppliers and sales to customers.

Accounts & Payroll: Track and manage income, expenses, and payroll for employees.

Vendor Management: Keep track of vendor payments and purchase orders.

Export to Excel/PDF: Export stock reports and other data to Excel and PDF formats.

Event Management: Schedule and manage important pharmacy events.

Installation
Follow the steps below to set up PharmaDesk on your local machine.

Prerequisites
PHP >= 8.2

Laravel >= 12.x

Node.js >= 14.x

Composer

MySQL or any preferred database

npm or yarn

1. Clone the Repository

git clone https://github.com/dullahmagic23/pharmadesk.git
cd pharmadesk
2. Install Backend Dependencies
Run the following command to install Laravel backend dependencies:
composer install
3. Configure Environment Variables
Duplicate the .env.example file and name it .env. Update the database connection and other environment variables.

cp .env.example .env
4. Set Up the Database
Create a new database and run migrations to set up the necessary tables.

php artisan migrate
5. Install Frontend Dependencies
For the frontend, we use Vue.js. Install the frontend dependencies with npm:

npm install
6. Set Up the Application Key
Generate an application key for Laravel.

bash
Copy
Edit
php artisan key:generate
7. Run the Application
Now you can run the application locally with:

php artisan serve
Frontend assets:

npm run dev
The app will be available at http://localhost:8000.

Usage
Stock Management
Add, update, and remove medicines and products in the stock.

View stock history with the ability to track stock in and out.

Reports
Generate reports for stock management and vendor transactions.

Export data in Excel or PDF formats for easy reporting and analysis.
Accounting & Payroll
Track sales and expenses.
Generate payroll reports for employees.
Vendor Management
Manage purchase orders, vendor payments, and purchase histories.
Events
Schedule and manage important pharmacy events, such as stock arrivals or meetings.

Contributing
We welcome contributions to PharmaDesk! If you'd like to help improve the project, follow these steps:

Fork the repository.
Create a new branch for your feature (git checkout -b feature/your-feature).

Commit your changes (git commit -am 'Add new feature').

Push to the branch (git push origin feature/your-feature).
Create a new pull request.

License
PharmaDesk is open-source software licensed under the MIT License.
