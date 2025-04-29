
---

# 🕷 CryBug – Advanced Bug Tracking and Task Management System

CryBug is a modern, responsive, and role-based web application designed to streamline software development by tracking bugs, assigning tasks, and managing team workflows in a structured and collaborative environment.

---

## 🚀 Key Features

- Centralized dashboard with bug and task overviews  
- Role-based access (Company, Manager, Employee)  
- Smart bug reporting and tracking  
- Task allocation and project progress management  
- Real-time status updates and reports  
- Secure session management for all users  
- Interactive UI with animations (like the spider drop effect)

---

## 👥 User Roles Explained

### 🏢 **Company (Admin-Level Access)**

The Company role has the highest level of control in CryBug. This user can:

- Add, delete, or promote Managers  
- Update Manager salary  
- View and manage all Managers and Employees  
- Approve or respond to feedback from Managers  
- Add holidays to the company calendar  
- Manage account settings (name, profile picture)  
- View complete system activities and performance

> ✅ Best suited for business owners or system administrators

---

### 👨‍💼 **Manager (Mid-Level Access)**

The Manager role acts as a bridge between the Company and the Employees. This user can:

- Add, remove, or promote Employees  
- Update Employee salary  
- Assign bugs and projects to Employees  
- Apply for personal leaves  
- View and respond to Employee feedback  
- Raise feedback directly to the Company  
- Track team and project progress  
- Change own account settings  
- View recent activities for their team

> ✅ Best suited for project leads or technical team managers

---

### 👨‍💻 **Employee (Limited Access)**

The Employee role is focused on active development and project updates. This user can:

- View and update project progress  
- Update bug progress (status, comments)  
- Submit feedback to Managers  
- Apply for leave  
- Change own account settings  
- View assigned tasks and bugs

> ✅ Best suited for developers, QA engineers, and team members

---

## 🔐 Session Management

Each user type has protected access with personalized sessions and dashboards. The system ensures secure login and role-based redirection, so each user sees only the modules relevant to their role.

---

## 💻 Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript  
- **Styling:** Tailwind CSS, Custom CSS with responsive layout  
- **Backend:** PHP  
- **Database:** MySQL  
- **Icons:** Remix Icon, Flaticon  
- **Animations:** JavaScript-based interactive effects

---

## 🧪 Sample Use Cases

- A company adds a manager, promotes them, and gives them salary access.  
- The manager creates a new bug, assigns it to an employee, and monitors the resolution.  
- The employee updates the progress of the bug and project, and submits leave or feedback.

---

## 📁 Folder Structure (Simplified)

```
CryBug-Main/
├─ .vscode/                  # VS Code settings
│  └─ settings.json
├─ companies/                # Company login/signup and connection logic
│  ├─ company-Login.{css,js,php}
│  ├─ company-Signup.{css,js,php}
│  └─ connection.php
├─ companyProfile/           # Company dashboard and related features
│  ├─ analysis.php, feedback.php, holiday.php, team.php, etc.
│  ├─ dashboard.{php,js,css}
│  ├─ connection.php
│  └─ settings.php, logout.php, help.php
├─ employee/                 # Employee login/signup logic
│  ├─ emp-Login.{css,js,php}
│  ├─ emp-Signup.{css,js,php}
│  └─ connection.php
├─ employeeProfile/          # Employee dashboard and project/bug updates
│  ├─ dashboard.{php,js,css}, bug.php, project.php, setting.php, etc.
│  └─ connection.php
├─ images/                   # All images used in the UI
│  ├─ about-us/, bug/, gallery/, hero/, Logo/, Profile/, etc.
├─ leaders/                  # Manager login/signup and related auth pages
│  ├─ leader-Login.{css,js}, manager-Login.php
│  ├─ leader-Signup.{css,js}, manager-Signup.php
│  └─ connection.php, forgot-password.php
├─ login-pages/              # Generic login/signup pages and test HTML
│  ├─ login.{html,css,js}, signup.{html,css,js}
│  └─ test.html
├─ profile/                  # Possibly a shared or general user profile module
│  ├─ dashboard.{php,js,css}, bug.php, project.php, setting.php, etc.
│  └─ connection.php
├─ src/                      # Tailwind input/output CSS files
│  ├─ input.css
│  └─ output.css
├─ uploads/                  # Uploaded images
│  ├─ company_images/, employee_images/, manager_images/
├─ connection.php            # Global DB connection (if reused)
├─ index.php                 # Entry point
├─ logout.php                # Global logout handler
├─ package.json              # Node/Tailwind dependencies
├─ package-lock.json
├─ script.js                 # Global JS (if any)
├─ session_manager.php       # Handles role-based sessions
└─ style.css                 # Global styles
```

--- 

Let me know if you'd like a PDF version or a styled README template!
