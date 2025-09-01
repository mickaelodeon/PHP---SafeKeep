<?php
require_once 'includes/session.php';

// Check if user is logged in
if (!SessionManager::isLoggedIn()) {
    header('Location: login.html');
    exit();
}

$username = SessionManager::getUsername();
$role = SessionManager::getUserRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - SafeKeep</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.37);
            --shadow-hover: 0 15px 35px rgba(31, 38, 135, 0.2);
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --bg-chat: #f7fafc;
            --message-sent: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --message-received: linear-gradient(135deg, #e2e8f0 0%, #f7fafc 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* Glass Morphism Navigation */
        .navbar {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: var(--shadow-soft);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--text-primary) !important;
        }

        .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 20px;
            padding: 8px 16px !important;
            margin: 0 4px;
        }

        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-1px);
            background: rgba(102, 126, 234, 0.1);
        }

        .nav-link.active {
            background: var(--primary-gradient) !important;
            color: white !important;
        }

        /* Main Container */
        .main-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-soft);
            margin: 20px 0;
            overflow: hidden;
        }

        /* Chat Layout */
        .chat-container {
            height: 85vh;
            display: flex;
        }

        /* Sidebar */
        .chat-sidebar {
            width: 380px;
            background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
            border-right: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid var(--glass-border);
            background: var(--primary-gradient);
            color: white;
        }

        .sidebar-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-subtitle {
            opacity: 0.9;
            font-size: 0.9rem;
            margin: 0;
        }

        .conversation-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .conversation-item {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
        }

        .conversation-item:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .conversation-item.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-soft);
        }

        .conversation-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--secondary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .conversation-info {
            flex: 1;
            margin-left: 12px;
            min-width: 0;
        }

        .conversation-name {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-preview {
            font-size: 0.875rem;
            opacity: 0.7;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            flex-shrink: 0;
        }

        .conversation-time {
            font-size: 0.75rem;
            opacity: 0.6;
            margin-bottom: 4px;
        }

        .unread-badge {
            background: var(--secondary-gradient);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Chat Area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-chat);
        }

        .chat-header {
            padding: 20px 24px;
            background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
            border-bottom: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
        }

        .chat-partner-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 12px;
        }

        .chat-partner-info {
            flex: 1;
        }

        .chat-partner-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .chat-partner-status {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin: 0;
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .message {
            max-width: 75%;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            animation: messageAppear 0.4s ease;
        }

        .message.sent {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message.received {
            align-self: flex-start;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--secondary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .message-content {
            background: var(--message-received);
            padding: 14px 18px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            word-wrap: break-word;
        }

        .message.sent .message-content {
            background: var(--message-sent);
            color: white;
        }

        .message-text {
            margin: 0;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 6px;
        }

        @keyframes messageAppear {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Message Input */
        .message-input-container {
            padding: 20px 24px;
            background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
            border-top: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }

        .message-input-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .message-input {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--glass-border);
            border-radius: 24px;
            padding: 14px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            resize: none;
            min-height: 50px;
            max-height: 120px;
        }

        .message-input:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .send-button {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .send-button:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: var(--shadow-hover);
        }

        .send-button:active {
            transform: translateY(0) scale(1);
        }

        .send-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            padding: 40px;
        }

        .empty-state-icon {
            font-size: 4rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .empty-state-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 400px;
            line-height: 1.6;
        }

        /* Loading States */
        .loading-spinner {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(102, 126, 234, 0.3);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            margin-bottom: 16px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--text-secondary);
            animation: typingAnimation 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-10px); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chat-sidebar {
                position: absolute;
                left: -380px;
                transition: left 0.3s ease;
                z-index: 1000;
                height: 100%;
                width: 300px;
            }

            .chat-sidebar.open {
                left: 0;
            }

            .chat-main {
                width: 100%;
            }

            .main-container {
                margin: 10px;
                border-radius: 16px;
            }

            .chat-container {
                height: 90vh;
            }
        }

        /* Scroll Styles */
        .conversation-list::-webkit-scrollbar,
        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .conversation-list::-webkit-scrollbar-track,
        .messages-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .conversation-list::-webkit-scrollbar-thumb,
        .messages-container::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 3px;
        }

        .conversation-list::-webkit-scrollbar-thumb:hover,
        .messages-container::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.5);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <img src="assets/safekeeplogo.png" alt="SafeKeep Logo" width="35" height="35" class="me-2">
                <strong>SafeKeep</strong>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($role === 'admin'): ?>
                        <!-- Admin Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">
                                <i class="bi bi-shield-check me-1"></i>Admin Panel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse.php">
                                <i class="bi bi-search me-1"></i>Browse Items
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Student Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-house me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse.php">
                                <i class="bi bi-search me-1"></i>Browse Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="messages.php">
                                <i class="bi bi-chat-dots me-1"></i>Messages
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><span id="username"><?= htmlspecialchars($username) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <?php if ($role === 'admin'): ?>
                            <li><a class="dropdown-item" href="admin.php" id="adminMenuItem"><i class="bi bi-shield-check"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Chat Container -->
    <div class="container-fluid">
        <div class="main-container">
            <div class="chat-container">
                <!-- Sidebar -->
                <div class="chat-sidebar">
                    <div class="sidebar-header">
                        <h2 class="sidebar-title">
                            <i class="bi bi-chat-heart me-2"></i>Messages
                        </h2>
                        <p class="sidebar-subtitle">Connect with the SafeKeep community</p>
                    </div>
                    
                    <div class="conversation-list" id="conversationsList">
                        <div class="empty-state">
                            <div class="loading-spinner"></div>
                            <p class="mt-3 mb-0">Loading conversations...</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Main Area -->
                <div class="chat-main" id="chatMain">
                    <div class="empty-state">
                        <i class="bi bi-chat-quote empty-state-icon"></i>
                        <h3 class="empty-state-title">Welcome to SafeKeep Messages</h3>
                        <p class="empty-state-subtitle">Select a conversation to start chatting securely about lost and found items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentConversationId = null;
        let messagePollingInterval = null;
        let conversationPollingInterval = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadConversations();
            
            // Check if we should open contact modal for specific item
            checkContactParameter();
            
            // Start polling for new messages
            startPolling();
        });

        // Get URL parameter for item contact
        function checkContactParameter() {
            const urlParams = new URLSearchParams(window.location.search);
            const itemId = urlParams.get('item');
            
            if (itemId) {
                // Show contact modal or start conversation
                console.log('Contact item:', itemId);
                // You can implement a direct contact feature here
            }
        }

        // Check authentication
        async function checkAuth() {
            try {
                const response = await fetch('api/check_session.php');
                const data = await response.json();
                
                if (data.success && data.user) {
                    document.getElementById('username').textContent = data.user.username;
                    
                    if (data.user.role === 'admin') {
                        const adminMenuItem = document.getElementById('adminMenuItem');
                        if (adminMenuItem) {
                            adminMenuItem.style.display = 'block';
                        }
                    }
                } else {
                    // Fallback to server-side session check (we already passed PHP auth)
                    // Don't redirect since PHP session is valid
                    console.log('API session check failed, but PHP session is valid');
                    document.getElementById('username').textContent = '<?php echo htmlspecialchars($username); ?>';
                    
                    <?php if ($role === 'admin'): ?>
                    const adminMenuItem = document.getElementById('adminMenuItem');
                    if (adminMenuItem) {
                        adminMenuItem.style.display = 'block';
                    }
                    <?php endif; ?>
                }
            } catch (error) {
                console.error('Auth check error:', error);
                // Fallback to server-side session check (we already passed PHP auth)
                // Don't redirect since PHP session is valid
                document.getElementById('username').textContent = '<?php echo htmlspecialchars($username); ?>';
                
                <?php if ($role === 'admin'): ?>
                const adminMenuItem = document.getElementById('adminMenuItem');
                if (adminMenuItem) {
                    adminMenuItem.style.display = 'block';
                }
                <?php endif; ?>
            }
        }

        // Load conversations
        async function loadConversations() {
            try {
                const response = await fetch('api/conversations.php');
                const data = await response.json();
                
                const conversationsList = document.getElementById('conversationsList');
                
                if (data.success && data.conversations && data.conversations.length > 0) {
                    conversationsList.innerHTML = '';
                    
                    data.conversations.forEach(conversation => {
                        const conversationEl = createConversationElement(conversation);
                        conversationsList.appendChild(conversationEl);
                    });
                } else {
                    conversationsList.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-chat-text empty-state-icon" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.5;"></i>
                            <h4 style="margin-bottom: 8px; color: var(--text-secondary);">No conversations yet</h4>
                            <p style="color: var(--text-secondary); margin: 0;">Start by contacting item owners through the Browse page</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
                document.getElementById('conversationsList').innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #e53e3e; margin-bottom: 16px;"></i>
                        <h4 style="color: #e53e3e;">Error loading conversations</h4>
                        <p style="color: var(--text-secondary);">Please refresh the page and try again</p>
                    </div>
                `;
            }
        }

        // Create conversation element
        function createConversationElement(conversation) {
            const div = document.createElement('div');
            div.className = 'conversation-item';
            div.dataset.conversationId = conversation.id;
            
            const otherUser = conversation.other_user;
            const avatarInitial = otherUser.username.charAt(0).toUpperCase();
            const lastMessage = conversation.last_message || 'No messages yet';
            const timeAgo = formatTimeAgo(conversation.updated_at);
            
            div.innerHTML = `
                <div class="conversation-avatar">${avatarInitial}</div>
                <div class="conversation-info">
                    <div class="conversation-name">${otherUser.username}</div>
                    <div class="conversation-preview">${lastMessage}</div>
                </div>
                <div class="conversation-meta">
                    <div class="conversation-time">${timeAgo}</div>
                    ${conversation.unread_count > 0 ? `<div class="unread-badge">${conversation.unread_count}</div>` : ''}
                </div>
            `;
            
            div.addEventListener('click', () => selectConversation(conversation.id, otherUser));
            
            return div;
        }

        // Select conversation
        async function selectConversation(conversationId, otherUser) {
            currentConversationId = conversationId;
            
            // Update UI
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('active');
            
            // Update chat header and load messages
            updateChatHeader(otherUser);
            await loadMessages(conversationId);
            showChatInterface();
        }

        // Update chat header
        function updateChatHeader(otherUser) {
            const avatarInitial = otherUser.username.charAt(0).toUpperCase();
            
            const chatHeader = `
                <div class="chat-partner-avatar">${avatarInitial}</div>
                <div class="chat-partner-info">
                    <h3 class="chat-partner-name">${otherUser.username}</h3>
                    <p class="chat-partner-status">Active on SafeKeep</p>
                </div>
            `;
            
            document.querySelector('.chat-main').innerHTML = `
                <div class="chat-header">${chatHeader}</div>
                <div class="messages-container" id="messagesContainer">
                    <div class="text-center">
                        <div class="loading-spinner"></div>
                        <p class="mt-3">Loading messages...</p>
                    </div>
                </div>
                <div class="message-input-container">
                    <form class="message-input-form" id="sendMessageForm">
                        <textarea class="message-input" id="messageInput" placeholder="Type your message..." rows="1"></textarea>
                        <button type="submit" class="send-button">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            `;
            
            // Add form event listener
            document.getElementById('sendMessageForm').addEventListener('submit', handleSendMessage);
            
            // Auto-resize textarea
            const messageInput = document.getElementById('messageInput');
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        }

        // Show chat interface
        function showChatInterface() {
            const chatMain = document.getElementById('chatMain');
            chatMain.style.display = 'flex';
        }

        // Load messages
        async function loadMessages(conversationId) {
            try {
                const response = await fetch(`api/messages.php?conversation_id=${conversationId}`);
                const data = await response.json();
                
                const messagesContainer = document.getElementById('messagesContainer');
                
                if (data.success && data.messages) {
                    messagesContainer.innerHTML = '';
                    
                    if (data.messages.length === 0) {
                        messagesContainer.innerHTML = `
                            <div class="empty-state">
                                <i class="bi bi-chat-left-dots" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 16px;"></i>
                                <h4 style="color: var(--text-secondary);">Start the conversation</h4>
                                <p style="color: var(--text-secondary);">Send the first message to begin chatting</p>
                            </div>
                        `;
                    } else {
                        data.messages.forEach(message => {
                            const messageEl = createMessageElement(message);
                            messagesContainer.appendChild(messageEl);
                        });
                        
                        // Scroll to bottom
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                } else {
                    messagesContainer.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #e53e3e;"></i>
                            <h4 style="color: #e53e3e;">Error loading messages</h4>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        // Create message element
        function createMessageElement(message) {
            const div = document.createElement('div');
            const isOwn = message.is_own;
            div.className = `message ${isOwn ? 'sent' : 'received'}`;
            
            const avatarInitial = message.sender_name.charAt(0).toUpperCase();
            const timeFormatted = formatTime(message.created_at);
            
            div.innerHTML = `
                <div class="message-avatar">${avatarInitial}</div>
                <div class="message-content">
                    <p class="message-text">${escapeHtml(message.message)}</p>
                    <div class="message-time">${timeFormatted}</div>
                </div>
            `;
            
            return div;
        }

        // Handle send message
        async function handleSendMessage(e) {
            e.preventDefault();
            
            if (!currentConversationId) return;
            
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            const sendButton = document.querySelector('.send-button');
            sendButton.disabled = true;
            sendButton.innerHTML = '<div class="loading-spinner"></div>';
            
            try {
                const response = await fetch('api/send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        conversation_id: currentConversationId,
                        message: message
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    await loadMessages(currentConversationId);
                    loadConversations(); // Refresh conversation list
                } else {
                    alert('Error sending message: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Network error. Please try again.');
            } finally {
                sendButton.disabled = false;
                sendButton.innerHTML = '<i class="bi bi-send-fill"></i>';
                messageInput.focus();
            }
        }

        // Start polling for new messages
        function startPolling() {
            // Poll conversations every 30 seconds
            conversationPollingInterval = setInterval(loadConversations, 30000);
            
            // Poll messages every 10 seconds if conversation is open
            messagePollingInterval = setInterval(() => {
                if (currentConversationId) {
                    loadMessages(currentConversationId);
                }
            }, 10000);
        }

        // Utility functions
        function formatTimeAgo(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffInHours = (now - date) / (1000 * 60 * 60);
            
            if (diffInHours < 1) return 'Just now';
            if (diffInHours < 24) return `${Math.floor(diffInHours)}h ago`;
            if (diffInHours < 48) return 'Yesterday';
            return date.toLocaleDateString();
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('api/logout.php', { method: 'POST' });
                if (response.ok) {
                    window.location.href = 'login.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (messagePollingInterval) clearInterval(messagePollingInterval);
            if (conversationPollingInterval) clearInterval(conversationPollingInterval);
        });
    </script>
</body>
</html>
