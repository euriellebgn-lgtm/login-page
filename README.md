# login-page
Flashcard App: Full-Stack Quiz & Admin System
A secure, database-driven application for personalized learning and user management.

Technical Stack
-Backend: PHP 

_Database: MySQL (Relational architecture with foreign key constraints)

-Frontend: HTML5, CSS3, JavaScript 

-Security: Session-based Authentication, Prepared Statements 

Professional Features
-Robust Multi-Role Authentication
-Developed a secure entry point with distinct logic for Users and Administrators
-Session Management: Tracks user state, roles, and error messages across page refreshes.
-Role-Based Access: Automatically redirects unauthorized users; Admins access a management dashboard while Users are directed to their personal study space.

Dynamic Flashcard Engine
-A CRUD (Create, Read, Update, Delete) system that allows users to build custom study sets
-Database Relationships: Implemented a "One-to-Many" relationship where one User owns many Decks, and one Deck owns many Flashcards.
-Relational Integrity: Deleting a deck automatically triggers a clean-up of all associated flashcards in the database.

Asynchronous Quiz System
Created an interactive study tool using JavaScript Fetch API:
-Zero-Refresh Updates: Quizzes are fetched from the server and rendered dynamically without reloading the page.
-Instant Feedback: A real-time grading logic that compares user input against the database values, providing immediate visual scores.

Administrative Dashboard
-A dedicated control panel for system management
-User Oversight: Allows admins to view and manage the entire user table.
-Protected Logic: Self-deletion protection prevents admins from accidentally removing their own accounts.
