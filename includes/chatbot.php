<?php
// Ensure session variables are accessible for the greeting
$user_role = $_SESSION['role'] ?? 'Guest';
$first_name = $_SESSION['first_name'] ?? 'Guest';

// ✨ DYNAMIC ROUTING: Auto-detects folder depth so the API path always works!
$base_path = file_exists('actions/process_chat.php') ? '' : '../';
?>

<style>
    /* Custom Scrollbar for a cleaner look */
    .custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
    
    /* Smooth Message Entry Animation */
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-msg { animation: slideUpFade 0.3s ease-out forwards; }
</style>

<button id="chatbotToggle" onclick="toggleChatbot()" class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform z-50">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
</button>

<div id="chatWindow" class="fixed bottom-24 right-6 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col z-50 transform scale-0 opacity-0 pointer-events-none transition-all duration-300 origin-bottom-right" style="height: 580px; max-height: 85vh;">
    
    <div class="bg-gradient-to-r from-blue-700 to-blue-900 p-4 rounded-t-2xl flex justify-between items-center text-white shadow-sm shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-sm tracking-wide">ScholarLink AI</h3>
                <p class="text-[10px] text-blue-200 font-medium">TAU Virtual Assistant</p>
            </div>
        </div>
        <div class="flex gap-2">
            <!-- ✨ NEW: CLEAR CHAT BUTTON -->
            <button onclick="clearChatHistory()" class="text-white hover:text-red-300 transition-colors" title="Clear Conversation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
            <button onclick="downloadTranscript()" class="text-white hover:text-blue-200 transition-colors" title="Download Chat Transcript">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </button>
            <button onclick="toggleChatbot()" class="text-white hover:text-blue-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <div id="chatBox" class="flex-1 p-4 overflow-y-auto custom-scrollbar flex flex-col gap-4 bg-slate-50">
        <!-- Default Greeting -->
        <div class="flex gap-3 animate-msg w-full justify-start">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div class="bg-white border border-slate-100 p-3 rounded-2xl rounded-tl-sm text-sm text-slate-700 shadow-sm shadow-slate-200/50 flex flex-col gap-1 items-start w-full max-w-[85%]">
                <div class="font-black text-[10px] uppercase tracking-widest text-blue-500 mb-1">ScholarLink AI</div>
                <div class="leading-relaxed">Hello, <?= htmlspecialchars($first_name) ?>! I can help you find scholarships, check requirements, or guide you through the application steps. What do you need help with?</div>
            </div>
        </div>

        <!-- ✨ MEMORY HYDRATION LOOP -->
        <?php
        if (isset($_SESSION['chat_history']) && is_array($_SESSION['chat_history'])) {
            foreach ($_SESSION['chat_history'] as $msg) {
                $role = $msg['role'];
                $text = htmlspecialchars($msg['parts'][0]['text'] ?? '');

                if ($role === 'user') {
                    $clean_text = str_replace("[User Attached an Image/Document] ", "", $text);
                    echo '
                    <div class="flex gap-3 animate-msg w-full justify-end">
                        <div class="bg-slate-900 text-white p-3 rounded-2xl rounded-tr-sm text-sm shadow-sm flex flex-col gap-1 items-end max-w-[85%]">
                            <div class="leading-relaxed">' . nl2br($clean_text) . '</div>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-slate-200 flex items-center justify-center text-slate-500 shrink-0 font-bold text-xs uppercase shadow-sm">
                            ' . substr($first_name, 0, 1) . '
                        </div>
                    </div>';
                } else if ($role === 'model') {
                    $formattedText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
                    $formattedText = preg_replace('/\[GOTO:(.*?)\]/', '<button onclick="window.location.href=\'$1\'" class="mt-2 text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-600 hover:text-white px-3 py-2 rounded-xl transition-all shadow-sm active:scale-95 flex items-center justify-center gap-1 w-full">Proceed to Page <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></button>', $formattedText);

                    echo '
                    <div class="flex gap-3 animate-msg w-full justify-start">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div class="bg-white border border-slate-100 p-4 rounded-2xl rounded-tl-sm text-sm text-slate-700 shadow-sm shadow-slate-200/50 flex flex-col gap-1 items-start w-full max-w-[85%]">
                            <div class="font-black text-[10px] uppercase tracking-widest text-blue-500 mb-1">ScholarLink AI</div>
                            <div class="leading-relaxed whitespace-pre-line">' . $formattedText . '</div>
                        </div>
                    </div>';
                }
            }
        }
        ?>
    </div>

    <!-- ✨ AI SUGGESTION CHIPS -->
    <div class="px-3 pt-2 bg-white flex gap-2 overflow-x-auto custom-scrollbar shrink-0 border-t border-slate-100">
        <button onclick="sendQuickReply('How do I apply for a scholarship? Please give me the steps.')" class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all shadow-sm active:scale-95">🚀 Guide Me to Apply</button>
        <button onclick="sendQuickReply('What are my missing documents?')" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all shadow-sm active:scale-95">📋 Missing Docs</button>
        <button onclick="sendQuickReply('I want to talk to a human admin please.')" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all shadow-sm active:scale-95">👨‍💼 Talk to Admin</button>
    </div>

    <div class="p-3 bg-white rounded-b-2xl shrink-0">
        <div class="flex gap-2 items-end bg-slate-50 border border-slate-200 p-1 rounded-xl focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
            <textarea id="chatInput" rows="1" class="flex-1 bg-transparent border-none text-sm p-2 focus:ring-0 resize-none custom-scrollbar" placeholder="Ask a question..."></textarea>
            <button onclick="sendMessage()" class="bg-blue-600 hover:bg-blue-700 text-white w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors mb-0.5 mr-0.5 shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
    </div>
