# Demeter - Intelligent Waste Management Platform

**Live Website:** [demeter-production-7e52.up.railway.app]

Demeter is a state-of-the-art, AI-powered waste management system designed to optimize waste collection and monitoring for university campuses. Built with Laravel and integrated with Google's Gemini AI, Demeter provides real-time analytics, automated scheduling, and an interactive assistant to streamline campus sustainability efforts.

---

## Key Features

- **Interactive AI Assistant**: An integrated chat interface powered by `gemini-2.5-flash-lite` that provides real-time, data-driven insights on campus waste metrics, scheduling, and environmental impact.
- **Real-time Analytics Dashboard**: Visualizes current bin capacities, historical waste data, and collection trends across different campus locations.
- **Smart Bin Tracking**: Registers and monitors individual smart bins across various buildings, tracking their current weight, capacity, and fill status.
- **Secure Architecture**: Fully hardened with custom rate limiting, prompt injection protection, comprehensive XSS/SQLi prevention, and strict authentication middleware.
- **Responsive Interface**: A modern, dynamic user interface optimized for both administrators and regular campus users to easily access data on the go.

---

## Technology Stack

- **Backend Framework**: Laravel 12 (PHP 8.4)
- **Database**: PostgreSQL (hosted on Neon)
- **Frontend**: Blade Templating Engine, Vanilla CSS, Vanilla JS
- **Asset Compilation**: Vite
- **Web Server**: Nginx
- **AI Integration**: Google Gemini API

---

## Security Implementations

Demeter is built with robust security measures to ensure data integrity and user safety in a production environment:
- Strict AI system prompts and regex filters to prevent prompt injection and character-breaking.
- API endpoint rate-limiting (max 20 requests/minute) to prevent abuse and API exhaustion.
- Enforced session security (Strict SameSite, encrypted sessions).
- Complete sanitization of user input to prevent XSS (Cross-Site Scripting) and HTML injection.
- Parameterized database queries to guarantee protection against SQL injection.

---

## License
This project is open-source and available under the [MIT License](LICENSE).
