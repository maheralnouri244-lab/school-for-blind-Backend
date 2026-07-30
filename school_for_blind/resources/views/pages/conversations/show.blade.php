@extends('layouts.app')

@section('content')
  <div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1" style="color: var(--text-main);">{{ $conversation->name ?? 'محادثة' }}</h4>
        <small class="text-muted">
          أستاذ المادة: {{ $conversation->teacher->full_name ?? 'غير محدد' }}
          @if($conversation->type === 'channel')
            <span class="badge bg-soft-info text-info ms-2">قناة</span>
          @elseif($conversation->type === 'discussion')
            <span class="badge bg-soft-warning text-warning ms-2">مجموعة نقاش</span>
          @else
            <span class="badge bg-soft-success text-success ms-2">محادثة إدارية</span>
          @endif
        </small>
      </div>
      <a href="{{ route('dashboard.conversations.index') }}" class="btn btn-sm"
        style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">
        <i class="fa-solid fa-arrow-right me-1"></i> العودة للمحادثات
      </a>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="custom-card d-flex flex-column" style="height: 650px; padding: 20px;">

          <div id="chat-messages" class="flex-grow-1 overflow-y-auto mb-3 p-3 position-relative"
            style="background-color: var(--bg-main); border-radius: 12px; border: 1px solid var(--border-color);">

            <div id="top-loading-spinner" class="text-center py-2 d-none">
              <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
              <small class="text-muted ms-2">جاري تحميل الرسائل القديمة...</small>
            </div>

            <div id="messages-wrapper"></div>
          </div>

          {{--
          HEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEee
          RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
          EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEe
          HEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
          RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
          EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEHEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
          RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
          EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEHEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
          RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
          EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEHEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
          RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
          EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEHEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
          RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
          EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
          --}}
          @if((isset($canReply) && $canReply) || 1)
            <form id="send-message-form" class="mt-auto" enctype="multipart/form-data">
              @csrf
              <div class="input-group p-1 rounded-3"
                style="background-color: var(--bg-card); border: 1px solid var(--border-color);">

                <button type="button" id="btn-record-voice" class="btn btn-link text-muted m-0 d-flex align-items-center"
                  title="تسجيل صوتي">
                  <i class="fa-solid fa-microphone fs-5 transition-transform"></i>
                </button>

                <input type="text" id="message-body-input" name="body"
                  class="form-control border-0 shadow-none bg-transparent" placeholder="اكتب رسالتك هنا..."
                  style="color: var(--text-main);">

                <button type="submit" class="btn btn-primary px-4 fw-bold rounded-2 d-flex align-items-center gap-2"
                  id="btn-send-msg">
                  <span>إرسال</span>
                  <i class="fa-solid fa-paper-plane"></i>
                </button>
              </div>

              <div id="attachment-preview" class="mt-2 d-none align-items-center gap-2 p-2 rounded"
                style="background-color: var(--hover-bg);">
                <i class="fa-solid fa-microphone-lines text-info fa-beat"></i>
                <small id="attachment-file-name" class="text-muted flex-grow-1">مقطع صوتي جاهز للإرسال</small>
                <button type="button" class="btn-close btn-sm" id="btn-remove-attachment"></button>
              </div>
            </form>
          @else
            <div class="p-2 rounded bg-soft-warning text-warning text-center" style="font-size: 0.9rem;">
              <i class="fa-solid fa-circle-info me-1"></i> هذه الواجهة مخصصة لمراقبة وضبط المحتوى وحذف المخالفات.
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold text-danger">تأكيد حذف الرسالة</h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-end">
          <p style="color: var(--text-main);">هل أنت متأكد من رغبتك في حذف هذه الرسالة نهائياً؟ لا يمكن التراجع عن هذا
            الإجراء.</p>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-sm" data-bs-dismiss="modal"
            style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">إلغاء</button>
          <button type="button" class="btn btn-sm btn-reject px-3" id="btn-confirm-delete">حذف الآن</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="userProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content glass-modal text-end" id="user-profile-modal-content">
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">جاري تحميل بيانات الملف الشخصي...</p>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script type="module">
    const conversationId = {{ $conversation->id }};
    const chatContainer = document.getElementById('chat-messages');
    const messagesWrapper = document.getElementById('messages-wrapper');
    const topSpinner = document.getElementById('top-loading-spinner');

    let nextCursorUrl = `/content-monitor/conversations/${conversationId}/fetch-messages`;
    let isLoading = false;
    let isFirstLoad = true;
    let messageIdToDelete = null;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const profileModal = new bootstrap.Modal(document.getElementById('userProfileModal'));

    function fetchMessages(url, isPrepend = false) {
      if (!url || isLoading) return;
      isLoading = true;
      if (isPrepend) topSpinner.classList.remove('d-none');

      fetch(url, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
        .then(res => res.json())
        .then(data => {
          nextCursorUrl = data.next_page_url;
          const messages = data.data.reverse();
          const oldScrollHeight = chatContainer.scrollHeight;

          if (isPrepend) {
            messages.forEach(msg => {
              messagesWrapper.insertAdjacentHTML('afterbegin', renderMessageHTML(msg));
            });
            chatContainer.scrollTop = chatContainer.scrollHeight - oldScrollHeight;
          } else {
            messages.forEach(msg => {
              messagesWrapper.insertAdjacentHTML('beforeend', renderMessageHTML(msg));
            });

            if (isFirstLoad) {
              scrollToBottom();
              isFirstLoad = false;
            }
          }

          if (messagesWrapper.children.length === 0) {
            messagesWrapper.innerHTML = `
                                    <div id="no-messages-alert" class="text-center py-5 text-muted">
                                      <i class="fa-regular fa-comments fs-1 mb-3"></i>
                                      <p>لا توجد رسائل في هذه المحادثة بعد.</p>
                                    </div>`;
          }
        })
        .catch(err => console.error('Error fetching messages:', err))
        .finally(() => {
          isLoading = false;
          topSpinner.classList.add('d-none');
        });
    }

    chatContainer.addEventListener('scroll', function () {
      if (chatContainer.scrollTop === 0 && nextCursorUrl && !isLoading) {
        fetchMessages(nextCursorUrl, true);
      }
    });

    window.retryAudio = function (audioElement) {
      let retries = parseInt(audioElement.dataset.retries || '0');
      if (retries < 3) {
        console.warn("الملف قيد الحفظ على السيرفر، جاري إعادة المحاولة...", retries + 1);
        audioElement.dataset.retries = retries + 1;

        setTimeout(() => {
          const url = new URL(audioElement.src, window.location.origin);
          url.searchParams.set('t', new Date().getTime());

          audioElement.src = url.toString();
          audioElement.load();
        }, 1500);
      } else {
        console.error("فشل تحميل الصوت بعد عدة محاولات.");
      }
    };

    function renderMessageHTML(msg) {
      const senderTypeClass = msg.sender_type ? msg.sender_type.split('\\').pop() : '';
      const senderName = msg.sender ? (msg.sender.fullname || msg.sender.full_name || msg.sender.role || 'مستخدم') : 'مستخدم';

      let iconClass = 'fa-user-graduate';
      let roleName = 'طالب';
      let iconBg = 'bg-soft-info';

      if (senderTypeClass === 'Teacher') {
        iconClass = 'fa-user-tie';
        roleName = 'أستاذ';
        iconBg = 'bg-soft-warning';
      } else if (senderTypeClass === 'Admin') {
        iconClass = 'fa-user-shield';
        roleName = 'إدارة';
        iconBg = 'bg-soft-success';
      }

      let attachmentHTML = '';
      if (msg.attachment_path) {
        const timeStamp = new Date().getTime();
        const fileUrl = msg.attachment_path.includes('?')
          ? `${msg.attachment_path}&t=${timeStamp}`
          : `${msg.attachment_path}?t=${timeStamp}`;

        if (msg.attachment_type === 'image') {
          attachmentHTML = `<div class="mt-2"><img src="${fileUrl}" class="img-fluid rounded border" style="max-width: 250px;" alt="مرفق صورة"></div>`;
        } else if (msg.attachment_type === 'voice') {
          attachmentHTML = `
                  <div class="mt-3 mb-2" style="width: 100%; min-width: 280px;">
                    <audio controls preload="auto" class="w-100 shadow-sm rounded" style="height: 54px; outline: none;" 
                           src="${fileUrl}" 
                           onerror="retryAudio(this)">
                      متصفحك لا يدعم مشغل الصوت.
                    </audio>
                  </div>`;
        } else {
          attachmentHTML = `<div class="mt-2"><a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-paperclip me-1"></i> فتح المرفق</a></div>`;
        }
      }

      const profileClick = (senderTypeClass === 'Student' || senderTypeClass === 'Teacher')
        ? `onclick="openUserProfile('${senderTypeClass}', ${msg.sender_id})"`
        : '';

      return `
                                <div class="d-flex align-items-start mb-3 justify-content-between p-2 rounded msg-item" id="message-${msg.id}" 
                                     style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                                  <div class="d-flex align-items-start gap-2 w-100">
                                    <div class="p-2 rounded ${iconBg} d-flex align-items-center justify-content-center cursor-pointer flex-shrink-0" 
                                         style="width: 38px; height: 38px;" ${profileClick} title="عرض الملف الشخصي">
                                      <i class="fa-solid ${iconClass}"></i>
                                    </div>
                                    <div class="flex-grow-1" style="max-width: 85%;">
                                      <strong class="d-block cursor-pointer text-hover-primary" style="color: var(--text-main); font-size: 0.9rem;" ${profileClick}>
                                        ${senderName}
                                        <span class="text-muted fw-normal" style="font-size: 0.75rem;">(${roleName})</span>
                                      </strong>
                                      ${msg.body ? `<p class="mb-1 mt-1" style="color: var(--text-main); font-size: 0.95rem;">${msg.body}</p>` : ''}
                                      ${attachmentHTML}
                                      <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">${new Date(msg.created_at).toLocaleString('ar-EG')}</small>
                                    </div>
                                  </div>
                                  <button class="btn btn-sm btn-link text-danger border-0 p-1 flex-shrink-0" onclick="confirmDeleteMessage(${msg.id})" title="حذف هذه الرسالة">
                                    <i class="fa-regular fa-trash-can fs-5"></i>
                                  </button>
                                </div>`;
    }

    window.openUserProfile = function (type, id) {
      const modalContent = document.getElementById('user-profile-modal-content');
      modalContent.innerHTML = `
                                <div class="text-center py-5">
                                  <div class="spinner-border text-primary" role="status"></div>
                                  <p class="mt-2 text-muted">جاري تحميل بيانات الملف الشخصي...</p>
                                </div>`;
      profileModal.show();

      fetch(`/content-monitor/conversations/user-profile?type=${type}&id=${id}`, {
        headers: { 'Accept': 'application/json' }
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            modalContent.innerHTML = data.html;
          } else {
            modalContent.innerHTML = `<div class="p-4 text-center text-danger">${data.message || 'فشل تحميل الملف الشخصي.'}</div>`;
          }
        })
        .catch(err => {
          modalContent.innerHTML = `<div class="p-4 text-center text-danger">حدث خطأ أثناء جلب البيانات.</div>`;
        });
    }

    const sendForm = document.getElementById('send-message-form');
    if (sendForm) {
      let mediaRecorder;
      let audioChunks = [];
      let audioBlob = null;

      const recordBtn = document.getElementById('btn-record-voice');
      const recordIcon = recordBtn.querySelector('i');
      const attachmentPreview = document.getElementById('attachment-preview');
      const removeAttachmentBtn = document.getElementById('btn-remove-attachment');

      recordBtn.addEventListener('click', async () => {
        if (!mediaRecorder || mediaRecorder.state === 'inactive') {
          try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];

            mediaRecorder.ondataavailable = e => {
              if (e.data.size > 0) audioChunks.push(e.data);
            };

            mediaRecorder.onstop = () => {
              audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
              attachmentPreview.classList.remove('d-none');
              attachmentPreview.classList.add('d-flex');
            };

            mediaRecorder.start();
            recordIcon.classList.remove('fa-microphone');
            recordIcon.classList.add('fa-stop', 'text-danger', 'fa-beat-fade');
          } catch (err) {
            alert('عذراً، لا يمكن الوصول إلى الميكروفون.');
          }
        } else if (mediaRecorder.state === 'recording') {
          mediaRecorder.stop();
          mediaRecorder.stream.getTracks().forEach(track => track.stop());
          recordIcon.classList.remove('fa-stop', 'text-danger', 'fa-beat-fade');
          recordIcon.classList.add('fa-microphone');
        }
      });

      removeAttachmentBtn.addEventListener('click', function () {
        audioBlob = null;
        attachmentPreview.classList.add('d-none');
        attachmentPreview.classList.remove('d-flex');
      });

      sendForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        if (audioBlob) {
          formData.append('attachment', audioBlob, 'voice_record.webm');
        }

        const btnSend = document.getElementById('btn-send-msg');
        btnSend.disabled = true;

        fetch(`/content-monitor/conversations/${conversationId}/send`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: formData
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              const noMsgAlert = document.getElementById('no-messages-alert');
              if (noMsgAlert) noMsgAlert.remove();

              messagesWrapper.insertAdjacentHTML('beforeend', renderMessageHTML(data.data));
              scrollToBottom();
              sendForm.reset();
              removeAttachmentBtn.click();
            } else {
              alert(data.message || 'فشل إرسال الرسالة.');
            }
          })
          .catch(err => alert('حدث خطأ أثناء الإرسال.'))
          .finally(() => btnSend.disabled = false);
      });
    }

    window.confirmDeleteMessage = function (id) {
      messageIdToDelete = id;
      deleteModal.show();
    }

    document.getElementById('btn-confirm-delete').addEventListener('click', function () {
      if (!messageIdToDelete) return;

      this.blur();

      fetch(`/content-monitor/conversations/messages/${messageIdToDelete}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            const msgEl = document.getElementById('message-' + messageIdToDelete);
            if (msgEl) {
              msgEl.style.transition = 'all 0.4s ease';
              msgEl.style.opacity = '0';
              setTimeout(() => msgEl.remove(), 400);
            }
            deleteModal.hide();
          } else {
            alert('حدث خطأ أثناء محاولة الحذف.');
          }
        })
        .catch(err => alert('فشل الاتصال بالسيرفر.'));
    });

    function scrollToBottom() {
      chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    window.onload = function () {
      fetchMessages(nextCursorUrl);

      setTimeout(() => {
        if (typeof window.Echo !== 'undefined') {
          console.log("✅ Echo is ready! Connecting to Reverb...");

          window.Echo.private('conversation.' + conversationId)
            .listen('MessageDeleted', (e) => {
              console.log("🗑️ Message deleted event received", e);

              const deletedId = e.deleted_message_id || e.id;

              const deletedMsgEl = document.getElementById('message-' + deletedId);
              if (deletedMsgEl) {
                deletedMsgEl.style.transition = 'all 0.4s ease';
                deletedMsgEl.style.opacity = '0';
                setTimeout(() => deletedMsgEl.remove(), 400);
              }
            })
            .listen('MessageSent', (e) => {
              console.log("💬 New message received!", e);
              const messageData = e.message || e;

              if (messageData && messageData.id) {
                const noMsgAlert = document.getElementById('no-messages-alert');
                if (noMsgAlert) noMsgAlert.remove();

                messagesWrapper.insertAdjacentHTML('beforeend', renderMessageHTML(messageData));
                scrollToBottom();
              }
            });
        } else {
          console.error("❌ Laravel Echo is NOT defined. الرجاء التأكد من تشغيل 'npm run dev' واستدعاء app.js في ملف الـ Layout.");
        }
      }, 500);
    };
  </script>
@endpush