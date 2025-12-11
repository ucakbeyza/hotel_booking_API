# Hotel API

RESTful API for hotel chain management. Provides full CRUD operations for hotels, rooms, and reservations.

## Features

- Hotel management with full CRUD operations
- Room management with full CRUD operations
- Reservation management with full CRUD operations
- Date conflict validation to prevent overlapping reservations
- Automatic room status updates when reservations are created or cancelled
- Room filtering by hotel ID and date range
- Comprehensive request validation
- Standardized JSON response format

## Requirements

- PHP >= 8.2
- Composer
- MySQL 8.0+ or MariaDB
- Laravel 12.0+

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd hotelAPI
```

2. Install dependencies:
```bash
composer install
```

3. Create environment file:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotelapi
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. (Optional) Seed database:
```bash
php artisan db:seed
```

7. Start the server:
```bash
php artisan serve
```

API will be available at `http://localhost:8000/api`

## API Documentation

### Base URL
```
http://localhost:8000/api
```

---

## Hotels Endpoints

### GET /hotels

Retrieves a list of all hotels in the system.

**Endpoint:** `GET /api/hotels`

**Description:** Returns all hotels with their basic information including ID, name, location, rating, and timestamps.

**Request Example:**
```bash
curl -X GET http://localhost:8000/api/hotels
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": [
    {
      "id": 1,
      "name": "Grand Istanbul Hotel",
      "location": "Istanbul, Turkey",
      "rating": "4.5",
      "created_at": "2025-12-05T19:00:00.000000Z",
      "updated_at": "2025-12-05T19:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Luxury Beach Resort",
      "location": "Antalya, Turkey",
      "rating": "4.8",
      "created_at": "2025-12-05T19:05:00.000000Z",
      "updated_at": "2025-12-05T19:05:00.000000Z"
    }
  ]
}
```

---

### GET /hotels/{id}

Retrieves detailed information about a specific hotel by its ID.

**Endpoint:** `GET /api/hotels/{id}`

**Description:** Returns a single hotel's complete information including all fields.

**Path Parameters:**
- `id` (integer, required): The unique identifier of the hotel

**Request Example:**
```bash
curl -X GET http://localhost:8000/api/hotels/1
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    "id": 1,
    "name": "Grand Istanbul Hotel",
    "location": "Istanbul, Turkey",
    "rating": "4.5",
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T19:00:00.000000Z"
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 404,
    "errorMessage": "No query results for model [App\\Models\\Hotel] 999"
  },
  "data": null,
  "errors": []
}
```

---

### POST /hotels/create

Creates a new hotel in the system.

**Endpoint:** `POST /api/hotels/create`

**Description:** Adds a new hotel with the provided information. All fields are required and validated.

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Grand Istanbul Hotel",
  "location": "Istanbul, Turkey",
  "rating": 4.5
}
```

**Validation Rules:**
- `name` (required): string, maximum 255 characters
- `location` (required): string, maximum 255 characters
- `rating` (required): numeric, minimum 0, maximum 5

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/hotels/create \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Grand Istanbul Hotel",
    "location": "Istanbul, Turkey",
    "rating": 4.5
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    "id": 1,
    "name": "Grand Istanbul Hotel",
    "location": "Istanbul, Turkey",
    "rating": "4.5",
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T19:00:00.000000Z"
  }
}
```

**Error Response (422 Validation Error):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 422,
    "errorMessage": "INPUT_INVALID"
  },
  "data": null,
  "errors": {
    "name": ["The name field is required."],
    "rating": ["The rating must be between 0 and 5."]
  }
}
```

---

### POST /hotels/update

Updates an existing hotel's information.

**Endpoint:** `POST /api/hotels/update`

**Description:** Modifies hotel information. All fields except `id` are optional. Only provided fields will be updated.

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 1,
  "name": "Updated Hotel Name",
  "location": "Updated Location",
  "rating": 4.8
}
```

