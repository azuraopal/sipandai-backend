# 📌 API Documentation

Base URL:
/api/v1

yaml
Copy code

---

## 🔑 Authentication & User Management

### 1. Register

**Endpoint**
POST /auth/register

pgsql
Copy code

**Payload**

```json
{
  "full_name": "John Doe",
  "email": "johndoe@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
Response (201)

json
Copy code
{
  "success": true,
  "message": "Registrasi berhasil. Silakan cek email Anda untuk kode verifikasi.",
  "data": null,
  "errors": null
}
2. Login
Endpoint

bash
Copy code
POST /auth/login
Payload

json
Copy code
{
  "email": "johndoe@example.com",
  "password": "password123"
}
Response (200)

json
Copy code
{
  "success": true,
  "message": "User logged in successfully",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "John Doe",
      "email": "johndoe@example.com",
      "role": "USER",
      "role_label": "User",
      "opd_id": null,
      "district_id": null,
      "profile_picture_url": null
    },
    "token": "sanctum_token_here"
  },
  "errors": null
}
3. Verify Email
Endpoint

bash
Copy code
POST /auth/verify-email
Payload

json
Copy code
{
  "email": "johndoe@example.com",
  "code": "123456"
}
Response (200)

json
Copy code
{
  "success": true,
  "message": "Email berhasil diverifikasi.",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "John Doe",
      "email": "johndoe@example.com",
      "role": "USER"
    },
    "token": "sanctum_token_here"
  },
  "errors": null
}
4. Resend Verification Code
Endpoint

bash
Copy code
POST /auth/resend-verification
Payload

json
Copy code
{
  "email": "johndoe@example.com"
}
Response (200)

json
Copy code
{
  "success": true,
  "message": "Kode verifikasi baru telah dikirim.",
  "data": null,
  "errors": null
}
5. Forgot Password
Endpoint

bash
Copy code
POST /auth/forgot-password
Payload

json
Copy code
{
  "email": "johndoe@example.com"
}
Response (200)

json
Copy code
{
  "success": true,
  "message": "Jika email terdaftar, kode verifikasi telah dikirim ke email Anda.",
  "data": null,
  "errors": null
}
6. Reset Password
Endpoint

pgsql
Copy code
POST /auth/reset-password
Payload

json
Copy code
{
  "email": "johndoe@example.com",
  "code": "123456",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
Response (200)

json
Copy code
{
  "success": true,
  "message": "Password berhasil direset.",
  "data": null,
  "errors": null
}
7. Get Profile (Me)
Endpoint

vbnet
Copy code
GET /auth/me
Headers

makefile
Copy code
Authorization: Bearer <token>
Response (200)

json
Copy code
{
  "success": true,
  "message": "Data Profil Behasil Diambil",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "John Doe",
      "email": "johndoe@example.com",
      "role": "USER",
      "role_label": "User",
      "email_verified_at": "2025-09-10T12:34:56.000000Z"
    }
  },
  "errors": null
}
8. Update Profile
Endpoint

bash
Copy code
PUT /auth/me
Headers

makefile
Copy code
Authorization: Bearer <token>
Content-Type: multipart/form-data
Payload

json
Copy code
{
  "full_name": "John Updated",
  "email": "johnupdated@example.com",
  "profile_picture_url": (file: jpg/png, max 2MB)
}
Response (200)

json
Copy code
{
  "success": true,
  "message": "Profil berhasil diperbarui.",
  "data": {
    "user": {
      "id": "uuid",
      "full_name": "John Updated",
      "email": "johnupdated@example.com",
      "role": "USER",
      "profile_picture_url": "/storage/profile_pictures/abc.jpg"
    }
  },
  "errors": null
}
9. Change Password
Endpoint

bash
Copy code
POST /auth/change-password
Headers

makefile
Copy code
Authorization: Bearer <token>
Payload

json
Copy code
{
  "current_password": "oldpassword123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
Response (200)

json
Copy code
{
  "success": true,
  "message": "Password changed successfully",
  "data": null,
  "errors": null
}
10. Logout
Endpoint

bash
Copy code
POST /auth/logout
Headers

makefile
Copy code
Authorization: Bearer <token>
Response (200)

json
Copy code
{
  "success": true,
  "message": "Logged out",
  "data": null,
  "errors": null
}
```
