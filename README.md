Setup Instructions

Clone the repository:

git clone https://github.com/XcviiNB/mini_project_management_system
cd mini_project_management_system

Update dependencies:
composer update
npm update

Create and configure your .env file:
copy .env.example to .env
php artisan key:generate

Run database migrations and seeders:
php artisan migrate --seed



Eloquent ORM and Query Builder
I used ORM and Query Builder at ProjectController


Implemented Routes
Public
/ → Welcome page
/dashboard → User dashboard (auth + verified)

Profile (auth)
GET /profile → Edit profile
PATCH /profile → Update profile
DELETE /profile → Delete profile

Projects (auth, role: admin, manager)
GET /projects → List projects
GET /projects/create → Create form
POST /projects → Store project
GET /projects/{project} → Show project
GET /projects/{project}/edit → Edit form
PUT /projects/{project} → Update project
DELETE /projects/{project} → Delete project

Tasks (auth, role: admin, manager, developer)
GET /tasks → List tasks
GET /tasks/create → Create form
POST /tasks → Store task
GET /tasks/{task} → Show task
GET /tasks/{task}/edit → Edit form
PUT /tasks/{task} → Update task
DELETE /tasks/{task} → Delete task
GET /users/{user}/tasks → Show tasks by user
POST /tasks/{task}/status → Update task status

Task Comments (auth, role: admin, manager, developer)
POST /tasks/{task}/comments → Store comment
GET /comments/{comment} → Show comment
GET /comments/{comment}/edit → Edit comment
PUT /comments/{comment} → Update comment
DELETE /comments/{comment} → Delete comment
