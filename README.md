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