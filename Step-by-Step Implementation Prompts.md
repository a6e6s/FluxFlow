Step-by-Step Implementation Prompts
You can feed these to an AI agent one by one to build the app systematically:

Step 1: Environment & Database Setup

"I am building a Laravel 12 project with Livewire 3 and Tailwind CSS. 
Set up migrations for projects (id, name, icon, color, sort_order, is_archived, is_active, priority 'is a larvel Enum') and 
tasks (id, project_id, title, is_active, description, status, position, due_date, labels, priority'is a larvel Enum'). with softdelete"

Step 2: Sidebar Component (The List)

"Build a Livewire component for the Sidebar. It should list active projects sorted by sort_order. Use Alpine.js to handle a 'New Project' modal and integrate livewire-sortable so projects can be reordered via drag-and-drop."

Step 3: Sidebar Component (The Archive)

"Enhance the Sidebar to include an 'Archive' section at the bottom. This section should be collapsed by default. Add a Livewire method archiveProject($id) that toggles the is_archived boolean and moves the project between lists instantly."

Step 4: Kanban Board Logic

"Create a Kanban board component that filters tasks based on the selected project. Create three columns: To Do, Doing, and Done. Implement drag-and-drop for tasks within and between columns using SortableJS, ensuring the status and position are updated in the database."

Step 5: Task Card Detail Enhancement

"Refactor the Task Card UI. Add small indicators for due dates (turns red if within 24 hours), a priority badge (High/Med/Low), and a visual 'complexity' score (1-5 dots). Ensure these are editable via a Livewire slide-over 'Detail' panel."