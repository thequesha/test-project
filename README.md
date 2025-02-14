# Resource Booking System

A Laravel-based resource booking and management system that allows users to schedule and manage bookings for various resources.

## Features

- Resource Management
  - Create and manage different types of resources
  - Track resource availability
  - View resource booking history

- Booking System
  - Check resource availability for specific time periods
  - Create, update, and cancel bookings
  - Prevent double-booking conflicts
  - Exclude specific bookings when checking availability (useful for updates)

## System Requirements

- PHP >= 8.0
- Laravel Framework
- MySQL/PostgreSQL Database
- Composer

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd test-project
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

## Usage

### Resource Management

The system provides a `ResourceService` class that handles all resource-related operations:

```php
// Check resource availability
$isAvailable = $resourceService->isAvailable($resource, $startTime, $endTime);

// Get resource bookings
$bookings = $resourceService->getBookings($resource);
```

### Booking Management

Use the `BookingService` class to manage bookings:

```php
// Create a new booking
$booking = $bookingService->create([
    'resource_id' => $resourceId,
    'start_time' => $startTime,
    'end_time' => $endTime
]);
```

## Architecture

The project follows a repository pattern with clear separation of concerns:

- **Services**: Business logic layer (`ResourceService`, `BookingService`)
- **Repositories**: Data access layer with interfaces for better testability
- **Models**: Eloquent models representing database entities

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

