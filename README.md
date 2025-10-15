# README / คู่การใช้งานระบบ

![Static Badge](https://img.shields.io/badge/Laravel-red?style=for-the-badge&logo=laravel&logoColor=red&labelColor=black)
![Static Badge](https://img.shields.io/badge/Vue.JS-green?style=for-the-badge&logo=vue.js&logoColor=green&labelColor=black)
![Static Badge](https://img.shields.io/badge/docker-blue?style=for-the-badge&logo=docker&logoColor=blue&labelColor=black)
![Static Badge](https://img.shields.io/badge/github-black?style=for-the-badge&logo=github&logoColor=white&labelColor=black)

![Static Badge](https://img.shields.io/badge/Vite-purple?style=for-the-badge&logo=vite&logoColor=purple&labelColor=black)
![Static Badge](https://img.shields.io/badge/php-blue?style=for-the-badge&logo=php&logoColor=blue&labelColor=black)
![Static Badge](https://img.shields.io/badge/Javascript-yellow?style=for-the-badge&logo=javascript&logoColor=yellow&labelColor=black)
![Static Badge](https://img.shields.io/badge/Tailwind_CSS-lightblue?style=for-the-badge&logo=tailwindcss&logoColor=lightblue&labelColor=black)


## ภาพรวมของระบบ
ระบบสำหรับการยืม-คืนอุปกรณ์สำหรับผู้ใช้และผู้ดูแลระบบ ผู้ใช้สามารถลงทะเบียน ขออนุญาตยืมอุปกรณ์ อัปโหลดเอกสารยืนยันตัวตน และติดตามสถานะคำขอได้ ส่วนผู้ดูแลระบบจัดการอุปกรณ์ หมวดหมู่ การอนุมัติ เบิก-คืน และค่าปรับ

## คุณสมบัติหลัก 🌟
### ทั้่วไป
- UI แยกตามบทบาท (ผู้ดูแล vs ผู้ใช้ทั่วไป)
### ผู้ใช้ (User) 🧑‍🎓
- ลงทะเบียนผู้ใช้, เข้าสู่ระบบ และ จัดการโปรไฟล์
- ยืนยันตัวตนนักศึกษาโดยอัปโหลดภาพบัตรนักศึกษา และ รอการอนุมัติจากผู้ดูแล
- ค้นหา, กรองจากหมวดหมู่ หรือ สถานะได้
- สร้างคำขอยืม, อนุมัติ/ปฏิเสธ, เบิก, คืน
### พนักงาน (Staff) 👲
- หน้า Dashboard สำหรับดูภาพรวม
- จัดการคำขอการยืมของผู้ใช้
- จัดการค่าปรับและบันทึกหมายเหตุการคืน
### ผู้ดูแล (Administrator) 🧑‍💼
- หน้า Dashboard สำหรับดูภาพรวม
- สร้าง / แก้ไข / ลบอุปกรณ์และหมวดหมู่ (รองรับภาพประกอบ)
- อัปโหลดภาพอุปกรณ์รองรับ jpeg, png, webp (มีดูตัวอย่างและลบ)
- ค้นหา, กรอง และแบ่งหน้าในตารางรายการ
- บันทึก Log ของผู้ใช้การใช้งานในระบบ

## คู่มือใช้งานด่วน
### สำหรับผู้ใช้
- สร้างบัญชี: ลงทะเบียนด้วยรหัสประจำตัว (นักศึกษา/พนักงาน), ชื่อ, อีเมล, เบอร์โทร และรหัสผ่าน
- ยืนยันตัวตน: ไปที่หน้าการยืนยันและอัปโหลดรูปบัตร (ผู้ดูแลจะอนุมัติ)
- ขออุปกรณ์:
    - เปิดหน้ารายการอุปกรณ์ → คลิก "ขอยืม"
    - เลือกวันที่ ใส่เหตุผล → ส่งคำขอ
    - ฟิลด์จำเป็นมีเครื่องหมาย * แดง
- ติดตามคำขอ: หน้า "คำขอของฉัน" แสดงสถานะและประวัติ

### สำหรับผู้ดูแลระบบ
- เปิดแดชบอร์ดผู้ดูแล: ดูการยืนยันและคำขอที่รอดำเนินการ
- จัดการคำขอ:
    - คลิกเปิดคำขอเพื่อดูรายละเอียด
    - ใช้ปุ่มทำรายการ (อนุมัติ / ปฏิเสธ) — เมื่อปฏิเสธต้องระบุเหตุผล
    - หลังการเบิก ให้บันทึกค่าปรับ (ถ้ามี) และหมายเหตุการคืน
- การจัดการอุปกรณ์:
    - ใช้ modal สร้าง/แก้ไข เพื่อใส่รหัส, ชื่อ, หมวด, สถานะ และภาพ
    - ภาพไฟล์รองรับ JPG/PNG/WebP — ดูตัวอย่างและลบได้ (การลบในโหมดแก้ไขจะถูกติดตาม)


## ผู้มีส่วนร่วมในโปรเจ็ค

- [@Unique](https://github.com/unikZer0) ยูนิค
- [@Thaksin.785](https://www.github.com/ThaksinhCW3) ทักษิณ
- [@Phanousith](https://github.com/phanousith) พานุสิทธ์