**Validation Rules:**
- `id` (required): integer, must exist in hotels table
- `name` (optional): string, maximum 255 characters
- `location` (optional): string, maximum 255 characters
- `rating` (optional): numeric, minimum 0, maximum 5

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/hotels/update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "name": "Updated Hotel Name",
    "rating": 4.8
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    "id": 1,
    "name": "Updated Hotel Name",
    "location": "Istanbul, Turkey",
    "rating": "4.8",
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T20:00:00.000000Z"
  }
}
```

---

### POST /hotels/delete

Deletes a hotel from the system.

**Endpoint:** `POST /api/hotels/delete`

**Description:** Removes a hotel. **Important:** Hotels that have associated rooms cannot be deleted. All rooms must be deleted first.

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 1
}
```

**Validation Rules:**
- `id` (required): integer, must exist in hotels table

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/hotels/delete \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "Hotel deleted successfully."
  },
  "data": null
}
```

**Error Response (400 Bad Request):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 400,
    "errorMessage": "Cannot delete hotel with existing rooms."
  },
  "data": null,
  "errors": []
}
```

---

## Rooms Endpoints

### GET /rooms

Retrieves a list of all rooms with optional filtering capabilities.

**Endpoint:** `GET /api/rooms`

**Description:** Returns all rooms. Supports filtering by hotel ID and date range to find available rooms for specific dates.

**Query Parameters:**
- `hotel_id` (optional, integer): Filter rooms by a specific hotel ID
- `start_date` (optional, date): Start date for availability check (format: Y-m-d, e.g., 2025-12-10)
- `end_date` (optional, date): End date for availability check (format: Y-m-d, e.g., 2025-12-15)

**Note:** When both `start_date` and `end_date` are provided, only rooms that are available for the entire date range will be returned.

**Request Examples:**

Get all rooms:
```bash
curl -X GET http://localhost:8000/api/rooms
```

Get rooms for a specific hotel:
```bash
curl -X GET "http://localhost:8000/api/rooms?hotel_id=1"
```

Get available rooms for a date range:
```bash
curl -X GET "http://localhost:8000/api/rooms?start_date=2025-12-10&end_date=2025-12-15"
```

Get available rooms for a specific hotel and date range:
```bash
curl -X GET "http://localhost:8000/api/rooms?hotel_id=1&start_date=2025-12-10&end_date=2025-12-15"
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": [
    {
      "id": 1,
      "hotel_id": 1,
      "room_number": "101",
      "type": "Double",
      "price": "250.00",
      "status": "available",
      "created_at": "2025-12-05T19:00:00.000000Z",
      "updated_at": "2025-12-05T19:00:00.000000Z"
    },
    {
      "id": 2,
      "hotel_id": 1,
      "room_number": "102",
      "type": "Single",
      "price": "150.00",
      "status": "available",
      "created_at": "2025-12-05T19:00:00.000000Z",
      "updated_at": "2025-12-05T19:00:00.000000Z"
    }
  ]
}
```

---

### GET /rooms/{id}

Retrieves detailed information about a specific room by its ID.

**Endpoint:** `GET /api/rooms/{id}`

**Description:** Returns a single room's complete information including all fields.

**Path Parameters:**
- `id` (integer, required): The unique identifier of the room

**Request Example:**
```bash
curl -X GET http://localhost:8000/api/rooms/1
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    "id": 1,
    "hotel_id": 1,
    "room_number": "101",
    "type": "Double",
    "price": "250.00",
    "status": "available",
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T19:00:00.000000Z"
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 404,
    "errorMessage": "No query results for model [App\\Models\\Room] 999"
  },
  "data": null,
  "errors": []
}
```

---

### POST /rooms/create

Creates a new room in the system.

**Endpoint:** `POST /api/rooms/create`

