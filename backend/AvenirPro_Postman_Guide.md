# AvenirPro API Postman Testing Guide

This guide provides everything you need to test the AvenirPro API using Postman. The base URL pattern for all endpoints is `http://your-domain.local/api/v1`.

## 1. Authentication (AUTH)

### 1.1 Register
- **Method:** `POST`
- **URL:** `/api/v1/register`
- **Headers:** `Accept: application/json`
- **Body:** (JSON)
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
- **Expected Response:** `201 Created`
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "created_at": "..."
    },
    "token": "1|abc123def456..."
  }
}
```

### 1.2 Login
- **Method:** `POST`
- **URL:** `/api/v1/login`
- **Headers:** `Accept: application/json`
- **Body:** (JSON)
```json
{
  "email": "jane@example.com",
  "password": "password123"
}
```
- **Expected Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "2|xyz987..."
  }
}
```

### 1.3 Get Authenticated User (Me)
- **Method:** `GET`
- **URL:** `/api/v1/me`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <your_token_here>`
- **Expected Response:** `200 OK` (User object)

### 1.4 Logout
- **Method:** `POST`
- **URL:** `/api/v1/logout`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <your_token_here>`
- **Expected Response:** `200 OK`
```json
{
  "success": true,
  "message": "Logged out successfully."
}
```

---

## 2. Jobs (JOBS)

### 2.1 List All Jobs
- **Method:** `GET`
- **URL:** `/api/v1/jobs?page=1&search=developer&category=IT`
- **Headers:** `Accept: application/json`
- **Expected Response:** `200 OK` (Paginated list of jobs)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Software Developer",
      "sector": "Technology",
      "salary_range": "$70k - $120k",
      "category": "IT",
      "image_url": "http://domain.local/storage/jobs/image.jpg"
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "total": 5
}
```

### 2.2 Get Single Job Details
- **Method:** `GET`
- **URL:** `/api/v1/jobs/{id}`
- **Headers:** `Accept: application/json`
- **Expected Response:** `200 OK` (Job object including category and steps)

### 2.3 Get Job Steps
- **Method:** `GET`
- **URL:** `/api/v1/jobs/{id}/steps`
- **Headers:** `Accept: application/json`
- **Expected Response:** `200 OK`

---

## 3. Questions (QUESTIONS)

### 3.1 List All Questions
- **Method:** `GET`
- **URL:** `/api/v1/questions?page=1`
- **Headers:** `Accept: application/json`
- **Expected Response:** `200 OK` (Paginated list of questions with possible answers)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "question_text": "I like to work with numbers.",
      "riasec_profile": { "code": "C", "name": "Conventional" },
      "answers": [ { "id": 1, "answer_text": "Strongly Agree", "score": 5 } ]
    }
  ],
  "current_page": 1
}
```

---

## 4. Test (TEST)

### 4.1 Submit Answer
- **Method:** `POST`
- **URL:** `/api/v1/test/answer`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <your_token_here>`
- **Body:** (JSON)
```json
{
  "question_id": 1,
  "answer_id": 5
}
```
- **Expected Response:** `200 OK`

### 4.2 Calculate Results
- **Method:** `POST`
- **URL:** `/api/v1/test/calculate`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <your_token_here>`
- **Expected Response:** `200 OK` (Returns `TestResultResource` containing top profile and job matches)

### 4.3 Get My Results
- **Method:** `GET`
- **URL:** `/api/v1/my-results`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <your_token_here>`
- **Expected Response:** `200 OK` (Returns User's latest `TestResultResource`)

---

## 5. Admin (ADMIN)

*Note: The user token used here must belong to an Admin role.*

### 5.1 Create Job
- **Method:** `POST`
- **URL:** `/api/v1/admin/jobs`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <admin_token_here>`
- **Body:** (form-data)
  - `title` (text) : "Backend Engineer"
  - `category_id` (text) : 1
  - `description` (text) : "..."
  - `sector` (text) : "Technology"
  - `riasec_types` (text) : "I,C"
  - `image` (file) : [Attach .jpg or .png image here]
- **Expected Response:** `201 Created`

### 5.2 Update Job
- **Method:** `POST` *(Note: Use POST and spoof PUT when uploading files in Laravel)*
- **URL:** `/api/v1/admin/jobs/{id}`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <admin_token_here>`
- **Body:** (form-data)
  - `_method` (text): "PUT"
  - `title` (text) : "Senior Backend Engineer"
  - `image` (file) : [Optional attach new image]
- **Expected Response:** `200 OK`

### 5.3 Delete Job
- **Method:** `DELETE`
- **URL:** `/api/v1/admin/jobs/{id}`
- **Headers:** 
  - `Accept: application/json`
  - `Authorization: Bearer <admin_token_here>`
- **Expected Response:** `200 OK`
