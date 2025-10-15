# Backend Taste — README

README สั้นและใช้งานได้จริงสำหรับแอป Backend Taste ปรับค่า placeholder ให้เป็นค่าจริงของโปรเจกต์

## ภาพรวมโปรเจกต์
Backend Taste เป็นเว็บ API สำหรับแอปแบบ full‑stack ให้บริการการพิสูจน์ตัวตน, REST/GraphQL API, การเก็บข้อมูล, งานแบ็กกราวด์ และเอกสาร OpenAPI พัฒนาด้วย TypeScript, Node.js (Express หรือเทียบเท่า) และ PostgreSQL ออกแบบเพื่อพัฒนาในเครื่อง ทดสอบ และปรับใช้งานจริง (รองรับ Docker)

## คุณสมบัติหลัก
- JSON REST API (ตัวเลือก: GraphQL)
- การพิสูจน์ตัวตนด้วย JWT (access + refresh tokens)
- การอนุญาตตามบทบาท (role-based authorization)
- การตรวจสอบคำขอและการจัดการข้อผิดพลาดแบบรวมศูนย์
- การแบ่งหน้า (pagination), กรอง (filtering), และจัดเรียง (sorting) สำหรับ resource
- มิเกรชันและ seeder สำหรับสคีมาและข้อมูลตัวอย่าง
- ทดสอบอัตโนมัติ (unit + integration)
- OpenAPI/Swagger สำหรับเอกสาร API
- Docker Compose สำหรับสภาพแวดล้อมพัฒนาในเครื่อง

## เทคสแตก (ตัวอย่าง)
- Node.js + TypeScript
- Express (หรือ Fastify/NestJS)
- PostgreSQL (หรือ MySQL)
- TypeORM / Prisma / Sequelize (เลือกอย่างใดอย่างหนึ่ง)
- Jest + Supertest สำหรับการทดสอบ
- ESLint + Prettier สำหรับคุณภาพโค้ด
- Docker + Docker Compose

## โครงสร้างรีโพ (ตัวอย่าง)
- src/
    - controllers/
    - services/
    - repositories/
    - models/ (entities หรือ prisma)
    - middlewares/
    - routes/
    - jobs/
    - utils/
    - config/
    - index.ts (จุดเริ่มต้นแอป)
- tests/
- migrations/
- seed/
- docker/
- .env.example
- package.json
- tsconfig.json
- README.md

## เริ่มต้น (การพัฒนา)

ข้อกำหนดเบื้องต้น:
- Node.js (LTS)
- npm หรือ yarn
- PostgreSQL (หรือใช้ Docker Compose)

1. Clone
     git clone <repo-url>
     cd backend_taste

2. ติดตั้ง
     npm install
     # หรือ
     yarn

3. คัดลอกไฟล์สภาพแวดล้อม
     cp .env.example .env
     # แก้ .env และใส่ค่าจริง

4. ตัวอย่าง .env (ปรับตามสภาพแวดล้อม)
     NODE_ENV=development
     PORT=4000
     DATABASE_URL=postgres://user:password@localhost:5432/backend_taste
     JWT_ACCESS_SECRET=change_this_access_secret
     JWT_REFRESH_SECRET=change_this_refresh_secret
     SALT_ROUNDS=10
     REDIS_URL=redis://localhost:6379   # ออปชันสำหรับ jobs/cache

5. ฐานข้อมูล
     # ตัวอย่างการใช้มิเกรชัน
     npm run migrate:latest
     npm run seed

     หากใช้ Prisma:
     npx prisma migrate dev --name init
     npx prisma db seed

6. รัน (โหมดพัฒนา)
     npm run dev
     # หรือ
     yarn dev

เซิร์ฟเวอร์จะพร้อมใช้งานที่ http://localhost:4000 โดยค่าเริ่มต้น

## สคริปต์ (แนะนำใน package.json)
- dev — สตาร์ทในโหมดพัฒนาด้วย ts-node-dev / nodemon
- build — คอมไพล์ TypeScript
- start — รันโค้ดที่คอมไพล์แล้ว (production)
- migrate:latest — รันมิเกรชัน DB
- migrate:rollback — ย้อนมิเกรชันล่าสุด
- seed — โหลดข้อมูลตัวอย่าง
- test — รันชุดทดสอบ
- lint — รัน ESLint
- format — รัน Prettier