**Description:** Adds a new room to a hotel. All fields are required and validated.

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "hotel_id": 1,
  "room_number": "101",
  "type": "Double",
  "price": 250.00,
  "status": "available"
}
```

**Validation Rules:**
- `hotel_id` (required): integer, must exist in hotels table
- `room_number` (required): string, maximum 50 characters
- `type` (required): string, maximum 100 characters (e.g., "Single", "Double", "Suite")
- `price` (required): numeric, minimum 0
- `status` (required): string, must be either "available" or "booked"

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/rooms/create \
  -H "Content-Type: application/json" \
  -d '{
    "hotel_id": 1,
    "room_number": "101",
    "type": "Double",
    "price": 250.00,
    "status": "available"
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    "id": 1,
    "hotel_id": 1,
    "room_number": "101",
    "type": "Double",
    "price": "250.00",
    "status": "available",
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T19:00:00.000000Z"
  }
}
```

---

### POST /rooms/update

Updates an existing room's information.

**Endpoint:** `POST /api/rooms/update`

**Description:** Modifies room information. All fields except `id` are optional. Only provided fields will be updated.

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 1,
  "room_number": "101A",
  "price": 275.00,
  "status": "booked"
}
```

**Validation Rules:**
- `id` (required): integer, must exist in rooms table
- `hotel_id` (optional): integer, must exist in hotels table
- `room_number` (optional): string, maximum 50 characters
- `type` (optional): string, maximum 100 characters
- `price` (optional): numeric, minimum 0
- `status` (optional): string, must be either "available" or "booked"

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/rooms/update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "price": 275.00,
    "status": "booked"
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    "id": 1,
    "hotel_id": 1,
    "room_number": "101",
    "type": "Double",
    "price": "275.00",
    "status": "booked",
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T20:00:00.000000Z"
  }
}
```

---

### POST /rooms/delete

Deletes a room from the system.

**Endpoint:** `POST /api/rooms/delete`

**Description:** Removes a room. **Important:** Rooms with status "booked" cannot be deleted. The room status must be changed to "available" first.

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 1
}
```

**Validation Rules:**
- `id` (required): integer, must exist in rooms table

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/rooms/delete \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "Room deleted successfully."
  },
  "data": null
}
```

**Error Response (400 Bad Request):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 400,
    "errorMessage": "Cannot delete a booked room."
  },
  "data": null,
  "errors": []
}
```

---

## Reservations Endpoints

### GET /reservations/{id}

Retrieves detailed information about a specific reservation by its ID.

**Endpoint:** `GET /api/reservations/{id}`

**Description:** Returns a single reservation's complete information including associated room details.

**Path Parameters:**
- `id` (integer, required): The unique identifier of the reservation

**Request Example:**
```bash
curl -X GET http://localhost:8000/api/reservations/1
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    "id": 1,
    "room_id": 1,
    "guest_name": "John Doe",
    "guest_email": "john@example.com",
    "start_date": "2025-12-10",
    "end_date": "2025-12-15",
    "status": "pending",
    "room": {
      "id": 1,
      "hotel_id": 1,
      "room_number": "101",
      "type": "Double",
      "price": "250.00",
      "status": "booked"
    },
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T19:00:00.000000Z"
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 404,
    "errorMessage": "No query results for model [App\\Models\\Reservation] 999"
  },
  "data": null,
  "errors": []
}
```

---

### POST /reservations/create

Creates a new reservation in the system.

**Endpoint:** `POST /api/reservations/create`

**Description:** Creates a new reservation for a room. The system automatically checks for date conflicts and updates the room status to "booked" upon successful creation. The reservation status defaults to "pending" if not specified.

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "room_id": 1,
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "start_date": "2025-12-10",
  "end_date": "2025-12-15"
}
```

