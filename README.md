# 📌 API Documentation

**Base URL**  
`/api/v1`

---

## 🔑 Authentication & User Management

### 1. Register  

**Endpoint**  
`POST /auth/register`  

**Payload**
```json
{
  "full_name": "Nopal Ganteng",
  "email": "marihitamkan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201)**
```json
{
  "success": true,
  "message": "Registrasi berhasil. Silakan cek email Anda untuk kode verifikasi.",
  "data": null,
  "errors": null
}
```

---

### 2. Login  

**Endpoint**  
`POST /auth/login`  

**Payload**
```json
{
  "email": "marihitamkan@example.com",
  "password": "password123"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "User logged in successfully",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "Nopal Ready To Test",
      "email": "marihitamkan@example.com",
      "role": "CITIZEN",
      "role_label": "Warga",
      "opd_id": null,
      "district_id": null,
      "profile_picture_url": null
    },
    "token": "sanctum_token_here"
  },
  "errors": null
}
```

---

### 3. Verify Email  

**Endpoint**  
`POST /auth/verify-email`  

**Payload**
```json
{
  "email": "marihitamkan@example.com",
  "code": "123456"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Email berhasil diverifikasi.",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "Nopal Ganteng",
      "email": "marihitamkan@example.com",
      "role": "CITIZEN"
    },
    "token": "sanctum_token_here"
  },
  "errors": null
}
```

---

### 4. Resend Verification Code  

**Endpoint**  
`POST /auth/resend-verification`  

**Payload**
```json
{
  "email": "marihitamkan@example.com"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Kode verifikasi baru telah dikirim.",
  "data": null,
  "errors": null
}
```

---

### 5. Forgot Password  

**Endpoint**  
`POST /auth/forgot-password`  

**Payload**
```json
{
  "email": "marihitamkan@example.com"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Jika email terdaftar, kode verifikasi telah dikirim ke email Anda.",
  "data": null,
  "errors": null
}
```

---

### 6. Reset Password  

**Endpoint**  
`POST /auth/reset-password`  

**Payload**
```json
{
  "email": "marihitamkan@example.com",
  "code": "123456",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Password berhasil direset.",
  "data": null,
  "errors": null
}
```

---

### 7. Get Profile (Me)  

**Endpoint**  
`GET /auth/me`  

**Headers**  
`Authorization: Bearer <token>`

**Response (200)**
```json
{
  "success": true,
  "message": "Data Profil Behasil Diambil",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "Nopal Ganteng",
      "email": "marihitamkan@example.com",
      "role": "CITIZEN",
      "role_label": "Warga",
      "email_verified_at": "2025-09-10T12:34:56.000000Z"
    }
  },
  "errors": null
}
```

---

### 8. Update Profile  

**Endpoint**  
`PUT /auth/me`  

**Headers**  
- `Authorization: Bearer <token>`  
- `Content-Type: multipart/form-data`

**Payload**
```json
{
  "full_name": "Nopal Mau ITB",
  "email": "marihitamkan@example.com",
  "profile_picture_url": "(file: jpg/png, max 2MB)"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Profil berhasil diperbarui.",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "Nopal Mau ITB",
      "email": "marihitamkann@example.com",
      "role": "CITIZEN",
      "profile_picture_url": "/storage/profile_pictures/abc.jpg"
    }
  },
  "errors": null
}
```

---

### 9. Change Password  

**Endpoint**  
`POST /auth/change-password`  

**Headers**  
`Authorization: Bearer <token>`

**Payload**
```json
{
  "current_password": "oldpassword123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Password changed successfully",
  "data": null,
  "errors": null
}
```

---

### 10. Logout  

**Endpoint**  
`POST /auth/logout`  

**Headers**  
`Authorization: Bearer <token>`

**Response (200)**
```json
{
  "success": true,
  "message": "Logged out",
  "data": null,
  "errors": null
}
```

---

## 🏢 OPD Management

### 1. List OPD
**Endpoint:**  
`GET /opds`

**Response (200):**
```json
{
  "success": true,
  "message": "Daftar OPD berhasil diambil.",
  "data": {
    "items": [
      { "id": 1, "name": "OPD Example" }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1
    }
  },
  "errors": null
}
```

---

### 2. Create OPD
**Endpoint:**  
`POST /opds`

**Payload:**
```json
{
  "name": "OPD Baru"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "OPD berhasil dibuat.",
  "data": {
    "id": 2,
    "name": "OPD Baru"
  },
  "errors": null
}
```

---

### 3. Show OPD
**Endpoint:**  
`GET /opds/{id}`

**Response (200):**
```json
{
  "success": true,
  "message": "Detail OPD berhasil diambil.",
  "data": {
    "id": 1,
    "name": "OPD Example"
  },
  "errors": null
}
```

---

### 4. Update OPD
**Endpoint:**  
`PUT /opds/{id}`

**Payload:**
```json
{
  "name": "OPD Update"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "OPD berhasil diperbarui.",
  "data": {
    "id": 1,
    "name": "OPD Update"
  },
  "errors": null
}
```

---

### 5. Delete OPD
**Endpoint:**  
`DELETE /opds/{id}`

**Response (200):**
```json
{
  "success": true,
  "message": "OPD berhasil dihapus",
  "data": null,
  "errors": null
}
```

---

## 🌍 District Management

### 1. List Districts
**Endpoint:**  
`GET /districts`

**Response (200):**
```json
{
  "success": true,
  "message": "List districts berhasil diambil",
  "data": {
    "items": [
      { "id": 1, "code": "01", "name": "District A" }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1
    }
  },
  "errors": null
}
```

---

### 2. Create District
**Endpoint:**  
`POST /districts`

**Payload:**
```json
{
  "name": "District Baru",
  "villages": [
    { "name": "Village 1" },
    { "name": "Village 2" }
  ]
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Kecamatan beserta desa berhasil ditambahkan.",
  "data": [
    {
      "id": 2,
      "code": "02",
      "name": "District Baru",
      "villages": [
        { "code": "02.01", "name": "Village 1" },
        { "code": "02.02", "name": "Village 2" }
      ]
    }
  ],
  "errors": null
}
```

---

### 3. Show District
**Endpoint:**  
`GET /districts/{id}`

**Response (200):**
```json
{
  "success": true,
  "message": "Detail district berhasil diambil",
  "data": [
    {
      "id": 1,
      "code": "01",
      "name": "District A",
      "villages": [
        { "code": "01.01", "name": "Village A" }
      ]
    }
  ],
  "errors": null
}
```

---

### 4. Update District
**Endpoint:**  
`PUT /districts/{id}`

**Payload:**
```json
{
  "name": "District Update"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "District berhasil diupdate",
  "data": [
    { "id": 1, "code": "01", "name": "District Update" }
  ],
  "errors": null
}
```

---

### 5. Delete District
**Endpoint:**  
`DELETE /districts/{id}`

**Response (200):**
```json
{
  "success": true,
  "message": "District berhasil dihapus",
  "data": null,
  "errors": null
}
```

---

## 🏘️ Village Management

### 1. List Villages in District
**Endpoint:**  
`GET /districts/{code}/villages`

**Response (200):**
```json
{
  "success": true,
  "message": "List villages berhasil diambil",
  "data": {
    "items": [
      { "code": "01.01", "name": "Village A" }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1
    }
  },
  "errors": null
}
```

---

### 2. Create Village
**Endpoint:**  
`POST /districts/{code}/villages`

**Payload:**
```json
{
  "name": "Village Baru"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Village berhasil ditambahkan ke District",
  "data": {
    "code": "01.02",
    "district_code": "01",
    "name": "Village Baru"
  },
  "errors": null
}
```

---

### 3. Update Village
**Endpoint:**  
`PUT /districts/{code}/villages/{villageCode}`

**Payload:**
```json
{
  "name": "Village Update"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Kelurahan berhasil diperbarui.",
  "data": {
    "code": "01.01",
    "district_code": "01",
    "name": "Village Update"
  },
  "errors": null
}
```

---

### 4. Delete Village
**Endpoint:**  
`DELETE /districts/{code}/villages/{villageCode}`

**Response (200):**
```json
{
  "success": true,
  "message": "Kelurahan berhasil dihapus.",
  "data": null,
  "errors": null
}
```