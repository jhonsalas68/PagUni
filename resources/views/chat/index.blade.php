@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/chat-navigation.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4" style="height: calc(100vh - 60px);">
    <div class="row h-100">
        <!-- Sidebar - Conversations List -->
        <div class="col-md-4 col-lg-3 border-end h-100 overflow-auto bg-white p-0">
            <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Mensajes</h5>
                <button class="btn btn-sm btn-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="list-group list-group-flush" id="conversations-list">
                @foreach($conversations as $conversation)
                    <a href="#" class="list-group-item list-group-item-action border-0 py-3 conversation-item" 
                       data-id="{{ $conversation->id }}"
                       id="conversation-{{ $conversation->id }}">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-1 me-2">
                                    @if($conversation->type == 'private')
                                        @foreach($conversation->participants as $p)
                                            @if($p->participant_id != session('user_id') || $p->participant_type != session('user_type'))
                                                {{ $p->participant->nombre ?? 'Usuario' }} {{ $p->participant->apellido ?? '' }}
                                            @endif
                                        @endforeach
                                    @else
                                        <i class="fas fa-users me-1"></i>{{ $conversation->title ?? 'Grupo' }}
                                    @endif
                                </h6>
                                @if($conversation->type == 'private')
                                    @foreach($conversation->participants as $p)
                                        @if($p->participant_id != session('user_id') || $p->participant_type != session('user_type'))
                                            <span class="badge badge-sm {{ isset($p->is_online) && $p->is_online ? 'bg-success' : 'bg-secondary' }} rounded-circle" 
                                                  style="width: 8px; height: 8px; padding: 0;" 
                                                  title="{{ isset($p->is_online) && $p->is_online ? 'En línea' : 'Desconectado' }}"></span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="badge bg-primary rounded-pill small">{{ $conversation->participants->count() }}</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $conversation->updated_at->shortAbsoluteDiffForHumans() }}</small>
                        </div>
                        <p class="mb-1 text-truncate text-muted small last-message">
                            {{ $conversation->messages->first()->content ?? 'Sin mensajes' }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-8 col-lg-9 h-100 d-flex flex-column bg-light p-0">
            <div id="chat-header" class="p-3 border-bottom bg-white d-none">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <button id="back-to-conversations" class="btn btn-outline-secondary btn-sm me-3 d-md-none">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div id="chat-title">Selecciona un chat</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button id="close-chat" class="btn btn-outline-danger btn-sm" title="Cerrar chat">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="messages-area" class="flex-grow-1 p-4 overflow-auto d-flex flex-column">
                <div class="text-center text-muted my-auto" id="empty-state">
                    <i class="far fa-comments fa-3x mb-3"></i>
                    <p>Selecciona una conversación para comenzar</p>
                </div>
                <!-- Messages will be injected here -->
            </div>

            <div id="input-area" class="p-3 bg-white border-top d-none">
                <div id="recipient-info" class="mb-2 d-none">
                    <small class="text-muted">
                        <i class="fas fa-paper-plane me-1"></i>
                        Enviando mensaje <span id="recipient-text">a...</span>
                    </small>
                </div>
                <form id="message-form" class="d-flex gap-2">
                    <input type="hidden" id="current-conversation-id">
                    <input type="text" class="form-control rounded-pill" id="message-input" placeholder="Escribe un mensaje..." autocomplete="off">
                    <button type="submit" class="btn btn-primary rounded-circle">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Nuevo Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-pills mb-3 nav-fill" id="searchTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-sm small" id="direct-tab" data-bs-toggle="pill" data-bs-target="#direct-search" type="button">búsqueda Directa</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm small" id="subject-tab" data-bs-toggle="pill" data-bs-target="#subject-search" type="button">Por Materia</button>
                    </li>
                    @if(session('user_type') === 'profesor')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm small" id="group-tab" data-bs-toggle="pill" data-bs-target="#group-create" type="button">Crear Grupo</button>
                    </li>
                    @endif
                </ul>

                <div class="tab-content" id="searchTabContent">
                    <!-- Direct Search -->
                    <div class="tab-pane fade show active" id="direct-search" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Filtrar por tipo:</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="roleFilter" id="roleAll" value="all" checked autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm" for="roleAll">Todos</label>

                                <input type="radio" class="btn-check" name="roleFilter" id="roleDocente" value="profesor" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm" for="roleDocente">Docentes</label>

                                <input type="radio" class="btn-check" name="roleFilter" id="roleAlumno" value="estudiante" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm" for="roleAlumno">Alumnos</label>
                            </div>
                        </div>
                        <div class="position-relative">
                             <input type="text" class="form-control" id="user-search-input" placeholder="Escribe un nombre...">
                             <i class="fas fa-search position-absolute text-muted" style="right: 15px; top: 12px;"></i>
                        </div>
                    </div>

                    <!-- Subject Search -->
                    <div class="tab-pane fade" id="subject-search" role="tabpanel">
                         <div class="mb-3">
                            <label class="form-label small text-muted">Materia:</label>
                            <select class="form-select" id="materia-select">
                                <option value="">Cargando materias...</option>
                            </select>
                        </div>
                         <div class="position-relative">
                             <input type="text" class="form-control" id="materia-user-search" placeholder="Buscar alumno en esta materia..." disabled>
                        </div>
                    </div>

                    @if(session('user_type') === 'profesor')
                    <!-- Group Creation -->
                    <div class="tab-pane fade" id="group-create" role="tabpanel">
                        <form id="group-create-form">
                            <div class="mb-3">
                                <label class="form-label small text-muted">Materia:</label>
                                <select class="form-select" id="group-materia-select" required>
                                    <option value="">-- Selecciona una Materia --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Nombre del Grupo:</label>
                                <input type="text" class="form-control" id="group-title" placeholder="Ej: Programación I - Grupo A" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Descripción (opcional):</label>
                                <textarea class="form-control" id="group-description" rows="2" placeholder="Descripción del grupo..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-users me-2"></i>Crear Grupo con Todos los Alumnos
                            </button>
                        </form>
                        <div id="group-preview" class="mt-3 d-none">
                            <h6 class="small text-muted">VISTA PREVIA:</h6>
                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="group-preview-text"></span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-3">
                    <h6 class="small text-muted font-weight-bold">RESULTADOS</h6>
                    <div id="search-results" class="list-group list-group-flush border rounded overflow-auto" style="max-height: 250px; min-height: 50px;">
                        <p class="text-center text-muted small my-3">Empieza a escribir para buscar...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js'])
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userId = {{ session('user_id') }};
        const messagesArea = document.getElementById('messages-area');
        const emptyState = document.getElementById('empty-state');
        const chatHeader = document.getElementById('chat-header');
        const inputArea = document.getElementById('input-area');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const currentConvInput = document.getElementById('current-conversation-id');
        const backButton = document.getElementById('back-to-conversations');
        const closeButton = document.getElementById('close-chat');
        const conversationsList = document.querySelector('.col-md-4');
        const chatArea = document.querySelector('.col-md-8');
        let activeChannel = null;

        // Load Conversation match
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                loadConversation(id);
                
                // Active state
                document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active', 'bg-light'));
                this.classList.add('active', 'bg-light');
            });
        });

        function loadConversation(id) {
            currentConvInput.value = id;
            emptyState.classList.add('d-none');
            chatHeader.classList.remove('d-none');
            inputArea.classList.remove('d-none');
            messagesArea.innerHTML = ''; // Clear

            // Manejar vista móvil
            handleMobileView();

            axios.get(`/chat/${id}`)
                .then(response => {
                    const messages = response.data.messages;
                    const conversation = response.data.conversation;
                    
                    // Update header with conversation info
                    updateChatHeader(conversation);

                    messages.forEach(msg => appendMessage(msg, response.data.current_user_id, conversation));
                    scrollToBottom();
                    
                    // Subscribe to Echo
                    if (activeChannel) {
                        Echo.leave(`chat.${activeChannel}`);
                    }
                    activeChannel = id;
                    Echo.private(`chat.${id}`)
                        .listen('MessageSent', (e) => {
                            appendMessage(e.message, userId); // userId from blade
                            scrollToBottom();
                        });
                })
                .catch(error => console.error(error));
        }

        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const content = messageInput.value.trim();
            if (!content) return;

            const convId = currentConvInput.value;
            
            // Optimistic append
            // appendMessage({ content: content, sender_id: userId, created_at: new Date().toISOString() }, userId);
            messageInput.value = '';

            axios.post('/chat', {
                conversation_id: convId,
                content: content
            }).then(res => {
                // Confirm or replace optimistic
            }).catch(err => {
                console.error(err);
                alert('Error al enviar');
            });
        });

        function appendMessage(msg, currentUserId, conversation) {
            const div = document.createElement('div');
            const isMe = msg.sender_id == currentUserId; 
            const isSystemMessage = msg.message_type === 'system';
            
            if (isSystemMessage) {
                // Mensaje del sistema (centrado)
                div.className = 'd-flex justify-content-center mb-3';
                div.innerHTML = `
                    <div class="alert alert-info small text-center border-0 bg-light" style="max-width: 80%;">
                        <i class="fas fa-info-circle me-1"></i>
                        ${msg.content}
                    </div>
                `;
            } else {
                // Mensaje normal
                div.className = `d-flex mb-3 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;
                
                const senderName = msg.sender ? `${msg.sender.nombre} ${msg.sender.apellido}` : 'Usuario';
                
                // Determinar el destinatario
                let recipientInfo = '';
                if (conversation && conversation.type === 'private') {
                    // En chat privado, mostrar "para [nombre]" solo en mensajes propios
                    if (isMe && conversation.participants) {
                        const recipient = conversation.participants.find(p => 
                            p.participant_id != currentUserId || p.participant_type != '{{ session("user_type") }}'
                        );
                        if (recipient && recipient.participant) {
                            recipientInfo = `para ${recipient.participant.nombre} ${recipient.participant.apellido}`;
                        }
                    }
                } else if (conversation && conversation.type === 'group') {
                    // En grupo, mostrar el nombre del grupo
                    recipientInfo = `en ${conversation.title}`;
                }
                
                div.innerHTML = `
                    <div class="d-flex flex-column" style="max-width: 75%;">
                        <div class="d-flex align-items-center mb-1">
                            <small class="text-muted ms-2">
                                <strong>${isMe ? 'Tú' : senderName}</strong>
                                ${recipientInfo ? ` ${recipientInfo}` : ''}
                            </small>
                        </div>
                        <div class="card ${isMe ? 'bg-primary text-white border-0' : 'bg-white border'}">
                            <div class="card-body p-2 px-3">
                                <p class="mb-0">${msg.content}</p>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="${isMe ? 'text-white-50' : 'text-muted'}" style="font-size: 0.7rem;">
                                        ${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                    </small>
                                    ${isMe ? `<i class="fas fa-check ${msg.is_read ? 'text-white-50' : 'text-white'}" title="${msg.is_read ? 'Leído' : 'Enviado'}" style="font-size: 0.7rem;"></i>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            messagesArea.appendChild(div);
        }

        function updateChatHeader(conversation) {
            const chatTitle = document.getElementById('chat-title');
            const recipientInfo = document.getElementById('recipient-info');
            const recipientText = document.getElementById('recipient-text');
            
            if (conversation.type === 'private') {
                // Chat privado - mostrar nombre del otro participante
                const otherParticipant = conversation.participants.find(p => 
                    p.participant_id != {{ session('user_id') }} || p.participant_type != '{{ session("user_type") }}'
                );
                
                if (otherParticipant && otherParticipant.participant) {
                    const name = `${otherParticipant.participant.nombre} ${otherParticipant.participant.apellido}`;
                    const isOnline = otherParticipant.is_online;
                    
                    chatTitle.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; font-weight: bold;">
                                    ${name.split(' ').map(n => n[0]).join('')}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">Chat con ${name}</h6>
                                <small class="text-muted">
                                    <span class="badge badge-sm ${isOnline ? 'bg-success' : 'bg-secondary'} rounded-circle me-1" 
                                          style="width: 8px; height: 8px; padding: 0;"></span>
                                    ${isOnline ? 'En línea' : 'Desconectado'}
                                </small>
                            </div>
                        </div>
                    `;
                    
                    // Actualizar información del destinatario
                    recipientText.textContent = `a ${name}`;
                    recipientInfo.classList.remove('d-none');
                }
            } else if (conversation.type === 'group') {
                // Chat grupal - mostrar información del grupo
                chatTitle.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">${conversation.title}</h6>
                            <small class="text-muted">
                                ${conversation.participants.length} participantes
                                ${conversation.description ? ` • ${conversation.description}` : ''}
                            </small>
                        </div>
                    </div>
                `;
                
                // Actualizar información del destinatario para grupo
                recipientText.textContent = `al grupo "${conversation.title}" (${conversation.participants.length} participantes)`;
                recipientInfo.classList.remove('d-none');
            }
        }

        function scrollToBottom() {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        // Función para cerrar el chat y volver a la lista
        function closeChat() {
            // Limpiar conversación activa
            currentConvInput.value = '';
            
            // Ocultar áreas del chat
            chatHeader.classList.add('d-none');
            inputArea.classList.add('d-none');
            
            // Mostrar estado vacío
            messagesArea.innerHTML = '';
            emptyState.classList.remove('d-none');
            
            // Limpiar selección activa
            document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active', 'bg-light'));
            
            // Desconectar del canal de Echo
            if (activeChannel) {
                Echo.leave(`chat.${activeChannel}`);
                activeChannel = null;
            }
            
            // En móviles, mostrar la lista de conversaciones
            if (window.innerWidth < 768) {
                conversationsList.classList.remove('d-none');
                chatArea.classList.add('d-none');
            }
        }

        // Event listeners para los botones
        backButton.addEventListener('click', function() {
            // En móviles, mostrar lista y ocultar chat
            conversationsList.classList.remove('d-none');
            chatArea.classList.add('d-none');
        });

        closeButton.addEventListener('click', function() {
            closeChat();
        });

        // Manejar responsive - cuando se selecciona una conversación en móvil
        function handleMobileView() {
            if (window.innerWidth < 768) {
                conversationsList.classList.add('d-none');
                chatArea.classList.remove('d-none');
            }
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        // ... (existing code variables)
        
        // --- Search Logic ---
        const userSearchInput = document.getElementById('user-search-input');
        const materiaSearchInput = document.getElementById('materia-user-search');
        const searchResults = document.getElementById('search-results');
        const materiaSelect = document.getElementById('materia-select');
        let searchTimeout = null;

        // Load options on modal open
        const newChatModal = document.getElementById('newChatModal');
        newChatModal.addEventListener('shown.bs.modal', function () {
            userSearchInput.focus();
            loadMaterias();
            // Trigger initial search
            performSearch('', 'all', null);
        });

        function loadMaterias() {
            if(materiaSelect.options.length > 1) return; // Already loaded

            fetch('/chat/users/options')
                .then(res => res.json())
                .then(data => {
                    materiaSelect.innerHTML = '<option value="">-- Selecciona una Materia --</option>';
                    data.materias.forEach(m => {
                        materiaSelect.innerHTML += `<option value="${m.id}">${m.nombre} (${m.codigo})</option>`;
                    });
                });
        }

        // Direct Search Events
        userSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = this.value;
                const role = document.querySelector('input[name="roleFilter"]:checked').value;
                performSearch(query, role, null);
            }, 300);
        });

        document.querySelectorAll('input[name="roleFilter"]').forEach(el => {
            el.addEventListener('change', function() {
                 performSearch(userSearchInput.value, this.value, null);
            });
        });

        // Subject Search Events
        materiaSelect.addEventListener('change', function() {
            const materiaId = this.value;
            materiaSearchInput.disabled = !materiaId;
            if(materiaId) {
                performSearch('', 'estudiante', materiaId); // Fetch all students in course initially
            } else {
                searchResults.innerHTML = '';
            }
        });

        materiaSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = this.value;
                const materiaId = materiaSelect.value;
                if(materiaId) performSearch(query, 'estudiante', materiaId);
            }, 300);
        });

        function performSearch(query, role, materiaId) {
            // Allow empty query if we are just listing (e.g. initial load or filter change)
            // But if subject tab active, maybe require subject? 
            // Current logic: if materiaId is provided, use it. If not, use query/role.
            
            if (materiaId) {
                // Good to go
            } else {
                 // Direct search mode. Allow empty query to show recommendations/all users.
            }

            searchResults.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';

            let url = `/chat/users/search?role=${role}`;
            if(query) url += `&query=${encodeURIComponent(query)}`;
            if(materiaId) url += `&materia=${materiaId}`;

            fetch(url)
                .then(res => res.json())
                .then(users => {
                    if(users.length === 0) {
                        searchResults.innerHTML = '<p class="text-center text-muted small my-3">No se encontraron usuarios.</p>';
                        return;
                    }

                    let html = '';
                    users.forEach(user => {
                        html += `
                            <button class="list-group-item list-group-item-action d-flex align-items-center mb-1 border-0 user-select-item" 
                                data-id="${user.id}" data-type="${user.type}">
                                <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px; font-weight: bold;">
                                    ${user.initials}
                                </div>
                                <div class="flex-grow-1 text-start">
                                    <h6 class="mb-0 text-truncate text-dark" style="max-width: 250px;">${user.name}</h6>
                                    <small class="text-muted">${user.role_label}</small>
                                </div>
                                <i class="fas fa-comment text-primary opacity-0 icon-hover"></i>
                            </button>
                        `;
                    });
                    searchResults.innerHTML = html;

                    // Add click listeners to new items
                    document.querySelectorAll('.user-select-item').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const recipientId = this.dataset.id;
                            const recipientType = this.dataset.type;
                            startConversation(recipientId, recipientType);
                        });
                    });
                });
        }

        function startConversation(recipientId, recipientType) {
            axios.post('/chat/create', {
                recipient_id: recipientId,
                recipient_type: recipientType
            }).then(response => {
                const conv = response.data;
                // Close modal
                const modal = bootstrap.Modal.getInstance(newChatModal);
                modal.hide();
                // Reload list or select conversation
                 window.location.reload(); // Simplest way to refresh sidebar for now
            }).catch(err => {
                console.error(err);
                alert('Error al iniciar conversación');
            });
        }

        // Funcionalidad para crear grupos (solo profesores)
        @if(session('user_type') === 'profesor')
        const groupForm = document.getElementById('group-create-form');
        const groupMateriaSelect = document.getElementById('group-materia-select');
        const groupPreview = document.getElementById('group-preview');
        const groupPreviewText = document.getElementById('group-preview-text');

        // Cargar materias del profesor en el selector de grupos
        function loadProfessorMaterias() {
            fetch('/chat/users/options')
                .then(res => res.json())
                .then(data => {
                    groupMateriaSelect.innerHTML = '<option value="">-- Selecciona una Materia --</option>';
                    data.materias.forEach(m => {
                        groupMateriaSelect.innerHTML += `<option value="${m.id}">${m.nombre} (${m.codigo})</option>`;
                    });
                });
        }

        // Mostrar vista previa del grupo
        groupMateriaSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const materiaName = selectedOption.text;
                
                // Obtener número de estudiantes en la materia
                fetch(`/chat/users/search?materia=${this.value}&role=estudiante`)
                    .then(res => res.json())
                    .then(students => {
                        groupPreviewText.textContent = `Se creará un grupo para "${materiaName}" con ${students.length} estudiantes inscritos.`;
                        groupPreview.classList.remove('d-none');
                    });
            } else {
                groupPreview.classList.add('d-none');
            }
        });

        // Manejar creación de grupo
        groupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const materiaId = groupMateriaSelect.value;
            const title = document.getElementById('group-title').value;
            const description = document.getElementById('group-description').value;
            
            if (!materiaId || !title) {
                alert('Por favor completa los campos requeridos');
                return;
            }
            
            // Mostrar loading
            const submitBtn = groupForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando Grupo...';
            submitBtn.disabled = true;
            
            axios.post('/chat/create-group', {
                materia_id: materiaId,
                title: title,
                description: description
            }).then(response => {
                const result = response.data;
                alert(`¡Grupo creado exitosamente! Se agregaron ${result.students_added} estudiantes.`);
                
                // Cerrar modal y recargar
                const modal = bootstrap.Modal.getInstance(newChatModal);
                modal.hide();
                window.location.reload();
                
            }).catch(err => {
                console.error(err);
                alert('Error al crear el grupo: ' + (err.response?.data?.error || 'Error desconocido'));
            }).finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Cargar materias del profesor cuando se abre el modal
        newChatModal.addEventListener('shown.bs.modal', function () {
            loadProfessorMaterias();
        });
        @endif

        // Sistema de estado en línea
        function updateOnlineStatus(status = 'online') {
            axios.post('/chat/status', { status: status })
                .catch(err => console.error('Error updating status:', err));
        }

        // Actualizar estado cada 30 segundos
        setInterval(() => {
            updateOnlineStatus();
        }, 30000);

        // Actualizar estado al cargar la página
        updateOnlineStatus();

        // Actualizar estado cuando la ventana pierde/gana foco
        window.addEventListener('focus', () => updateOnlineStatus('online'));
        window.addEventListener('blur', () => updateOnlineStatus('away'));

        // Actualizar estado antes de cerrar la página
        window.addEventListener('beforeunload', () => {
            navigator.sendBeacon('/chat/status', JSON.stringify({ status: 'offline' }));
        });

        // Soporte para tecla Escape para cerrar chat
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !chatHeader.classList.contains('d-none')) {
                closeChat();
            }
        });

        // Manejar cambios de tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                // En desktop, mostrar ambas columnas
                conversationsList.classList.remove('d-none');
                chatArea.classList.remove('d-none');
            }
        });
    });
</script>
@endsection
