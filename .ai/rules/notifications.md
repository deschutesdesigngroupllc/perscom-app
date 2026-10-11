---
paths:
  - 'app/Notifications/**'
---

# Notifications

## Notifications are queued and mail through Mailables
Notifications implement ShouldQueue and build via() from the NotificationChannel enum filtered by the notifiable's preferences. toMail() returns a dedicated App\Mail\{Area} Mailable ->to($notifiable->email), not a MailMessage. Build broadcast/database payloads with Filament Notification.
