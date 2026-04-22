# 🍹 REST API Es Buah

## 1. Project Overview

API sederhana untuk mengelola data produk es buah.


## 2. Tech Stack

Teknologi yang digunakan dalam project ini:

* *PHP Native* → Backend
* *MySQL* → Database
* *JSON* → format response API

---

## 3. Database Diagram

Struktur database terdiri dari 2 tabel:

### 📌 Tabel products

| Field       | Type     |
| ----------- | -------- |
| id          | INT (PK) |
| name        | VARCHAR  |
| description | TEXT     |
| price       | INT      |
| image_url   | VARCHAR  |
| calories    | INT      |

### 📌 Tabel ingredients

| Field      | Type     |
| ---------- | -------- |
| id         | INT (PK) |
| product_id | INT (FK) |
| name       | VARCHAR  |




## 4. Installation Guide

1. Pindahkan folder project ke:
    - htdocs/ (XAMPP) atau www/ (Laragon)

2. Buat database di MySQL (misalnya: rest_api)

3. Konfigurasi database di:
    - config/database.php

4. Jalankan Apache & MySQL

5. Akses di browser / Postman:

http://localhost/REST-API/products/read.php



## 5. API Documentation Link

## Endpoint API
* GET /products/read.php → Ambil semua produk
* GET /products/read_one.php?id=1 → Ambil 1 produk
* GET /products/search.php?s=keyword → Cari produk
* POST /products/create.php → Tambah produk
* PUT /products/update.php → Update produk
* DELETE /products/delete.php → Hapus produk

## Postman Collection

File Postman Collection dapat disimpan di folder:

- docs/postman_collection.json

Import ke Postman untuk mencoba semua endpoint.


## 6. Integration Contract
* Base URL: http://localhost/REST-API/
* Response Format: JSON (snake_case)
* CORS: Enabled

Contoh Endpoint
* GET /products/read.php
* GET /products/read_one.php?id=1
* GET /products/search.php?q=es