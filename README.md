# Blogs Email Notify – README

## Module Overview
----------------------

**Blogs Email Notify** is a lightweight Drupal custom module that listens for the creation of new nodes of type `blogs` and automatically sends an email notification to all users with the `content_editor` role. This helps keep editorial staff or stakeholders informed in real-time whenever new blog content is published.

The implementation uses Drupal's **Event Subscriber** system for a clean, scalable, and modern architecture.

-----------------------
## Requirements
-----------------------

To make this module work correctly, ensure the following:

### 1. **Content Type:**
- A content type must exist with the **machine name**: `blogs`
- This content type will trigger the email notifications when a node is created.

### 2. **User Roles:**
- At least one user should exist with the **`content_editor`** role.
- These users will receive the email notifications.
- Users must be:
  - Active (`status = 1`)
  - Have a valid email address

### 3. **SMTP Mail System (Recommended):**
- To reliably send emails, especially during development, configure the **SMTP Authentication Support** module.
- Install via composer or manually:  
  `composer require drupal/smtp`

- Enable:  
  `drush en smtp`

- Sample Configuration:
  - SMTP server: `smtp.gmail.com`
  - Port: `587`
  - Encryption: `TLS`
  - Username: your Gmail address (e.g., `yourname@gmail.com`)
  - Password: **App Password** (not your Gmail password)
  - From address: same as SMTP account

> Note: You must enable "App Password" in your Google account if 2FA is enabled.

### 4. **Hook Event Dispatcher (Dependency)**
- This module uses **Event Subscriber**, so it relies on Drupal Core events and Symfony’s dispatcher system.
- No additional module like `hook_event_dispatcher` is needed for core entity events, but you should be aware of its utility if listening to contributed module events in future.

--------------------------
## Installation & Setup
--------------------------

1. Place the `blogs_email_notify` module folder in your `/modules/custom/` directory.
2. Enable the module:
   - Using UI: Admin → Extend
   - Or CLI: `drush en blogs_email_notify`
3. Clear cache:
   - `drush cr` or use Admin UI
4. Create or confirm the `blogs` content type exists.
5. Ensure test users with `content_editor` role exist and have valid emails.
6. Configure SMTP module and send test email to ensure setup works.

-------------------
## How It Works
-------------------

- The module listens to the **EntityInsertEvent** for new nodes.
- When a node of type `blogs` is created:
  - The event subscriber fetches all active users with the `content_editor` role.
  - It then uses Drupal’s mail manager to send an email to each user.
  - Email includes the blog title and a link to the node.

----------------------
## Troubleshooting
----------------------

If emails are **not being sent** or **errors are logged**, verify the following:

| Issue | Suggested Fix |
|-------|----------------|
| SMTP error | Check SMTP module is enabled and properly configured |
| No email received | Check spam/junk folder, and ensure SMTP "from" address is valid |
| Event not triggering | Make sure content type machine name is **exactly `blogs`** |
| No users found | Ensure at least one user has `content_editor` role and is active |
| No logs | Check Drupal logs: `/admin/reports/dblog` (enable Database Logging if needed) |
| Debugging help | Add `\Drupal::logger()` lines in subscriber for diagnostics |
| Test email not working | Try sending test from SMTP module admin settings to verify mail setup |

----------------------------
## Optional Enhancements
----------------------------

- Allow configurable content types or roles via settings form
- Add multilingual support to emails
- Queue emails using Drupal’s queue system for high volume sites
- Track delivery status using Mail Log module
