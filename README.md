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

    - Unit tests for services.
    - Feature tests for API endpoints.
    - Mocked HTTP client for offline and credential-free testing.

6. **API Versioning**  
   Supports future-proofing with versioned endpoints.

---

## Setup Instructions

1. **Clone the Repository**
    ```bash
    git clone https://github.com/MaxDutton/lendflow-nyt-api.git
    cd lendflow-nyt-api
    ```
