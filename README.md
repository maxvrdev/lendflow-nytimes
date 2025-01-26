# Lendflow Assessment: NYT Best Sellers List & Filter

**Author**: Max Dutton  
**Purpose**: A take-home assessment for Lendflow to demonstrate the ability to integrate with external APIs, handle validation, error scenarios, caching, and testing.

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Setup Instructions](#setup-instructions)
4. [API Documentation](#api-documentation)
5. [Validation and Error Handling](#validation-and-error-handling)
6. [Caching](#caching)
7. [Testing](#testing)

---

## Overview

This Laravel application serves as a **JSON API wrapper** around the [New York Times Best Sellers History API](https://developer.nytimes.com/docs/books-product/1/routes/lists/best-sellers/history.json/get). Key focuses include:

-   **Validation** of incoming requests.
-   **Error Handling** for edge cases and API failures.
-   **Reusability** for future extensions.
-   **Caching** to optimize performance and minimize external API calls.
-   **Testing** to ensure reliability and cover edge cases.

---

## Features

1. **NYT Best Sellers Endpoint**

    - Provides an endpoint to query NYT Best Sellers data.
    - Supports filtering by `author`, `ISBN`, `title`, and pagination (`offset`).

2. **Robust Validation**

    - Ensures only valid data is processed.
    - Uses Laravel's `Form Requests`.

3. **Comprehensive Error Handling**

    - Handles invalid input, API failures, and other unexpected scenarios.
    - Provides meaningful error messages.

4. **Caching**

    - Reduces redundant API calls with a 60-minute cache.

5. **Testing Suite**
    - Feature tests include validation, success cases, and error handling.
    - Mocked HTTP client enables offline and credential-free testing.

---

## Setup Instructions

1. **Clone the Repository**

    ```bash
    git clone https://github.com/maxvrdev/lendflow-nytimes
    cd lendflow-nytimes
    ```

2. **Install Dependencies**

    ```bash
    composer install
    ```

3. **Set Up Environment**

    - Copy `.env.example` to `.env`:
        ```bash
        cp .env.example .env
        ```
    - Add your NYT API key:
        ```env
        NYT_API_KEY=your-nyt-api-key
        NYT_API_BASE_URL=https://api.nytimes.com/svc/books/v3
        ```

4. **Run the Application**

    ```bash
    php artisan serve
    ```

5. **Run Tests**
   Execute the test suite to verify functionality:
    ```bash
    php artisan test
    ```

---

## API Documentation

This application provides a versioned JSON API that wraps the NYT Best Sellers History API.

### Base URL

```
http://{your-domain}/api/v1/nyt
```

### Endpoints

#### **GET /best-sellers**

Fetches data from the NYT Best Sellers History API.

**Query Parameters**:

| Parameter | Type            | Required | Description                          |
| --------- | --------------- | -------- | ------------------------------------ |
| `author`  | `string`        | No       | Filter results by author name.       |
| `isbn[]`  | `array[string]` | No       | Filter results by one or more ISBNs. |
| `title`   | `string`        | No       | Filter results by book title.        |
| `offset`  | `integer`       | No       | Used for pagination. Default is `0`. |

**Example Requests**:

-   Fetch by Author:

    ```
    GET /api/v1/nyt/best-sellers?author=Stephen+King
    ```

-   Fetch by ISBNs:

    ```
    GET /api/v1/nyt/best-sellers?isbn[]=9781476727653&isbn[]=9780553380163
    ```

-   Fetch by Title:

    ```
    GET /api/v1/nyt/best-sellers?title=The+Shining
    ```

-   Paginate Results:
    ```
    GET /api/v1/nyt/best-sellers?offset=20
    ```

### Example Responses

#### **Successful Response (200)**

```json
{
    "status": "OK",
    "results": [
        {
            "title": "The Shining",
            "author": "Stephen King",
            "description": "A thriller about a haunted hotel.",
            "publisher": "Anchor",
            "isbn": ["9780553380163", "9781476727653"],
            "updated_date": "2025-01-25"
        },
        {
            "title": "It",
            "author": "Stephen King",
            "description": "A novel about a shape-shifting entity that terrorizes children.",
            "publisher": "Scribner",
            "isbn": ["9781501142970", "9781501141232"],
            "updated_date": "2025-01-25"
        }
    ]
}
```

#### **Validation Error (422)**

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "isbn.0": ["The isbn.0 must be a string."]
    }
}
```

#### **NYT API Failure (401)**

```json
{
    "error": "Failed to fetch data from NYT API: {\"fault\":\"Invalid API Key\"}"
}
```

#### **Internal Server Error (500)**

```json
{
    "error": "An unexpected error occurred."
}
```

---

## Validation and Error Handling

This application implements robust **validation** and **error handling** to ensure data integrity and provide meaningful feedback in case of failures.

### Validation

1. **Request Validation**

    - Validation is performed using Laravel's `Form Requests` to ensure all incoming requests contain valid data before processing.
    - Validation rules are defined in the `NytBestSellersRequest` class.

2. **Validation Rules**
   The following rules are applied:

    - `author`: Must be a string (optional).
    - `isbn`: Must be an array of valid ISBN strings (optional).
    - `isbn.*`: Each ISBN must be a string.
    - `title`: Must be a string (optional).
    - `offset`: Must be an integer (optional, minimum value: 0).

3. **Example Validation Rules**
    ```php
    public function rules()
    {
        return [
            'author' => 'nullable|string',
            'isbn' => 'nullable|array',
            'isbn.*' => 'string',
            'title' => 'nullable|string',
            'offset' => 'nullable|integer|min:0',
        ];
    }
    ```

---

## Caching

To optimize performance and reduce redundant API calls to the New York Times Best Sellers API, this application implements **caching** using Laravel's built-in caching functionality.

### Caching Strategy

1. **When Caching is Applied**:

    - API responses are cached for 60 minutes.
    - Each unique request query is hashed into a cache key, ensuring distinct requests have distinct cache entries.

2. **Cache Key Structure**:

    - The cache key is generated by hashing the query parameters of the request using `md5()`. This ensures the cache uniquely identifies each request.

3. **Benefits of Caching**:
    - Reduces the number of external API calls, preventing excessive usage of the NYT API key.
    - Improves response times for repeated requests.
    - Provides resiliency by serving cached data if the NYT API becomes temporarily unavailable.

### Implementation

Caching is implemented in the `NytBestSellersController` using the following logic:

```php
$cacheKey = 'nyt_best_sellers_' . md5(json_encode($query));

$data = Cache::remember($cacheKey, 60, function () use ($query) {
    $response = Http::get(config('services.nyt.base_url') . '/lists/best-sellers/history.json', $query);

    if ($response->failed()) {
        throw new \Exception('Failed to fetch data from NYT API: ' . $response->body(), $response->status());
    }

    return $response->json();
});
```

---

## Testing

This project includes a robust suite of **feature tests** to ensure the functionality of the API, covering validation, error handling, and integration with a mocked New York Times API. Below are the details and instructions for running the tests.

### Overview of Tests

The following tests are included in the suite:

1. **Validation Tests**

    - `test_valid_request_data`: Verifies that valid input passes validation.
    - `test_invalid_isbn`: Ensures invalid ISBN formats are rejected by the validation rules.

2. **API Endpoint Tests**
    - `test_best_sellers_endpoint_returns_data`: Tests the endpoint for a successful response from the NYT API.
    - `test_best_sellers_endpoint_handles_invalid_response`: Ensures the endpoint gracefully handles errors (e.g., invalid API keys or failed requests).

### Running Tests

1. **Prepare Your Environment**

    - Ensure your `.env` file is configured correctly, though API keys are **not required** for testing because the HTTP client is mocked in the tests.

2. **Execute Tests**

    - Open your terminal and navigate to the project directory.
    - Run the following command to execute the test suite:
        ```bash
        php artisan test
        ```

3. **Expected Output**
   When all tests pass, you should see output similar to this:

    ```plaintext
    PASS  Tests\Feature\NytBestSellersTest
    ✓ test_valid_request_data
    ✓ test_invalid_isbn
    ✓ test_best_sellers_endpoint_returns_data
    ✓ test_best_sellers_endpoint_handles_invalid_response

    Tests:    4 passed (12 assertions)
    Time:     0.45s
    ```
