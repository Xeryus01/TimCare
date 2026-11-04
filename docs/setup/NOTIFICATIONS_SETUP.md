# Notification System Configuration Guide

This document outlines the setup and configuration for the notification system including WhatsApp integration.

## Overview

The notification system consists of:
1. **In-App Notifications** - Database-stored, real-time notifications displayed in a UI bell icon
2. **WhatsApp Notifications** - SMS notifications sent via WhatsApp API when events occur
3. **Automatic Triggering** - Notifications are sent when:
   - Tickets are created
   - Ticket status changes
   - Tickets are resolved
   - Reservations are created
   - Reservations are approved
   - Assets are added to the system

## Database Setup

The following migrations have been applied:
- `2026_03_04_023652_create_notifications_table` - Creates the notifications table
- `2026_03_04_100000_add_phone_number_to_users_table` - Adds phone_number field to users

## Environment Variables

Add the following to your `.env` file:

```env
# WhatsApp Integration
WHATSAPP_ENABLED=true
WHATSAPP_API_URL=https://graph.instagram.com/v18.0/
WHATSAPP_API_KEY=your_whatsapp_api_key_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id_here
```

## WhatsApp API Integration

The system supports WhatsApp Business API (Meta/Facebook integration).

### Setup Steps:

1. Create a Meta Business Account at https://business.facebook.com
2. Set up a WhatsApp Business Account
3. Get your credentials:
   - Business Account ID
   - Phone Number ID
   - Permanent Access Token (API Key)
4. Add these to your `.env` file

### Supported Message Types:

The WhatsAppService automatically formats messages based on notification type:
- `ticket_created` - New ticket notification
- `ticket_updated` - Ticket status change
- `ticket_resolved` - Ticket completion
- `reservation_created` - Room reservation
- `reservation_approved` - Reservation approval
- `asset_created` - New asset added

## API Endpoints

### Get Notifications
```
GET /api/notifications
Authorization: Bearer {token}
```

### Get Unread Count
```
GET /api/notifications/unread-count
Authorization: Bearer {token}
```

### Get Latest Unread
```
GET /api/notifications/latest-unread
Authorization: Bearer {token}
```

### View Single Notification
```
GET /api/notifications/{notification_id}
Authorization: Bearer {token}
```

### Mark as Read
```
PATCH /api/notifications/{notification_id}/mark-as-read
Authorization: Bearer {token}
```

### Mark All as Read
```
PATCH /api/notifications/mark-all-as-read
Authorization: Bearer {token}
```

### Delete Notification
```
DELETE /api/notifications/{notification_id}
Authorization: Bearer {token}
```

## Frontend Features

### Notification Bell (Header)
- Shows unread count badge
- Auto-refreshes every 30 seconds
- Click to view latest unread notifications
- Link to full notifications page

### Notifications Page
- View all notifications with pagination
- Mark single or all notifications as read
- Delete notifications
- View related items (tickets, reservations, assets)
- Shows WhatsApp delivery status

### Notification Display
- Type-based emoji icons
- Creation timestamp
- Unread indicator
- Action links to related items
- Direct read/delete actions

## Customization

### Message Templates

Edit `app/Services/WhatsAppService.php` `formatNotificationMessage()` method to customize message templates for each notification type.

### Notification Types

Add new notification types in `app/Services/NotificationService.php`:

```php
public function notifyCustomEvent(User $user, $entity, bool $sendWhatsApp = true): Notification
{
    return $this->notify(
        $user,
        'custom_type',
        'Custom Title',
        'Custom message with ' . $entity->name,
        'entity_type',
        $entity->id,
        $sendWhatsApp
    );
}
```

Then call from your controller:
```php
$this->notificationService->notifyCustomEvent($user, $entity);
```

## Notification Flow

1. User performs action (create ticket, etc.)
2. Controller calls `NotificationService->notify*()`
3. NotificationService:
   - Creates database notification record
   - Checks user has `phone_number`
   - Calls WhatsAppService for SMS
4. WhatsAppService:
   - Formats message based on type
   - Sends HTTP request to WhatsApp API
   - Updates notification with delivery status
5. Frontend:
   - Polls `/api/notifications/unread-count` every 30 seconds
   - Updates badge and dropdown

## Testing

Test the notification system:

1. Create a test user with phone number
2. Create a ticket/reservation/asset
3. Check:
   - Database notifications table for record
   - Header bell icon shows unread count
   - Visit `/notifications` page to see full list
   - Check WhatsApp logs (if enabled)

## Troubleshooting

### Notifications not being created
- Check `logs/laravel.log` for exceptions
- Verify NotificationService is being called

### WhatsApp not sending
- Verify `WHATSAPP_ENABLED=true` in `.env`
- Check user has `phone_number` field filled
- Verify WhatsApp API credentials are correct
- Check `notifications.whatsapp_status` for error details

### Bell icon not updating
- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Check CORS if running separate frontend

## Architecture Notes

The notification system follows these design patterns:

- **Dependency Injection** - NotificationService receives WhatsAppService
- **Service Layer** - Business logic separated from controllers
- **Template Method** - Message formatting via WhatsAppService
- **Observer Pattern** - Can be expanded to use Laravel Observers
- **API First** - RESTful endpoints for frontend integration

## Future Enhancements

- [ ] Queue jobs for async WhatsApp sending
- [ ] Notification preferences/opt-out by type
- [ ] Notification categories and filtering
- [ ] Email notifications
- [ ] SMS via Twilio/SNS
- [ ] Push notifications
- [ ] WebSocket for real-time updates
- [ ] Notification templates UI
- [ ] Rate limiting per user
- [ ] Notification retention policies
