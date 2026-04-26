# 🌊 FluxFlow
## Tasks that Grow, Work that Flows
**A Modern Project & Task Management Dashboard**

FluxFlow is a high-productivity Kanban workspace designed to bridge the gap between high-level project strategy and granular task execution. Built with the TALL Stack (Tailwind, Alpine.js, Laravel, Livewire), it emphasizes fluid motion, a theme-aware interface, API access, and power-user features.

![FluxFlow Dashboard](public/screenshots/dashboard.png)

---

## ✨ Features

### 📁 Smart Project Sidebar
- **Drag & Drop Reordering** - Prioritize projects by dragging them into your preferred order
- **Visual Identity** - Custom emoji icons and color-coded project tabs
- **Progress Rings** - Real-time completion percentage for each project
- **Priority Glow** - High-priority projects feature a subtle red glow indicator
- **Compact Mode** - Collapse the sidebar to icon-only view with a quick toggle
- **The Vault** - Collapsible archive section for completed or paused projects

![Project Sidebar](public/screenshots/sidebar.png)

### 📋 Fluid Kanban Board
- **Four Status Columns** - To Do, In Progress, Review, and Done
- **Cross-Column Drag & Drop** - Move tasks between columns with smooth animations
- **Task Cards** featuring:
  - Priority badges (Low/Medium/High)
  - Due date indicators with overdue warnings
  - Effort score points
  - Assignee avatars
  - Attachment count indicators
- **Optimistic UI** - Instant visual feedback before server confirmation

![Kanban Board](public/screenshots/kanban.png)

### 📝 Task Details Slide-over
- **Quick Edit Panel** - Update task details without leaving the board
- **File Dropzone** - Drag & drop file uploads with progress indicators
- **Attachment Grid** - Image thumbnails and file-type icons
- **Multi-file Support** - Upload multiple files at once (max 10MB each)

![Task Details](public/screenshots/task-details.png)

### 🔍 Global Search
- **Universal Search** - Find projects and tasks across your workspace
- **Keyboard Shortcut** - Press `⌘K` or `Ctrl+K` to open instantly
- **Smart Results** - Grouped by projects and tasks with status indicators

![Global Search](public/screenshots/search.png)

### ⚡ Power User Features
- **Keyboard Shortcuts**
  - `N` - Create new task (when project is selected)
  - `P` - Create new project
  - `ESC` - Close any modal
  - `⌘K` / `Ctrl+K` - Open global search
- **Smooth Animations** - Weighted, tactile drag interactions
- **Theme Persistence** - Light and dark mode stay in sync across refreshes
- **API Key Management** - Generate, regenerate, and copy a personal API key from the user menu

---

## 🛠 Tech Stack

| Technology | Purpose |
|------------|---------|
| **Laravel 13** | Backend framework & API |
| **Livewire 4** | Real-time UI components |
| **Alpine.js** | Frontend interactivity & animations |
| **Tailwind CSS 4.1** | Utility-first styling |
| **SortableJS** | Drag & drop functionality |
| **MariaDB** | Primary database engine |
| **Flux UI** | Pre-built UI components |
| **Scramble** | Generated OpenAPI documentation |

---

## 📦 Installation

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+
- npm or yarn

### Setup Steps

```bash
# Clone the repository
git clone https://github.com/a6e6s/FluxFlow.git
cd fluxflow

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed sample data (optional)
php artisan db:seed

# Create storage symlink
php artisan storage:link

# Build assets
npm run build

# Start the development server
php artisan serve
```

### Development Mode

```bash
# Run with hot reload
npm run dev

# In a separate terminal (disable Xdebug for better performance)
XDEBUG_MODE=off php artisan serve
```

### API Access

The workspace API is available under `/api/v1` and is authenticated with the API key created from the user dropdown.

1. Open the user menu in the top-right corner.
2. Choose **API Key**.
3. Generate or regenerate a key, then copy it.
4. Send it with either `Authorization: Bearer <api-key>` or `X-API-Key: <api-key>`.

The API documentation is generated with Scramble and is available at `/docs/api`.

#### Authentication

- The API accepts a single user-scoped key per account.
- The key is stored encrypted, while a SHA-256 hash is used for request authentication.
- Requests without a valid key receive `401 Unauthenticated.`

#### Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/projects` | List the authenticated user’s active projects |
| `POST` | `/api/v1/projects` | Create a project |
| `GET` | `/api/v1/projects/{project}` | Show a project |
| `PUT` / `PATCH` | `/api/v1/projects/{project}` | Update a project |
| `DELETE` | `/api/v1/projects/{project}` | Delete a project |
| `GET` | `/api/v1/projects/{project}/tasks` | List tasks for a project |
| `POST` | `/api/v1/projects/{project}/tasks` | Create a task inside a project |
| `GET` | `/api/v1/projects/{project}/tasks/{task}` | Show a task |
| `PUT` / `PATCH` | `/api/v1/projects/{project}/tasks/{task}` | Update a task |
| `DELETE` | `/api/v1/projects/{project}/tasks/{task}` | Delete a task |

#### Project Payload

- `title` - required string, 2-100 characters
- `icon` - optional string, up to 50 characters
- `color` - required hex color like `#3b82f6`
- `priority` - `low`, `medium`, or `high`
- `sort_order` - optional non-negative integer

#### Task Payload

- `title` - required string, 2-255 characters
- `description` - optional string
- `priority` - `low`, `medium`, or `high`
- `status` - `todo`, `doing`, `review`, or `done`
- `sort_order` - optional non-negative integer
- `due_date` - optional date
- `effort_score` - optional integer from 1 to 10
- `assigned_to` - optional user ID

#### Response Shape

