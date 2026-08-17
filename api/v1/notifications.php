<?php
/**
 * Notifications API - polling endpoint for admin notifications
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

// Only admins can access notifications
if (!Auth::check() || !Auth::isAdmin()) {
    jsonResponse(['success' => false, 'message' => 'No autorizado'], 401);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'unread':
        $notifications = Notification::getUnread();
        $count = Notification::getUnreadCount();
        jsonResponse([
            'success' => true,
            'notifications' => $notifications,
            'count' => $count,
        ]);
        break;

    case 'mark_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        if (!CSRF::validateHeader()) {
            jsonResponse(['success' => false, 'message' => 'Token CSRF inválido'], 419);
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Notification::markRead($id);
        }
        jsonResponse(['success' => true, 'count' => Notification::getUnreadCount()]);
        break;

    case 'mark_all_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        if (!CSRF::validateHeader()) {
            jsonResponse(['success' => false, 'message' => 'Token CSRF inválido'], 419);
        }
        Notification::markAllRead();
        jsonResponse(['success' => true, 'count' => 0]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Acción inválida'], 400);
}