ตัวอย่าง:
{
    "scripts": {
        "dev": "ts-node-dev --respawn --transpile-only src/index.ts",
        "build": "tsc",
        "start": "node dist/index.js",
        "migrate:latest": "your-migration-tool up",
        "seed": "node dist/seed/index.js",
        "test": "jest --runInBand",
        "lint": "eslint 'src/**/*.{ts,js}'",
        "format": "prettier --write 'src/**/*.{ts,js,json,md}'"
    }
}

## เอกสาร API
- Swagger/OpenAPI เปิดให้เข้าถึงที่ /docs หรือ /api-docs เมื่อ NODE_ENV !== production
- จัดเตรียมคอลเลกชัน Postman หรือไฟล์ OpenAPI JSON สำหรับการบูรณาการกับ frontend

## การพิสูจน์ตัวตน
- Endpoint: POST /auth/login — คืน access & refresh tokens
- Endpoint: POST /auth/refresh — ออก access token ใหม่โดยใช้ refresh token
- Endpoint: POST /auth/register — สร้างผู้ใช้ (ถ้ามี)
- ป้องกันเส้นทางด้วย middleware ที่ตรวจสอบ JWT และบังคับบทบาท

## สภาพแวดล้อม & ความลับ
- อย่า commit ไฟล์ .env หรือข้อมูลลับ
- ใช้ secrets manager ใน production (AWS Secrets Manager, Azure Key Vault, Vault)
- หมุนรหัสลับ JWT และข้อมูลบัญชีฐานข้อมูลเป็นระยะ

## การทดสอบ
- Unit tests: เร็วและเป็นเอกเทศ
- Integration tests: สร้าง test DB (Docker หรือ sqlite) และเรียก endpoint ผ่าน Supertest
- ตัวอย่าง:
    NODE_ENV=test DATABASE_URL=postgres://... npm test

## Docker (ตัวอย่าง)
- Dockerfile: สร้าง image สำหรับ production (multi-stage: build + run)
- docker-compose.yml: บริการสำหรับ app, postgres, redis
- เริ่มต้น:
    docker-compose up --build

## CI/CD
- ขั้นตอนที่แนะนำ:
    - ติดตั้ง dependencies
    - Lint -> Test -> Build
    - รันมิเกรชัน (ระมัดระวังเมื่อรันใน production)
    - นำ artifact ไปยัง container registry หรือ VM
- เครื่องมือแนะนำ: GitHub Actions, GitLab CI, Azure DevOps

## การสังเกตระบบ
- Logging: บันทึกแบบ JSON โครงสร้าง (winston/pino)
- Metrics: จุดข้อมูลสำหรับ Prometheus หรือ APM (NewRelic, Datadog)
- การติดตามข้อผิดพลาด: Sentry หรือเครื่องมือคล้ายกัน

## การแก้ปัญหา
- ข้อผิดพลาดการเชื่อมต่อ DB: ตรวจสอบ DATABASE_URL, ฐานข้อมูลทำงาน และเครือข่าย/ข้อมูลรับรอง
- มิเกรชันล้มเหลว: ตรวจสอบประวัติการมิเกรชันและกลยุทธ์การ rollback
- เทสต์ล้ม: ตรวจสอบว่า test DB สะอาดและได้รันมิเกรชัน/seed แล้ว

## การมีส่วนร่วม
- Fork รีโพ
- สร้าง branch ฟีเจอร์: feature/<short-desc>
- เขียนเทสต์สำหรับพฤติกรรมใหม่
- รัน linter และ test ก่อนส่ง PR
- ปฏิบัติตามแนวทางข้อความคอมมิต

## ความปลอดภัย
- ตรวจสอบและกรองข้อมูลนำเข้า
- ใช้ HTTPS ใน production
- เก็บรหัสผ่านด้วย bcrypt/argon2
- ใช้ access token อายุสั้นและ refresh token แบบหมุนได้
- รันเครื่องมือตรวจสอบ dependency (npm audit, Snyk)

## ไลเซนส์
- ระบุไลเซนส์ (เช่น MIT) และรวมไฟล์ LICENSE

## ติดต่อ
- มีไฟล์ MAINTAINERS หรือ CODEOWNERS ระบุผู้รับผิดชอบ
- สำหรับปัญหา ให้เปิด GitHub Issues ในรีโพ

ปรับคำสั่ง ตัวพอร์ต และการตั้งค่าอื่น ๆ ให้ตรงกับรีโพนี้ ไฟล์นี้เป็นจุดเริ่มต้นที่ใช้งานได้จริงสำหรับ backend ที่พร้อมใช้ใน production
