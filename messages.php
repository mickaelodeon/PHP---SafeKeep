<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - SafeKeep</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browse.php">Browse Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="messages.php">Messages</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <span id="username">User</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <li id="adminMenuItem" style="display: none;"><a class="dropdown-item" href="admin.php"><i class="bi bi-shield-check"></i> Admin Panel</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-primary text-white p-4 rounded">
                    <h2><i class="bi bi-chat-dots"></i> Messages</h2>
                    <p class="mb-0">Communicate securely with other users about lost and found items</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Conversations List -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Conversations</h5>
                        <span class="badge bg-primary" id="unreadCount">0</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="conversationsList" class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading conversations...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white" id="chatHeader" style="display: none;">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img src="assets/user-icon.png" alt="User" class="rounded-circle" width="40" height="40">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0" id="chatUsername">Select a conversation</h6>
                                <small class="text-muted" id="chatItemTitle">No conversation selected</small>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary btn-sm" onclick="viewItemDetails()" id="viewItemBtn" style="display: none;">
                                    <i class="bi bi-eye"></i> View Item
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column" style="height: 500px;">
                        <!-- Welcome Message -->
                        <div id="welcomeMessage" class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="bi bi-chat-square-dots text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">Welcome to SafeKeep Messaging</h5>
                                <p class="text-muted">Select a conversation to start chatting, or browse items to contact their owners.</p>
                                <a href="browse.php" class="btn btn-primary">Browse Items</a>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div id="messagesArea" class="flex-grow-1 overflow-auto p-3" style="display: none; background-color: #f8f9fa; border-radius: 0.5rem;">
                            <div id="messagesList">
                                <!-- Messages will be loaded here -->
                            </div>
                        </div>
                        
                        <!-- Message Input -->
                        <div id="messageInput" class="mt-3" style="display: none;">
                            <form id="sendMessageForm" class="d-flex">
                                <input type="text" class="form-control me-2" id="messageText" placeholder="Type your message..." autocomplete="off">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal (for starting new conversations from browse page) -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="contactForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Item:</label>
                            <div id="contactItemInfo" class="p-2 bg-light rounded">
                                <!-- Item info will be loaded here -->
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contactSubject" class="form-label">Subject</label>
                            <select class="form-select" id="contactSubject" required>
                                <option value="">Select a reason for contact...</option>
                                <option value="claim">I think this is my lost item</option>
                                <option value="found_similar">I found something similar</option>
                                <option value="question">I have a question about this item</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contactMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="contactMessage" rows="4" placeholder="Please provide details..." required></textarea>
                        </div>
                        <input type="hidden" id="contactItemId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
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
            
            // Handle send message form
            document.getElementById('sendMessageForm').addEventListener('submit', sendMessage);
            
            // Handle contact form
            document.getElementById('contactForm').addEventListener('submit', sendContactMessage);
        });

        // Check if contact parameter is in URL
        function checkContactParameter() {
            const urlParams = new URLSearchParams(window.location.search);
            const contactItemId = urlParams.get('contact');
            
            if (contactItemId) {
                // Load item details and show contact modal
                loadItemForContact(contactItemId);
            }
        }

        // Load item details for contact modal
        async function loadItemForContact(itemId) {
            try {
                const response = await fetch(`api/get_item_details.php?id=${itemId}`);
                const data = await response.json();
                
                if (data.success) {
                    const item = data.item;
                    
                    // Populate contact modal
                    document.getElementById('contactItemInfo').innerHTML = `
                        <div class="d-flex align-items-center">
                            <img src="${item.image_url || 'assets/safekeeplogo.png'}" 
                                 class="me-3 rounded" width="60" height="60" alt="${item.title}">
                            <div>
                                <h6 class="mb-1">${item.title}</h6>
                                <small class="text-muted">${item.status.toUpperCase()} - ${item.category}</small>
                                <br><small class="text-muted">Posted by: ${item.posted_by}</small>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('contactItemId').value = itemId;
                    
                    // Show contact modal
                    const modal = new bootstrap.Modal(document.getElementById('contactModal'));
                    modal.show();
                } else {
                    showAlert('danger', 'Item not found or not available for contact');
                }
            } catch (error) {
                console.error('Error loading item for contact:', error);
                showAlert('danger', 'Failed to load item details');
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
                        document.getElementById('adminMenuItem').style.display = 'block';
                    }
                } else {
                    window.location.href = 'login.html';
                }
            } catch (error) {
                console.error('Auth check error:', error);
                window.location.href = 'login.html';
            }
        }

        // Load conversations
        async function loadConversations() {
            try {
                const response = await fetch('api/conversations.php');
                const data = await response.json();
                
                const container = document.getElementById('conversationsList');
                
                if (data.success && data.conversations.length > 0) {
                    container.innerHTML = data.conversations.map(conv => createConversationItem(conv)).join('');
                    
                    // Update unread count
                    const unreadCount = data.conversations.filter(c => c.unread_count > 0).length;
                    document.getElementById('unreadCount').textContent = unreadCount;
                } else {
                    container.innerHTML = `
                        <div class="text-center p-4">
                            <i class="bi bi-chat-square text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">No conversations yet</p>
                            <a href="browse.php" class="btn btn-outline-primary btn-sm">Browse Items to Start Chatting</a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
                showAlert('danger', 'Failed to load conversations');
            }
        }

        // Create conversation item HTML
        function createConversationItem(conv) {
            const isUnread = conv.unread_count > 0;
            const timeAgo = formatTimeAgo(conv.last_message_time);
            
            return `
                <div class="list-group-item list-group-item-action conversation-item ${isUnread ? 'bg-light border-primary' : ''}" 
                     onclick="selectConversation(${conv.id}, '${conv.other_user}', '${conv.item_title}', ${conv.item_id})"
                     data-conversation-id="${conv.id}">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 ${isUnread ? 'fw-bold' : ''}">
                                ${conv.other_user}
                                ${isUnread ? `<span class="badge bg-primary ms-2">${conv.unread_count}</span>` : ''}
                            </h6>
                            <p class="mb-1 text-muted small">About: ${conv.item_title}</p>
                            <small class="text-muted">${conv.last_message || 'No messages yet'}</small>
                        </div>
                        <small class="text-muted">${timeAgo}</small>
                    </div>
                </div>
            `;
        }

        // Select conversation
        async function selectConversation(conversationId, username, itemTitle, itemId) {
            currentConversationId = conversationId;
            
            // Update UI
            document.getElementById('welcomeMessage').style.display = 'none';
            document.getElementById('chatHeader').style.display = 'block';
            document.getElementById('messagesArea').style.display = 'block';
            document.getElementById('messageInput').style.display = 'block';
            
            // Update header
            document.getElementById('chatUsername').textContent = username;
            document.getElementById('chatItemTitle').textContent = `About: ${itemTitle}`;
            document.getElementById('viewItemBtn').style.display = 'block';
            document.getElementById('viewItemBtn').onclick = () => viewItemDetails(itemId);
            
            // Mark conversation as active
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('active');
            
            // Load messages
            loadMessages();
            
            // Mark as read
            markConversationAsRead(conversationId);
        }

        // Load messages for current conversation
        async function loadMessages() {
            if (!currentConversationId) return;
            
            try {
                const response = await fetch(`api/messages.php?conversation_id=${currentConversationId}`);
                const data = await response.json();
                
                const container = document.getElementById('messagesList');
                
                if (data.success && data.messages.length > 0) {
                    container.innerHTML = data.messages.map(msg => createMessageBubble(msg)).join('');
                    
                    // Scroll to bottom
                    const messagesArea = document.getElementById('messagesArea');
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                } else {
                    container.innerHTML = `
                        <div class="text-center text-muted">
                            <p>Start the conversation by sending a message!</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
                showAlert('danger', 'Failed to load messages');
            }
        }

        // Create message bubble
        function createMessageBubble(msg) {
            const isOwn = msg.is_own;
            const timeFormatted = new Date(msg.created_at).toLocaleString();
            
            return `
                <div class="d-flex ${isOwn ? 'justify-content-end' : 'justify-content-start'} mb-3">
                    <div class="message-bubble ${isOwn ? 'bg-primary text-white' : 'bg-white border'}" 
                         style="max-width: 70%; padding: 10px 15px; border-radius: ${isOwn ? '20px 20px 5px 20px' : '20px 20px 20px 5px'};">
                        <p class="mb-1">${msg.message}</p>
                        <small class="${isOwn ? 'text-white-50' : 'text-muted'}" style="font-size: 0.75rem;">${timeFormatted}</small>
                    </div>
                </div>
            `;
        }

        // Send message
        async function sendMessage(e) {
            e.preventDefault();
            
            if (!currentConversationId) return;
            
            const messageText = document.getElementById('messageText').value.trim();
            if (!messageText) return;
            
            try {
                const response = await fetch('api/send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        conversation_id: currentConversationId,
                        message: messageText
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('messageText').value = '';
                    loadMessages(); // Reload messages
                    loadConversations(); // Update conversation list
                } else {
                    showAlert('danger', data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                showAlert('danger', 'Failed to send message');
            }
        }

        // Send contact message (new conversation)
        async function sendContactMessage(e) {
            e.preventDefault();
            
            const itemId = document.getElementById('contactItemId').value;
            const subject = document.getElementById('contactSubject').value;
            const message = document.getElementById('contactMessage').value.trim();
            
            if (!itemId || !subject || !message) {
                showAlert('warning', 'Please fill all fields');
                return;
            }
            
            try {
                const response = await fetch('api/start_conversation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        item_id: itemId,
                        subject: subject,
                        message: message
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
                    modal.hide();
                    
                    // Reset form
                    document.getElementById('contactForm').reset();
                    
                    showAlert('success', 'Message sent successfully!');
                    
                    // Reload conversations and select the new one
                    loadConversations();
                    
                    setTimeout(() => {
                        selectConversation(data.conversation_id, data.other_user, data.item_title, itemId);
                    }, 1000);
                } else {
                    showAlert('danger', data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('Error sending contact message:', error);
                showAlert('danger', 'Failed to send message');
            }
        }

        // Mark conversation as read
        async function markConversationAsRead(conversationId) {
            try {
                await fetch('api/mark_read.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ conversation_id: conversationId })
                });
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        }

        // Start polling for new messages
        function startPolling() {
            // Poll for new messages every 3 seconds if a conversation is selected
            messagePollingInterval = setInterval(() => {
                if (currentConversationId) {
                    loadMessages();
                }
            }, 3000);
            
            // Poll for conversation updates every 10 seconds
            conversationPollingInterval = setInterval(() => {
                loadConversations();
            }, 10000);
        }

        // View item details
        function viewItemDetails(itemId) {
            if (itemId) {
                window.open(`browse.php?item=${itemId}`, '_blank');
            }
        }

        // Format time ago
        function formatTimeAgo(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = now - time;
            
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);
            
            if (minutes < 1) return 'Just now';
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            if (days < 7) return `${days}d ago`;
            return time.toLocaleDateString();
        }

        // Show alert
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('api/logout.php', { method: 'POST' });
                const data = await response.json();
                if (data.success) {
                    window.location.href = 'login.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        });

        // Cleanup intervals when page unloads
        window.addEventListener('beforeunload', function() {
            if (messagePollingInterval) clearInterval(messagePollingInterval);
            if (conversationPollingInterval) clearInterval(conversationPollingInterval);
        });
    </script>
</body>
</html>