**Validation Rules:**
- `room_id` (required): integer, must exist in rooms table
- `guest_name` (required): string, maximum 255 characters
- `guest_email` (required): valid email address, maximum 255 characters
- `start_date` (required): date, must be today or later (format: Y-m-d)
- `end_date` (required): date, must be after start_date (format: Y-m-d)

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/reservations/create \
  -H "Content-Type: application/json" \
  -d '{
    "room_id": 1,
    "guest_name": "John Doe",
    "guest_email": "john@example.com",
    "start_date": "2025-12-10",
    "end_date": "2025-12-15"
  }'
```

**Response (201 Created):**
```json
{
  "meta": {
    "status": true,
    "code": 201,
    "message": "Reservation created successfully."
  },
  "data": {
    "id": 1,
    "room_id": 1,
    "guest_name": "John Doe",
    "guest_email": "john@example.com",
    "start_date": "2025-12-10",
    "end_date": "2025-12-15",
    "status": "pending",
    "room": {
      "id": 1,
      "hotel_id": 1,
      "room_number": "101",
      "type": "Double",
      "price": "250.00",
      "status": "booked"
    },
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T19:00:00.000000Z"
  }
}
```

**Error Response (409 Conflict - Date Overlap):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 409,
    "errorMessage": "The selected room is not available for the chosen dates."
  },
  "data": null,
  "errors": []
}
```

**Error Response (400 Bad Request - Room Not Available):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 400,
    "errorMessage": "Room is not available."
  },
  "data": null,
  "errors": []
}
```

**Error Response (422 Validation Error):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 422,
    "errorMessage": "INPUT_INVALID"
  },
  "data": null,
  "errors": {
    "start_date": ["The start date must be a date after or equal to today."],
    "end_date": ["The end date must be a date after start date."]
  }
}
```

---

### POST /reservations/update

Updates an existing reservation's information.

**Endpoint:** `POST /api/reservations/update`

**Description:** Modifies reservation information. All fields except `id` are optional. Only provided fields will be updated. **Important:** Cancelled reservations cannot be updated. When updating dates or room, the system checks for conflicts. When status is changed to "cancelled", the room status is automatically set to "available".

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 1,
  "guest_name": "Jane Doe",
  "status": "confirmed"
}
```

**Validation Rules:**
- `id` (required): integer, must exist in reservations table
- `room_id` (optional): integer, must exist in rooms table
- `guest_name` (optional): string, maximum 255 characters
- `guest_email` (optional): valid email address, maximum 255 characters
- `start_date` (optional): date, must be today or later (format: Y-m-d)
- `end_date` (optional): date, must be after start_date (format: Y-m-d)
- `status` (optional): string, must be one of: "pending", "confirmed", "cancelled"

**Request Examples:**

Update guest name and status:
```bash
curl -X POST http://localhost:8000/api/reservations/update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "guest_name": "Jane Doe",
    "status": "confirmed"
  }'
```

Cancel a reservation:
```bash
curl -X POST http://localhost:8000/api/reservations/update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "status": "cancelled"
  }'
```

Update reservation dates:
```bash
curl -X POST http://localhost:8000/api/reservations/update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "start_date": "2025-12-12",
    "end_date": "2025-12-17"
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "Reservation updated successfully."
  },
  "data": {
    "id": 1,
    "room_id": 1,
    "guest_name": "Jane Doe",
    "guest_email": "john@example.com",
    "start_date": "2025-12-10",
    "end_date": "2025-12-15",
    "status": "confirmed",
    "room": {
      "id": 1,
      "hotel_id": 1,
      "room_number": "101",
      "type": "Double",
      "price": "250.00",
      "status": "booked"
    },
    "created_at": "2025-12-05T19:00:00.000000Z",
    "updated_at": "2025-12-05T20:00:00.000000Z"
  }
}
```

**Error Response (400 Bad Request - Cancelled Reservation):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 400,
    "errorMessage": "Cannot update a cancelled reservation."
  },
  "data": null,
  "errors": []
}
```

