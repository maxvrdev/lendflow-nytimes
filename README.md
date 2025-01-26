# Lendflow Assessment: NYT Best Sellers List & Filter

**Author**: Max Dutton  
**Project Purpose**: A take-home test for the Lendflow assessment.

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Setup Instructions](#setup-instructions)
4. [API Documentation](#api-documentation)
5. [Validation and Error Handling](#validation-and-error-handling)
6. [Caching](#caching)
7. [Testing](#testing)
8. [Considerations and Best Practices](#considerations-and-best-practices)

---

## Overview

This Laravel application acts as a **JSON API wrapper** around the [New York Times Best Sellers History API](https://developer.nytimes.com/docs/books-product/1/routes/lists/best-sellers/history.json/get). The project demonstrates proper handling of API integrations with an emphasis on:

-   **Validation**
-   **Error Handling**
-   **Reusability**
-   **Caching**
-   **Testing, including edge and failure cases**

The solution reflects a focus on maintainability, extensibility, and reliability across environments.

---

## Features

1. **Endpoint to Query NYT Best Sellers**  
   Exposes a JSON API endpoint that forwards requests to the NYT API with the following parameters:

    - `author`: Filter by author name.
    - `isbn[]`: Filter by ISBN(s).
    - `title`: Filter by book title.
    - `offset`: Pagination support.

2. **Robust Validation**  
   Ensures incoming requests are validated using Laravel's `Form Requests`.

3. **Error Handling**  
   Handles edge cases, such as:

    - Invalid input parameters.
    - Unavailable NYT API (e.g., network issues, invalid API key).
    - Missing or invalid credentials.

4. **Caching**  
   Implements caching to reduce redundant API calls and improve response times.

5. **Testing**  
   Comprehensive test suite:

    - Feature tests for API endpoints.
    - Mocked HTTP client for offline and credential-free testing.

6. **API Versioning**  
   Supports future-proofing with versioned endpoints.

---

## Setup Instructions

Follow these steps to set up the project:

1. **Clone the Repository**:

    ```bash
    git clone https://github.com/maxvrdev/lendflow-nytimes
    cd lendflow-nytimes
    ```

2. **Configure Environment Variables**:

    - Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    - Open the `.env` file and update the `NYT_API_KEY` property with your New York Times API key.

3. **Generate the Application Key**:

    ```bash
    php artisan key:generate
    ```

4. **Install JavaScript Dependencies**:

    ```bash
    yarn
    ```

5. **Install PHP Dependencies**:

    ```bash
    composer install
    ```

6. **Serve the Application**:

    ```bash
    php artisan serve
    ```

7. **Access the Application**:
   Open your browser and navigate to:
    ```
    http://127.0.0.1:8000
    ```
