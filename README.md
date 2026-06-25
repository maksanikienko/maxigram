# Maxigram

A real-time messaging application, built with Laravel 11, Vue 3, Inertia.js, and Laravel Reverb (WebSocket).

## Features

- One-to-one private messaging with real-time delivery
- Group chats with member management
- Image attachments with lightbox preview
- Emoji picker
- Typing indicators
- Online/offline presence status
- PWA notifications

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11.9, PHP 8.2+ |
| Frontend | Vue 3.4, Inertia.js, Vite 5.4 |
| Real-time | Laravel Reverb (WebSocket) + Laravel Echo |
| Auth | Laravel Sanctum + Laravel Breeze |
| Styling | Tailwind CSS 3 |
| Queue | Database driver |
| Database | SQLite (dev) / MySQL (prod) |

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- npm

## Installation

```bash
git clone https://github.com/your-username/maxigram.git
cd maxigram

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your `.env` file (see [Environment Variables](#environment-variables)), then run migrations:

```bash
php artisan migrate
# or with seed data:
php artisan migrate:fresh --seed
```

## Running Locally

You need four processes running simultaneously:

```bash
# 1. Backend server
php artisan serve

# 2. Frontend dev server (Vite)
npm run dev

# 3. WebSocket server (Reverb)
php artisan reverb:start

# 4. Queue worker
php artisan queue:work
```

Then open [http://localhost:8000](http://localhost:8000).

## Environment Variables

Key variables to configure in `.env`:

```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

# Database
DB_CONNECTION=sqlite          # or mysql for production

# Laravel Reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080

# Vite (client-side)
VITE_REVERB_APP_KEY=your-app-key
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

> Set `BROADCAST_CONNECTION=log` to disable WebSocket during development.

## Project Structure

```
app/
├── Events/           # MessageSent, GroupMessageSent broadcast events
├── Http/Controllers/ # ChatController, GroupController, ProfileController
├── Jobs/             # SendMessageJob — queued message dispatch
├── Models/           # User, Room, Message, ChatGroup, GroupMessage
└── Services/         # MessageService — message creation logic

resources/js/
├── Components/
│   ├── ChatComponent.vue       # Root chat component
│   └── parts/
│       ├── FriendsList.vue     # One-to-one chat list with presence
│       ├── GroupsList.vue      # Group list + create group
│       ├── Messages.vue        # Message feed + image lightbox
│       ├── MessageInput.vue    # Input field + emoji + file attach
│       └── MessengerHeader.vue # Active chat header
└── utils/
    ├── conectChannel.js        # WebSocket subscriptions + typing whisper
    ├── statusHandler.js        # Online/offline status
    ├── fileHandler.js          # Image attachment handling
    ├── emojiPicker.js          # Emoji picker composable
    ├── modalHandler.js         # Image lightbox
    └── scrollToBottom.js       # Auto-scroll to latest message
```

## API Routes

### Web (Inertia)

| Method | URL | Description |
|---|---|---|
| GET | `/chat/rooms` | List chat rooms |
| GET | `/chat/room/{room}/messages` | Room message history |
| POST | `/chat/room/{room}/send` | Send a message |

### API — Groups

| Method | URL | Description |
|---|---|---|
| GET | `/api/groups/get` | List groups |
| POST | `/api/groups/create` | Create a group |
| POST | `/api/groups/add/{groupId}/members` | Add member |
| GET | `/api/groups/get/{groupId}/messages` | Group message history |
| POST | `/api/groups/send/{groupId}/messages` | Send group message |

## Building for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
```

Use a process manager (e.g. Supervisor) to keep `reverb:start` and `queue:work` running.

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.
