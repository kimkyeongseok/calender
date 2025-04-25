# 일정 관리 스케줄러

## 프로젝트 설명

`일정 관리 스케줄러`는 회사 내 직원들의 일정을 효율적으로 관리하기 위한 웹 애플리케이션입니다. 주요 기능은 다음과 같습니다:

- **로그인/권한 관리**
  - 일반 사용자: 자신의 일정만 조회 및 등록
  - 관리자: 모든 사용자의 일정을 조회, 수정, 삭제
- **일정 CRUD**
  - 일정 등록, 수정, 삭제, 복사
  - 일정 종류 선택(일반/교육/세미나/회식)
  - 제목, 장소, 시작/종료 시간, 참가자 입력
- **FullCalendar 연동**
  - 달력 형태로 일정 표시
  - 날짜 클릭 시 등록 폼에 해당 날짜 자동 반영
- **관리자 페이지**
  - 전체 일정 리스트(페이징)
  - 사용자 관리(등록·수정·삭제, 페이징)
  - 통계 차트(월별 일자별 일정 개수)


## 설치 및 실행 방법 (Docker 권장)

1. **레포지토리 클론**
   ```bash
   git clone https://your-repo-url.git
   cd your-repo
   ```

2. **Docker Compose 설정**
   프로젝트 루트에 다음과 같은 `docker-compose.yml`을 생성하세요:
   ```yaml
   version: '3.8'
   services:
     db:
       image: mariadb:10.6
       environment:
         MYSQL_ROOT_PASSWORD: example
         MYSQL_DATABASE: scheduler
         MYSQL_USER: dbuser
         MYSQL_PASSWORD: dbpass
       volumes:
         - db_data:/var/lib/mysql
       healthcheck:
         test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
         interval: 5s
         retries: 5

     php:
       image: php:8.0-fpm
       volumes:
         - .:/var/www/html
       depends_on:
         db:
           condition: service_healthy

     web:
       image: nginx:latest
       ports:
         - "8080:80"
       volumes:
         - .:/var/www/html
         - ./nginx.conf:/etc/nginx/conf.d/default.conf
       depends_on:
         - php

   volumes:
     db_data:
   ```

3. **nginx 설정** (`nginx.conf`)
   ```nginx
   server {
     listen 80;
     server_name _;
     root /var/www/html;

     location / {
       try_files $uri $uri/ /index.html;
     }

     location ~ \.php$ {
       fastcgi_pass php:9000;
       fastcgi_index index.php;
       include fastcgi_params;
       fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     }
   }
   ```

4. **Docker 컨테이너 실행**
   ```bash
   docker-compose up -d
   ```

5. **데이터베이스 초기화**
   - `mariadb` 컨테이너에 접속 후, `scheduler` 데이터베이스에 스키마를 생성하세요.
   - 예시 `schema.sql`:
     ```sql
     CREATE TABLE users (
       username VARCHAR(50) PRIMARY KEY,
       password VARCHAR(255) NOT NULL,
       role ENUM('user','admin') NOT NULL
     ) COLLATE=utf8mb4_general_ci;

     CREATE TABLE events (
       id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(100),
       start DATETIME,
       end DATETIME,
       type VARCHAR(20),
       location VARCHAR(100),
       participants VARCHAR(255),
       owner VARCHAR(50),
       FOREIGN KEY (owner) REFERENCES users(username)
     ) COLLATE=utf8mb4_general_ci;
     ```

6. **접속**
   웹 브라우저에서 `http://localhost:8080/index.html` 로 접속하세요.


## 사용법 메뉴얼

1. **회원 관리**
   - 관리자 계정으로 로그인 후 상단의 **사용자 관리** 메뉴 또는 `user_list.html` 페이지에서 사용자 등록/수정/삭제 가능합니다.

2. **일정 등록 및 관리**
   - 로그인 후 캘린더 상단의 **달력**에서 날짜를 클릭하면 등록 폼이 나타납니다.
   - **시작/종료** 필드는 클릭한 날짜의 `00:00`으로 자동 세팅됩니다.
   - 작성자는 해당 일정의 삭제·수정·복사 버튼을 이용할 수 있고, 다른 사용자는 볼 수만 있습니다.

3. **관리자 페이지** 
   - **전체 일정**: `admin_list.html`등록된 모든 일정을 페이징으로 확인하고, 수정/삭제/복사 권한 부여 여부를 확인할 수 있습니다.
   - **통계 차트**: `chat.html`월별 일자별 일정 개수를 Bar 차트로 시각화합니다.
   - **사용자 관리**: `user_list.html`로 이동하여 사용자 정보를 관리합니다.

4. **로그인**
   - 로그인 모달에서 **비밀번호** 입력 후 **Enter** 또는 **로그인** 버튼을 눌러 인증합니다.
   - 관리자 로그인 시 로컬스토리지에, 일반 사용자 로그인 시 세션스토리지에 정보가 저장됩니다.

---

더 궁금한 사항이나 버그 제보는 이슈 트래커에 등록해 주세요!  
감사합니다.