- Project responses include `id`, `title`, `icon`, `color`, `priority`, `sort_order`, `archived_at`, `created_at`, and `updated_at`.
- Task responses include `id`, `project_id`, `assigned_to`, `title`, `description`, `priority`, `status`, `sort_order`, `due_date`, `effort_score`, `created_at`, and `updated_at`.

---

## 📂 Project Structure

```
fluxflow/
├── app/
│   ├── Enums/
│   │   ├── Priority.php          # Low, Medium, High
│   │   └── TaskStatus.php        # Todo, Doing, Review, Done
│   ├── Livewire/
│   │   ├── CreateProjectModal.php
│   │   ├── CreateTaskModal.php
│   │   ├── GlobalSearch.php
│   │   ├── KanbanBoard.php
│   │   ├── ProjectSidebar.php
│   │   └── TaskDetails.php
│   └── Models/
│       ├── Attachment.php        # Polymorphic file attachments
│       ├── Project.php
│       ├── Task.php
│       └── User.php
├── database/
│   ├── factories/
│   │   ├── ProjectFactory.php
│   │   ├── TaskFactory.php
│   │   └── UserFactory.php
│   └── migrations/
│       ├── create_users_table.php
│       ├── create_projects_table.php
│       ├── create_tasks_table.php
│       └── create_attachments_table.php
├── resources/
│   ├── css/
│   │   └── app.css               # Custom styles & animations
│   └── views/
│       ├── components/
│       │   └── layouts/
│       │       └── kanban.blade.php
│       └── livewire/
│           ├── create-project-modal.blade.php
│           ├── create-task-modal.blade.php
│           ├── global-search.blade.php
│           ├── kanban-board.blade.php
│           ├── project-sidebar.blade.php
│           ├── task-details.blade.php
│           └── partials/
│               ├── kanban-column.blade.php
│               └── task-card.blade.php
└── routes/
    └── web.php
```

---

## 🎨 Data Models

### Project
| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | foreignId | Project owner |
| `title` | string | Project name |
| `icon` | string | Emoji icon (optional) |
| `color` | string | Hex color code |
| `priority` | enum | low, medium, high |
| `sort_order` | int | Display order |
| `archived_at` | timestamp | Archive date (nullable) |

### Task
| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `project_id` | foreignId | Parent project |
| `assigned_to` | foreignId | Assigned user (nullable) |
| `title` | string | Task title |
| `description` | text | Task details (nullable) |
| `priority` | enum | low, medium, high |
| `status` | enum | todo, doing, review, done |
| `sort_order` | int | Column order |
| `due_date` | date | Due date (nullable) |
| `effort_score` | tinyint | Complexity points 1-10 |

### Attachment (Polymorphic)
| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `attachable_id` | bigint | Parent ID |
| `attachable_type` | string | Parent model class |
| `file_path` | string | Storage path |
| `file_name` | string | Original filename |
| `file_size` | int | Size in bytes |

---

## ⌨️ Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `N` | New Task (requires selected project) |
| `P` | New Project |
| `ESC` | Close modal/slide-over |
| `⌘K` / `Ctrl+K` | Open Global Search |
| Sidebar arrow | Collapse or expand the project list |
| `Enter` | Submit form / Select search result |

---

## 🎯 Usage Guide

### Creating a Project
1. Click the **+** button beside Active Projects, or press **P**
2. Enter project name, select icon, color, and priority
3. Click "Create Project"

### Managing Your API Key
1. Open the user dropdown in the top-right corner
2. Choose **API Key**
3. Generate or regenerate a key
4. Copy the key and use it in API requests

### Creating a Task
1. Select a project from the sidebar
2. Click "Add Task" button or press **N**
3. Fill in task details (title, description, priority, due date)
4. Click "Create Task"

### Moving Tasks
1. Drag a task card from any column
2. Drop it into the target column
3. Tasks automatically save their new status

### Reordering Projects
1. Hover over a project in the sidebar
2. Grab the drag handle (⋮⋮ icon)
3. Drag to reorder

### Editing Task Details
1. Click on any task card
2. Update fields in the slide-over panel
3. Upload attachments via drag & drop
4. Click "Save Changes"

### Archiving Projects
1. Open a project
2. Click the "Archive" button in the header
3. Confirm the action
4. Find archived projects in the "Archived" section of the sidebar

### Searching
1. Press `⌘K` or click the search bar
2. Type your search query
3. Click a result to navigate directly

### Theme Behavior
1. Use the theme button in the header to switch between light and dark mode
2. Your selection persists after refresh
3. Modals, drawers, and the sidebar follow the same saved appearance

---

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME=FluxFlow
APP_ENV=local
APP_DEBUG=true

# Database (MariaDB/MySQL)
DB_CONNECTION=mysql

# Uncomment and configure as needed:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=fluxflow
# DB_USERNAME=root
# DB_PASSWORD=

# Performance
BCRYPT_ROUNDS=10
SESSION_DRIVER=file
CACHE_STORE=file
```

---

## 🚀 Performance Tips

1. **Disable Xdebug** in development:
   ```bash
   XDEBUG_MODE=off php artisan serve
   ```

2. **Use file-based cache** instead of database:
   ```env
   CACHE_STORE=file
   SESSION_DRIVER=file
   ```

3. **Enable OPcache** in production for faster PHP execution

4. **Run production build**:
   ```bash
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📞 Support

If you have any questions or run into issues, please [open an issue](https://github.com/a6e6s/FluxFlow.git/issues) on GitHub.

---

<p align="center">
  Built with ❤️ using the TALL Stack
</p>



## next update will focus on:
 - [ ] adding team management
 - [ ] adding user tasks and project management
