# 🚀 ZenFlow Pro: Collaborative Kanban Engine

ZenFlow Pro is a minimalist yet powerful project management workspace designed to bridge the gap between high-level project strategy and granular task execution. Built with the **TALL Stack**, it emphasizes fluid motion, glassmorphic design, and high-utility "power user" features.

---

## 🛠 Tech Stack & Architecture
- **Framework:** Laravel 12
- **Frontend:** Livewire 3 (Optimistic UI & Persistent Components)
- **State & Animation:** Alpine.js + SortableJS
- **Styling:** Tailwind CSS (Custom Glassmorphism)
- **Storage:** Laravel Storage (Local/S3) + Polymorphic Attachments

---

## 📂 Data Architecture


The system follows a strict ownership model:
- **Users** own **Projects**.
- **Projects** contain **Tasks** and can hold high-level **Attachments**.
- **Tasks** can be assigned to **Users** and hold their own **Attachments**.
- **Archiving** is handled via timestamps to preserve data history.

---

## 📅 Execution Roadmap & AI Prompts

### Phase 1: The Multi-Tenant Foundation
*Goal: Database schema and polymorphic relationships.*

> **AI Agent Prompt:**
> "Generate Laravel 12 migrations and models for ZenFlow Pro:
> 1. **User Model:** Default Auth + `profile_photo_path`.
> 2. **Project Model:** `user_id` (owner), `title`, `icon`, `color`, `sort_order` (int), `priority` (low/med/high) laravel Enum, and `archived_at` (timestamp).
> 3. **Task Model:** `project_id`, `assigned_to` (nullable user_id), `title`, `description`, `priority`, `status` (todo/doing/done) laravel Enum, `sort_order` (int), `due_date`, and `effort_score`.
> 4. **Attachment Model:** Polymorphic (`attachable_id`, `attachable_type`), `file_path`, `file_name`, `file_size`.
> 5. Relationships: User hasMany Projects; Project hasMany Tasks; both Projects/Tasks morphMany Attachments."
> 6. SoftDelete must be add to every migrations and models 

---

### Phase 2: The Smart Sidebar (Navigation)
*Goal: Persistent, draggable project list with archiving.*

> **AI Agent Prompt:**
> "Create a Livewire 3 component `ProjectSidebar`:
> 1. Use `@persist('sidebar')` to maintain state across navigation.
> 2. List projects for the authenticated user, ordered by `sort_order`.
> 3. Include an 'Archive' section that reveals projects where `archived_at` is NOT NULL.
> 4. Integrate **SortableJS** via Alpine.js for drag-and-drop reordering. On drop, call `reorderProjects(array $ids)`.
> 5. UI: Dark theme (`bg-slate-900`), border-left tabs using `project.color`, and a progress ring (done_tasks/total_tasks). as the layout.html"

---

### Phase 3: The Fluid Kanban Board
*Goal: The core workspace with cross-column task movement.*

> **AI Agent Prompt:**
> "Create a Livewire 3 component `KanbanBoard` (accepts Project ID):
> 1. Three columns: To Do, Doing, Done.
> 2. Task Card UI: Title, priority badge, effort score, and assignee avatar.
> 3. Indicators: Show a paperclip icon if `attachments_count > 0`.
> 4. Use **SortableJS** for dragging tasks between columns. 
> 5. Implement `moveTask($taskId, $newStatus, $newOrder)` with Optimistic UI updates (update UI before DB response)."

---

### Phase 4: Media & Details Slide-over
*Goal: Task editing and file management.*

> **AI Agent Prompt:**
> "Build a 'TaskDetails' slide-over component using Alpine.js and Livewire:
> 1. Form to update description, due_date, and assignee.
> 2. Implement a 'FileDropzone' using `Livewire\WithFileUploads`.
> 3. Support multiple uploads; store in the `attachments` table and link to the task.
> 4. Display a grid of files: image thumbnails for JPG/PNG, and file-type icons for others. 
> 5. Add an 'Archive Project' button in the board header that sets `archived_at = now()`."

---

### Phase 5: Power User UX & Polish
*Goal: Keyboard shortcuts and refined visuals.*

> **AI Agent Prompt:**
> "Add advanced UX features to ZenFlow Pro:
> 1. **Shortcuts:** Alpine.js `@keyup.window` for 'N' (New Task), 'P' (New Project), and 'Esc' (Close Modals).
> 2. **Visual Feedback:** Add a subtle outer glow to the sidebar icon of 'High Priority' projects.
> 3. **Search:** A global search bar to filter tasks across all active projects.
> 4. **Transitions:** Use Tailwind `transition-all` on task dragging for a weighted, tactile feel."

---

## 🎨 Visual Identity
- **Primary Surface:** `bg-slate-50` (Board) | `bg-slate-950` (Sidebar)
- **Card Design:** White background, 5% opacity borders, soft `shadow-sm`.
- **Typography:** Inter or System Sans-serif.
- **Accents:** Dynamic based on `project.color`.

---

## 🚀 Getting Started
1. `composer install && npm install`
2. `php artisan migrate`
3. `php artisan serve`
