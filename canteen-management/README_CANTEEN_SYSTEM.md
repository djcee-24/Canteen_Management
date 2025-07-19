# Centralized Canteen E-Commerce Management System

A comprehensive Laravel-based management system for canteen operations with multi-panel Filament administration, role-based access control, and real-time order management.

## 🚀 Features

### Core Features
- **Multi-User System**: Admin (Concessionaire), Tenants, Staff, Customers, and Guests
- **Menu Management**: Category-based menu system with item ownership
- **Order Management**: Real-time order processing and tracking
- **Financial Management**: Expense tracking and tenant rental fee management
- **Staff Management**: Attendance tracking and salary calculation
- **Real-time Updates**: Laravel Reverb for live order status updates
- **Role-based Access Control**: Spatie Laravel Permission integration

### User Roles

#### 👑 Admin (Concessionaire)
- Manages the entire system
- Can sell food and manage all menus
- Handles tenant rentals and staff management
- Access to comprehensive analytics and reports
- System-wide administrative privileges

#### 🏪 Tenants
- Manage their own menu items
- Process orders for their products
- Track their sales and expenses
- Limited administrative access to their own data

#### 👥 Staff
- Assist with order processing
- Track their own attendance
- Limited system access for operational tasks

#### 🛒 Customers
- Browse and place orders
- View order history
- Manage their profile and preferences

#### 👤 Guests
- Can only place online orders
- No on-site payment options
- Limited system access

## 🛠 Technology Stack

- **Backend**: Laravel 12.20
- **Admin Panel**: Filament v3 (Multi-panel setup)
- **Authentication**: Laravel Jetstream + Laravel Fortify
- **Permissions**: Spatie Laravel Permission
- **Real-time**: Laravel Reverb + Echo
- **Frontend**: Livewire + Tailwind CSS
- **Database**: MySQL (via XAMPP)
- **IDE**: VS Code

## 📋 System Requirements

- PHP 8.4+
- Composer
- Node.js & NPM
- MySQL 8.0+
- Apache/Nginx web server

## 🚀 Installation & Setup

### 1. Clone and Install Dependencies

```bash
git clone <repository-url>
cd canteen-management
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=canteen_management
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=reverb
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 3. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE canteen_management;
exit;

# Run migrations and seeders
php artisan migrate
php artisan db:seed
```

### 4. Permissions Setup

```bash
# Publish Spatie Permission migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Publish Filament assets
php artisan filament:install --panels=admin
```

### 5. Build Assets

```bash
npm run dev
# or for production
npm run build
```

### 6. Start the Application

```bash
# Start Laravel development server
php artisan serve

# Start Laravel Reverb (in a separate terminal)
php artisan reverb:start

# Start Vite development server (in a separate terminal)
npm run dev
```

## 👥 Default User Accounts

After running the seeders, the following accounts will be available:

| Role | Email | Password | Description |
|------|-------|----------|-------------|
| Admin | admin@canteen.com | password | System administrator |
| Tenant | tenant@canteen.com | password | Sample food vendor |
| Staff | staff@canteen.com | password | Staff member |
| Customer | customer@canteen.com | password | Customer account |

## 📊 Database Schema

### Core Tables

#### Users
- Extended user model with role-specific fields
- Support for tenant rental rates and staff hourly rates
- User type classification (admin, tenant, customer, staff, guest)

#### Menu System
- `menu_categories`: Organize menu items
- `menu_items`: Individual food items with ownership tracking
- Support for dietary information, allergens, and nutritional data

#### Order Management
- `orders`: Order header with customer and payment information
- `order_items`: Individual items within orders
- Status tracking (pending → confirmed → preparing → ready → completed)

#### Financial Management
- `tenant_rentals`: Monthly rental fee tracking
- `expenses`: Business expense categorization
- `staff_attendance`: Attendance and salary calculation

## 🔒 Permissions System

### Permission Categories

#### User Management
- `view users`, `create users`, `edit users`, `delete users`

