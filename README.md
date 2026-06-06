# 🏠 NestWay – Student PG Accommodation Website

A fully functional, responsive web application that helps students find and shortlist PG (Paying Guest) accommodations near their college.

---

## 🌐 Live Demo
🔗 [http://nestway.freehosting.dev](http://nestway.freehosting.dev)

## 📁 GitHub Repository
🔗 [https://github.com/smrutimayeesahoo07-ai/nestway](https://github.com/smrutimayeesahoo07-ai/nestway)

---

## 📌 Project Overview

NestWay is a student accommodation platform where users can:
- Browse PG listings with images, prices, ratings and amenities
- Filter properties by city, budget, and gender type
- View detailed information about each property
- Register and login to their account
- Mark interest in properties
- Shortlist favourite PGs using a React-powered interface

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5 |
| Backend | PHP |
| Database | MySQL |
| Dynamic UI | JavaScript, AJAX |
| Component | React (via CDN) |
| Local Server | XAMPP |
| Deployment | InfinityFree |

---

## 📂 Project Structure

```
nestway/
├── db.php                  # Database connection file
├── index.php               # Property listing page (AJAX-powered)
├── index.html              # Static version of listing page
├── property-detail.php     # Property detail page (dynamic)
├── property-detail.html    # Static version of detail page
├── filter.php              # AJAX handler for live filtering
├── interest.php            # AJAX handler for mark interest
├── login.php               # User login page
├── signup.php              # User registration page
├── logout.php              # Session logout
├── shortlist.html          # React-based shortlist component
└── nestway_db.sql          # MySQL database schema + sample data
```

---

## 🗄️ Database Design

The database contains 5 tables:

| Table | Description |
|-------|-------------|
| `users` | Stores student login information |
| `properties` | Stores all PG listing details |
| `amenities` | Master list of all amenities |
| `property_amenities` | Links properties to their amenities |
| `interested_users` | Tracks which student liked which PG |

---

## ⚙️ Features

- Property listing page with filters (city, budget, gender)
- Live AJAX filtering without page reload
- Property detail page with image gallery and amenities
- User registration and login with secure password hashing
- Mark interest in properties saved to database
- React shortlist component with sort, search and compare
- Fully responsive design using Bootstrap 5

---

## 🚀 How to Run Locally

1. Install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Copy project files to `C:\xampp\htdocs\nestway\`
3. Start Apache and MySQL in XAMPP Control Panel
4. Open `http://localhost/phpmyadmin` and import `nestway_db.sql`
5. Open `http://localhost/nestway/index.php`

---

## 👨‍💻 Developer

**Smrutimayee Sahoo**
Student – CV Raman Global University
smrutimayeesahoo07@gmail.com
