# 일정 관리 스케줄러

직원들의 효율적인 일정 관리를 위해 개발된 웹 기반 시스템입니다. 캘린더 한눈보기, 일정 등록/수정/삭제/복사, 로그인/권한 관리, 관리자 통계·사용자 관리 기능을 제공합니다.

---

## 주요 기능

- **캘린더 뷰**: FullCalendar를 이용한 월별 달력
- **일정 CRUD**: 등록·수정·삭제·복사 기능 지원
- **로그인/권한**
  - **일반 사용자**: 본인이 등록한 일정만 관리
  - **관리자**: 모든 일정 조회·관리, 통계·사용자 관리
- **통계 차트**: Chart.js로 일자별 일정 개수 시각화
- **사용자 관리**: 관리자 전용 사용자 등록·수정·삭제
- **백엔드**: PHP(클래스 구조), MySQLi 기반 MariaDB 연결
- **컨테이너**: Docker를 이용한 서비스 배포

---

## 기술 스택

- **프론트엔드**: HTML, CSS, JavaScript, jQuery, FullCalendar, Chart.js
- **백엔드**: PHP (클래스화된 `Database`, `UserManager`, `EventManager`, `AdminStats`)
- **데이터베이스**: MariaDB (utf8mb4\_general\_ci)
- **배포**: Docker, Docker Compose

---

## 설치 및 실행

### 요구 사항

- Docker & Docker Compose
- git

### 설치

1. 리포지토리 복제
   ```bash
   git clone https://your-repo-url/scheduler.git
   cd scheduler
   ```
2. 환경 변수 설정 (필요 시 `.env` 파일)
3. Docker 컴포즈 실행
   ```bash
   docker-compose up -d
   ```

### 데이터베이스 초기화

`docker-compose`로 MariaDB 컨테이너가 올라오면, 다음 SQL로 스키마 생성:

```sql
CREATE DATABASE scheduler CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE scheduler;
-- 테이블 users
CREATE TABLE users (
  username VARCHAR(50) PRIMARY KEY,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB;
-- 테이블 events
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  start DATETIME NOT NULL,
  end DATETIME NOT NULL,
  type ENUM('일반','교육','세미나','회식') NOT NULL,
  location VARCHAR(100),
  participants TEXT,
  owner VARCHAR(50) NOT NULL,
  FOREIGN KEY (owner) REFERENCES users(username)
) ENGINE=InnoDB;
```

### 실행

- **웹 브라우저 접근**: `http://localhost/index.html`
- **메인 페이지**: 일정 조회·등록 (로그인 필수)
- **관리자 페이지**: `admin.html`
- **통계 차트**: `admin_chart.html`
- **사용자 관리**: `user.html`

---

## 사용법

### 일반 사용자

1. **로그인**: 메인 페이지 상단 ‘로그인’ 버튼 → 모달로 아이디/비밀번호 입력
2. **일정 등록**: 달력에서 날짜 클릭 → 폼 입력 → 저장
3. **수정/삭제/복사**: 등록된 일정 클릭 → 수정·삭제·복사 버튼 사용

### 관리자

1. **관리자 로그인**: 권한이 `admin`인 계정으로 로그인
2. **전체 일정** (`admin_list.html`)
   - 페이징(10개/페이지)된 일정 목록 조회
3. **통계 차트** (`chart.html`)
   - 현재 월 일자별 일정 개수 막대 그래프
4. **사용자 관리** (`user_list.html`)
   - 사용자 목록 페이징, 등록·수정·삭제

---

## API 엔드포인트

| 파일           | 액션              | 설명                |
| ------------ | --------------- | ----------------- |
| `login.php`  | `POST`          | 사용자 인증            |
| `users.php`  | `action=list`   | 사용자 목록            |
|              | `action=save`   | 등록/수정             |
|              | `action=delete` | 삭제                |
| `events.php` | `action=list`   | 일정 목록 (페이징 지원)    |
|              | `action=get`    | 단일 일정 조회          |
|              | `action=save`   | 등록/수정             |
|              | `action=delete` | 삭제                |
|              | `action=copy`   | 복사                |
| `admin.php`  | `action=stats`  | 전체 통계(총합/사용자별 집계) |
|              | `action=list`   | 관리자용 일정 페이징 조회    |

---