#### Menu Management
- `view menu categories`, `create menu categories`, `edit menu categories`, `delete menu categories`
- `view menu items`, `create menu items`, `edit menu items`, `delete menu items`, `edit own menu items`

#### Order Management
- `view orders`, `create orders`, `edit orders`, `delete orders`, `process orders`, `view own orders`

#### Financial Management
- `view expenses`, `create expenses`, `edit expenses`, `delete expenses`
- `view tenant rentals`, `create tenant rentals`, `edit tenant rentals`, `delete tenant rentals`

#### Staff Management
- `view staff attendance`, `create staff attendance`, `edit staff attendance`, `delete staff attendance`, `view own attendance`

#### Dashboard Access
- `view admin dashboard`, `view tenant dashboard`, `view reports`, `export reports`

#### System Administration
- `manage settings`, `manage system`

## 🎯 Key Features Implementation

### Multi-Panel Filament Setup

The system uses separate Filament panels for different user types:

1. **Admin Panel** (`/admin`): Full system access for administrators
2. **Tenant Panel** (`/tenant`): Limited access for food vendors

### Real-time Order Updates

- Laravel Reverb provides WebSocket connections
- Order status changes broadcast to relevant users
- Live updates for kitchen staff and customers

### Role-based Menu Ownership

- Menu items are owned by specific users (tenants)
- Admins can manage all menu items
- Tenants can only manage their own items

### Financial Tracking

- Automatic rental fee generation for tenants
- Expense categorization and reporting
- Staff salary calculation based on attendance

### Guest Order Restrictions

- Guests can only place online orders
- No cash payment option for guest users
- Simplified checkout process

## 🔧 Configuration

### Filament Panels

Configure panels in `app/Providers/Filament/`:
- `AdminPanelProvider.php`: Admin panel configuration
- `TenantPanelProvider.php`: Tenant panel configuration

### Broadcasting

Configure Laravel Reverb in `config/broadcasting.php` and ensure WebSocket connections are properly set up.

### Permissions

Role and permission assignments are managed in `database/seeders/RolePermissionSeeder.php`.

## 📈 Future Enhancements

### Planned Features
1. **McDonald's-style Menu UI**: Modern, responsive menu interface
2. **Advanced Analytics**: Sales reports, trend analysis
3. **Mobile App Integration**: React Native mobile application
4. **Payment Gateway Integration**: Stripe, PayPal integration
5. **Inventory Management**: Stock tracking and alerts
6. **Customer Loyalty Program**: Points and rewards system
7. **Multi-language Support**: Internationalization
8. **Advanced Reporting**: PDF exports, email reports

### Performance Optimizations
- Redis caching implementation
- Database query optimization
- Image optimization and CDN integration
- API rate limiting

## 🐛 Troubleshooting

### Common Issues

#### Migration Errors
```bash
# Reset migrations if needed
php artisan migrate:fresh --seed
```

#### Permission Issues
```bash
# Clear permission cache
php artisan permission:cache-reset
```

#### Broadcasting Issues
```bash
# Restart Reverb server
php artisan reverb:restart
```

#### Asset Issues
```bash
# Clear and rebuild assets
npm run build
php artisan storage:link
```

## 📝 Development Guidelines

### Code Structure
- Follow Laravel conventions
- Use Eloquent relationships properly
- Implement proper validation
- Write descriptive comments

### Database Design
- Use proper foreign key constraints
- Implement soft deletes where appropriate
- Index frequently queried columns
- Maintain data integrity

### Security Considerations
- Validate all user inputs
- Use CSRF protection
- Implement proper authorization checks
- Sanitize file uploads

## 🤝 Contributing

1. Follow PSR-12 coding standards
2. Write comprehensive tests
3. Document new features
4. Submit pull requests with detailed descriptions

## 📄 License

This project is proprietary software. All rights reserved.

## 🆘 Support

For technical support or questions, please contact the development team.

---

**Version**: 1.0.0  
**Last Updated**: July 2024  
**Developed by**: [Your Development Team]