</div>

<script>
    const chatWindow = document.getElementById('chatWindow');
    const chatInput = document.getElementById('chatInput');
    const chatBox = document.getElementById('chatBox');
    const basePath = '<?= $base_path ?>';

    function toggleChatbot() {
        if (chatWindow.classList.contains('scale-0')) {
            chatWindow.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
            chatWindow.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            setTimeout(() => {
                chatInput.focus();
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 300);
        } else {
            chatWindow.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
            chatWindow.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
        }
    }

    // ✨ QUICK REPLY TRIGGER
    window.sendQuickReply = function(text) {
        chatInput.value = text;
        sendMessage();
    };

    chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // 1. Render User Message
        const userDiv = document.createElement('div');
        userDiv.className = "flex gap-3 animate-msg w-full justify-end";
        userDiv.innerHTML = `
            <div class="bg-slate-900 text-white p-3 rounded-2xl rounded-tr-sm text-sm shadow-sm flex flex-col gap-1 items-end max-w-[85%]">
                <div class="leading-relaxed">${message.replace(/\n/g, '<br>')}</div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-slate-200 flex items-center justify-center text-slate-500 shrink-0 font-bold text-xs uppercase shadow-sm">
                <?= substr($first_name, 0, 1) ?>
            </div>
        `;
        chatBox.appendChild(userDiv);
        chatInput.value = '';
        chatInput.style.height = 'auto';
        chatBox.scrollTop = chatBox.scrollHeight;

        // 2. Render Loading Animation
        const typingIndicator = document.createElement('div');
        typingIndicator.className = "flex gap-3 animate-msg w-full justify-start";
        typingIndicator.innerHTML = `
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shrink-0">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <div class="bg-white border border-slate-100 p-3 rounded-2xl rounded-tl-sm text-sm text-slate-500 shadow-sm flex items-center gap-2 max-w-[85%]">
                <span class="flex gap-1">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0s"></span>
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                </span>
            </div>
        `;
        chatBox.appendChild(typingIndicator);
        chatBox.scrollTop = chatBox.scrollHeight;

        // 3. Send to Server via Fetch
        const formData = new FormData();
        formData.append('message', message);
        formData.append('current_page', window.location.pathname.split('/').pop());

        fetch(basePath + 'actions/process_chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            typingIndicator.remove();
            
            let botReplyText = data.reply;
            let needsHandoff = false;

            if (botReplyText.includes('[ACTION:TRANSFER_TO_HUMAN]')) {
                needsHandoff = true;
                botReplyText = botReplyText.replace('[ACTION:TRANSFER_TO_HUMAN]', '').trim();
            }

            const msgDiv = document.createElement('div');
            msgDiv.className = "flex gap-3 animate-msg w-full justify-start";
            
            let formattedBotText = botReplyText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            // ✨ BEAUTIFIED LINK BUTTONS FOR NAVIGATION
            formattedBotText = formattedBotText.replace(/\[GOTO:(.*?)\]/g, '<button onclick="window.location.href=\'$1\'" class="mt-2 text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-600 hover:text-white px-3 py-2 rounded-xl transition-all shadow-sm active:scale-95 flex items-center justify-center gap-1 w-full">Proceed to Page <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></button>');
            
            msgDiv.innerHTML = `
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div class="bg-white border border-slate-100 p-4 rounded-2xl rounded-tl-sm text-sm text-slate-700 shadow-sm shadow-slate-200/50 flex flex-col gap-1 items-start w-full max-w-[85%]">
                    <div class="font-black text-[10px] uppercase tracking-widest text-blue-500 mb-1">ScholarLink AI</div>
                    <div class="leading-relaxed whitespace-pre-line"></div>
                </div>
            `;
            chatBox.appendChild(msgDiv);
            chatBox.scrollTop = chatBox.scrollHeight;

            // Typewriter Animation Engine
            const contentDiv = msgDiv.querySelector('.leading-relaxed');
            let index = 0;
            let currentHTML = ""; // ✨ FIX: Gumawa tayo ng text buffer
            
            function typeText() {
                if (index < formattedBotText.length) {
                    if (formattedBotText.charAt(index) === '<') {
                        let tag = "";
                        while (formattedBotText.charAt(index) !== '>' && index < formattedBotText.length) {
                            tag += formattedBotText.charAt(index);
                            index++;
                        }
                        tag += '>';
                        currentHTML += tag; // ✨ Idagdag sa buffer, hindi sa DOM
                        index++;
                    } else {
                        currentHTML += formattedBotText.charAt(index);
                        index++;
                    }
                    
                    // ✨ I-render ang buong buffer nang isahan para hindi mag-auto close ang browser
                    contentDiv.innerHTML = currentHTML; 
                    chatBox.scrollTop = chatBox.scrollHeight;
                    setTimeout(typeText, 5); 
                } else {
                    if (needsHandoff) {
                        setTimeout(() => {
                            executeHumanHandoff(message); 
                        }, 1200); 
                    }
                }
            }
            typeText();
        })
        .catch(err => {
            typingIndicator.remove();
            console.error("Chatbot communication error: ", err);
        });
    }

    function executeHumanHandoff(userQuery) {
        chatWindow.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
        chatWindow.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');

        if (typeof window.unhideLiveChatProtocol === 'function') {
            window.unhideLiveChatProtocol();
        }

        const openHumanChatFn = window.toggleHumanChat || window.toggleChatHead || window.toggleChat;
        const checkHumanState = window.isHumanChatOpen || false;

        if (typeof openHumanChatFn === 'function') {
            if (!checkHumanState) {
                setTimeout(() => openHumanChatFn(), 300);
            }

            setTimeout(() => {
                const humanInput = document.getElementById('humanChatInput') || 
                                   document.getElementById('studentHeadInput') || 
                                   document.getElementById('adminHeadInput'); 
                                   
                if (humanInput) {
                    humanInput.value = "⚠️ AI Escalate: I couldn't get an answer for: \"" + userQuery + "\"";
                    
                    if (typeof window.sendStudentHeadMessage === 'function') {
                        window.sendStudentHeadMessage({ preventDefault: () => {} });
                    } else if (typeof window.sendAdminHeadMessage === 'function') {
                        window.sendAdminHeadMessage({ preventDefault: () => {} });
                    }
                }
            }, 800); 
        } else {
            console.warn("Live Chat Human Module is currently unavailable on this view state page.");
        }
    }

    function downloadTranscript() {
        let transcript = "SCHOLARLINK AI - CHAT TRANSCRIPT\n";
        transcript += "Date: " + new Date().toLocaleString() + "\n\n";
        transcript += "=========================================\n\n";

        const messages = chatBox.querySelectorAll('.flex.gap-3');
        
        if (messages.length === 0) {
            alert("Your chat is empty! Talk to the AI first before downloading.");
            return;
        }

        messages.forEach(msg => {
            const isUser = msg.querySelector('.bg-slate-900') !== null;
            const sender = isUser ? "YOU" : "SCHOLARLINK AI";
            if (msg.querySelector('.animate-bounce')) return;

            let textElement = msg.querySelector('.leading-relaxed, .text-sm');
            if(textElement) {
                let text = textElement.innerText.trim();
                transcript += `[${sender}]:\n${text}\n\n`;
                transcript += "-----------------------------------------\n\n";
            }
        });

        const blob = new Blob([transcript], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'ScholarLink_AI_Transcript.txt');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    // ✨ NEW: CLEAR CHAT FUNCTION
    function clearChatHistory() {
        if (!confirm("Are you sure you want to delete this conversation? The AI will forget your previous context.")) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'clear_chat');

        fetch(basePath + 'actions/process_chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset the UI back to the default greeting only
                chatBox.innerHTML = `
                    <div class="flex gap-3 animate-msg w-full justify-start">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div class="bg-white border border-slate-100 p-3 rounded-2xl rounded-tl-sm text-sm text-slate-700 shadow-sm shadow-slate-200/50 flex flex-col gap-1 items-start w-full max-w-[85%]">
                            <div class="font-black text-[10px] uppercase tracking-widest text-blue-500 mb-1">ScholarLink AI</div>
                            <div class="leading-relaxed">Hello, <?= htmlspecialchars($first_name) ?>! I can help you find scholarships, check requirements, or guide you through the application steps. What do you need help with?</div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(err => console.error("Error clearing chat:", err));
    }
</script>