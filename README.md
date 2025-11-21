# OOPPS – Web Application for Active Memorization of Competitive Exam Topics

OOPPS is a web application designed to help students prepare competitive exam topics through **active recall**, using exercises such as fill-in-the-blanks and other interactive modes.  
The project also serves as a personal playground to apply clean code practices, refactoring, and a simple **MVC architecture with a Front Controller** in PHP.

---

## ✨ Features

- 📚 Manage exam topics (index, justification, context, bibliography…)
- ✍️ Practice exercises: fill-in-the-blanks and other modes (WIP)
- 👤 Login system with sessions
- 🧭 Dashboard with quick access to topics and exercises
- 🧱 **Front Controller** + `View` class for rendering templates
- 🎨 Basic UI built with **Bootstrap**
- 🔧 Progressive refactoring toward a cleaner, modular structure

---

## 🧰 Tech Stack

**Backend:**  
- PHP 8+  
- Composer (autoloading)  
- MySQL / MariaDB (relational database)

**Frontend:**  
- HTML, CSS  
- Bootstrap 5  

**Other:**  
- Git   
- MVC pattern  
- Clean code principles  
- Testing mindset (future PHPUnit integration)

---

## 📁 Project Structure

```text
oopps/
├─ config/        # App and database configuration
├─ public/        # Public entry point (index.php) and assets
├─ src/           # Controllers, core classes, helpers
├─ views/         # Templates rendered using the View class
├─ css/ js/       # Static assets
└─ composer.json  # Composer configuration
```

## 🚀 Getting Started

### 1. Clone the repository
```bash
git clone https://github.com/nespapu/oopps.git
cd oopps
```

### 2. Install PHP dependencies

`composer install`

### 3. Configure your environment

- Create a .env file or update config/config.php with your database credentials.
- Import the SQL schema (WIP: add SQL script to /docs/sql/).

### 4. Run the app locally

`php -S localhost:8000 -t public`

Then visit:
👉 http://localhost:8000

## 🛣️ Roadmap

- [ ] Finish migrating old views to the new View class
- [ ] Add description and icons configuration to each exercise
- [ ] Remove legacy templates and unused assets
- [ ] Improve layout and UI consistency
- [ ] Add new exercise types
- [ ] Add usage statistics for users
- [ ] Add PHPUnit tests for controllers and services

## ❓ Why this project?

This app merges my two professional profiles:
- Software developer: applying clean code, MVC, refactoring, and architectural patterns in PHP.
- IT teacher: creating a real tool to practice and memorize exam topics more effectively.

## 📝 License

MIT License.