**Error Response (409 Conflict - Date Overlap):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 409,
    "errorMessage": "Room is not available for the selected dates."
  },
  "data": null,
  "errors": []
}
```

---

### POST /reservations/delete

Deletes a reservation from the system.

**Endpoint:** `POST /api/reservations/delete`

**Description:** Removes a reservation. Upon deletion, the associated room's status is automatically set to "available".

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 1
}
```

**Validation Rules:**
- `id` (required): integer, must exist in reservations table

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/reservations/delete \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1
  }'
```

**Response (200 OK):**
```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "Reservation deleted successfully."
  },
  "data": null
}
```

**Error Response (404 Not Found):**
```json
{
  "meta": {
    "status": false,
    "errorCode": 404,
    "errorMessage": "No query results for model [App\\Models\\Reservation] 999"
  },
  "data": null,
  "errors": []
}
```

---

## Response Format

All API responses follow a standardized JSON format for consistency.

### Success Response

```json
{
  "meta": {
    "status": true,
    "code": 200,
    "message": "OK"
  },
  "data": {
    // Response data here
  }
}
```

### Error Response

```json
{
  "meta": {
    "status": false,
    "errorCode": 422,
    "errorMessage": "INPUT_INVALID"
  },
  "data": null,
  "errors": {
    // Validation errors here (if applicable)
  }
}
```

---

## HTTP Status Codes

The API uses standard HTTP status codes to indicate the result of requests:

| Code | Description | Usage |
|------|-------------|-------|
| 200 | Success | Request completed successfully |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request (e.g., trying to delete booked room) |
| 404 | Not Found | Requested resource not found |
| 409 | Conflict | Date overlap or resource conflict |
| 422 | Validation Error | Request validation failed |
| 500 | Internal Server Error | Server error occurred |

---

## Business Rules

### Hotels
- Hotels with existing rooms cannot be deleted. All associated rooms must be deleted first.

### Rooms
- Rooms with status "booked" cannot be deleted. Status must be changed to "available" first.
- Room status can be either "available" or "booked".

### Reservations
- A room cannot be reserved for overlapping dates. The system automatically checks for conflicts.
- Room status is automatically set to "booked" when a reservation is created.
- Room status is automatically set to "available" when a reservation is cancelled or deleted.
- Start date must be today or later (cannot book for past dates).
- End date must be after start date.
- Cancelled reservations cannot be updated.
- Valid reservation statuses: `pending`, `confirmed`, `cancelled`

---

## Usage Examples

### Complete Workflow Example

#### 1. Create a Hotel
```bash
curl -X POST http://localhost:8000/api/hotels/create \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Grand Istanbul Hotel",
    "location": "Istanbul, Turkey",
    "rating": 4.5
  }'
```

#### 2. Create Rooms for the Hotel
```bash
curl -X POST http://localhost:8000/api/rooms/create \
  -H "Content-Type: application/json" \
  -d '{
    "hotel_id": 1,
    "room_number": "101",
    "type": "Double",
    "price": 250.00,
    "status": "available"
  }'
```

#### 3. Check Available Rooms for Specific Dates
```bash
curl -X GET "http://localhost:8000/api/rooms?hotel_id=1&start_date=2025-12-10&end_date=2025-12-15"
```

#### 4. Create a Reservation
```bash
curl -X POST http://localhost:8000/api/reservations/create \
  -H "Content-Type: application/json" \
  -d '{
    "room_id": 1,
    "guest_name": "John Doe",
    "guest_email": "john@example.com",
    "start_date": "2025-12-10",
    "end_date": "2025-12-15"
  }'
```

#### 5. Confirm the Reservation
```bash
curl -X POST http://localhost:8000/api/reservations/update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "status": "confirmed"
  }'
```

#### 6. Cancel the Reservation
```bash
curl -X POST http://localhost:8000/api/reservations/update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "status": "cancelled"
  }'
```

#### 7. Delete a Reservation
```bash
curl -X POST http://localhost:8000/api/reservations/delete \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1
  }'
```

---

## License

MIT License
