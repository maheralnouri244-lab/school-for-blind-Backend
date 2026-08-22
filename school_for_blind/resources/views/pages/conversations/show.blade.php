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

          @if(!$conversation->trashed())
            <form id="send-message-form" class="mt-auto" enctype="multipart/form-data">
              @csrf
              <div class="input-group p-1 rounded-3"
                style="background-color: var(--bg-card); border: 1px solid var(--border-color);">

                <div class="dropdown">
                  <button class="btn btn-link text-muted m-0 d-flex align-items-center" type="button"
                    id="attachmentDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="إرفاق ملف أو تسجيل">
                    <i class="fa-solid fa-paperclip fs-5 transition-transform" id="attachment-icon"></i>
                  </button>
                  <ul class="dropdown-menu shadow border-0" aria-labelledby="attachmentDropdown"
                    style="background-color: var(--bg-card);">
                    <li>
                      <button type="button" class="dropdown-item d-flex align-items-center gap-2" id="btn-select-file"
                        style="color: var(--text-main);">
                        <i class="fa-solid fa-file-arrow-up text-primary"></i> اختيار ملف من الجهاز
                      </button>
                    </li>
                    <li>
                      <button type="button" class="dropdown-item d-flex align-items-center gap-2" id="btn-record-voice"
                        style="color: var(--text-main);">
                        <i class="fa-solid fa-microphone text-danger"></i> تسجيل مقطع صوتي
                      </button>
                    </li>
                  </ul>
                </div>

                <input type="file" id="file-upload-input" name="attachment" class="d-none">
                <input type="hidden" id="is-voice-input" name="is_voice" value="0">

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
                <small id="attachment-file-name" class="text-muted flex-grow-1">مرفق جاهز للإرسال</small>
                <button type="button" class="btn-close btn-sm" id="btn-remove-attachment"></button>
              </div>
            </form>
          @else
            <div class="p-3 rounded bg-soft-danger text-danger text-center fw-bold mt-2"
              style="font-size: 1rem; border: 1px dashed var(--danger-color);">
              <i class="fa-solid fa-box-archive me-1"></i> هذه المحادثة مؤرشفة - للعرض والقراءة فقط.
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

  <div class="modal fade" id="attachmentPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content glass-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold" style="color: var(--text-main);">عرض المرفق</h5>
          <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center p-4" id="attachment-modal-body">
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
    const previewModal = new bootstrap.Modal(document.getElementById('attachmentPreviewModal'));

    // تفريغ المودال عند إغلاقه لإيقاف تشغيل الفيديوهات أو الأصوات
    document.getElementById('attachmentPreviewModal').addEventListener('hidden.bs.modal', function () {
      document.getElementById('attachment-modal-body').innerHTML = '';
    });

    // دالة فتح المرفق داخل المودال
    window.openAttachmentModal = function (type, url) {
      const modalBody = document.getElementById('attachment-modal-body');
      if (type === 'image') {
        modalBody.innerHTML = `<img src="${url}" class="img-fluid rounded shadow" style="max-height: 70vh;" alt="عرض الصورة">`;
      } else if (type === 'video') {
        modalBody.innerHTML = `
                        <video controls autoplay class="w-100 rounded shadow" style="max-height: 70vh; background: #000;">
                          <source src="${url}">
                          متصفحك لا يدعم تشغيل الفيديو.
                        </video>`;
      } else {
        modalBody.innerHTML = `<iframe src="${url}" class="w-100 rounded" style="height: 60vh; border: none;"></iframe>`;
      }
      previewModal.show();
    };

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

            const urlParams = new URLSearchParams(window.location.search);
            const targetMsgId = urlParams.get('msg');

            if (isFirstLoad) {
              if (targetMsgId) {
                scrollToAndHighlightMessage(targetMsgId);
              } else {
                scrollToBottom();
              }
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

    function scrollToAndHighlightMessage(messageId) {
      setTimeout(() => {
        const targetEl = document.getElementById('message-' + messageId);
        if (targetEl) {
          targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
          targetEl.style.transition = 'all 0.5s ease';
          targetEl.style.backgroundColor = 'rgba(220, 53, 69, 0.2)';
          targetEl.style.border = '2px solid #dc3545';
          setTimeout(() => {
            targetEl.style.backgroundColor = 'var(--bg-card)';
            targetEl.style.border = '1px solid var(--border-color)';
          }, 3000);
        }
      }, 300);
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
      const isDeleted = msg.deleted_at !== null;
      const deletedBadge = isDeleted ? '<span class="badge bg-soft-danger text-danger ms-2" style="font-size: 0.65rem;">رسالة محذوفة</span>' : '';
      const deletedStyle = isDeleted ? 'opacity: 0.7; background-color: var(--hover-bg) !important; border: 1px dashed var(--danger-color) !important;' : '';
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
          attachmentHTML = `
                          <div class="mt-2 position-relative d-inline-block">
                            <img src="${fileUrl}" class="img-fluid rounded border" style="max-height: 200px; cursor: pointer;" alt="مرفق صورة" onclick="openAttachmentModal('image', '${fileUrl}')">
                            <a href="${fileUrl}" download class="btn btn-sm btn-dark position-absolute bottom-0 start-0 m-1 opacity-75 hover-opacity-100" title="تحميل"><i class="fa-solid fa-download"></i></a>
                          </div>`;
        } else if (msg.attachment_type === 'video') {
          attachmentHTML = `
                          <div class="mt-2 position-relative d-inline-block" style="max-width: 250px; cursor: pointer;" onclick="openAttachmentModal('video', '${fileUrl}')">
                            <video src="${fileUrl}" class="img-fluid rounded border" style="max-height: 200px; background: #000;" preload="metadata"></video>
                            <div class="position-absolute top-50 start-50 translate-middle pointer-events-none">
                              <i class="fa-solid fa-circle-play text-white opacity-75" style="font-size: 3rem; text-shadow: 0 0 10px rgba(0,0,0,0.5);"></i>
                            </div>
                            <a href="${fileUrl}" download class="btn btn-sm btn-dark position-absolute bottom-0 start-0 m-1 opacity-75 hover-opacity-100" title="تحميل" style="z-index: 2;" onclick="event.stopPropagation();"><i class="fa-solid fa-download"></i></a>
                          </div>`;
        } else if (msg.attachment_type === 'voice') {
          attachmentHTML = `
                          <div class="mt-3 mb-2 d-flex align-items-center gap-2" style="width: 100%; min-width: 280px;">
                            <audio controls preload="auto" class="flex-grow-1 shadow-sm rounded" style="height: 54px; outline: none;" src="${fileUrl}" onerror="retryAudio(this)">
                              متصفحك لا يدعم مشغل الصوت.
                            </audio>
                            <a href="${fileUrl}" download="voice_record.webm" class="btn btn-outline-secondary btn-sm" title="تحميل"><i class="fa-solid fa-download"></i></a>
                          </div>`;
        } else {
          attachmentHTML = `
                          <div class="mt-2 d-flex gap-2 align-items-center">
                            <button type="button" onclick="openAttachmentModal('file', '${fileUrl}')" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye me-1"></i> عرض</button>
                            <a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-download me-1"></i> تحميل</a>
                          </div>`;
        }
      }

      const profileClick = (senderTypeClass === 'Student' || senderTypeClass === 'Teacher')
        ? `onclick="openUserProfile('${senderTypeClass}', ${msg.sender_id})"`
        : '';

      return `
                <div class="d-flex align-items-start mb-3 justify-content-between p-2 rounded msg-item" id="message-${msg.id}" 
                     style="background-color: var(--bg-card); border: 1px solid var(--border-color); ${deletedStyle}">
                  <div class="d-flex align-items-start gap-2 w-100">
                    <div class="p-2 rounded ${iconBg} d-flex align-items-center justify-content-center cursor-pointer flex-shrink-0" 
                         style="width: 38px; height: 38px;" ${profileClick} title="عرض الملف الشخصي">
                      <i class="fa-solid ${iconClass}"></i>
                    </div>
                    <div class="flex-grow-1" style="max-width: 85%;">
                      <strong class="d-block cursor-pointer text-hover-primary" style="color: var(--text-main); font-size: 0.9rem;" ${profileClick}>
                        ${senderName}
                        <span class="text-muted fw-normal" style="font-size: 0.75rem;">(${roleName})</span>
                        ${deletedBadge}
                      </strong>
                      ${msg.body ? `<p class="mb-1 mt-1" style="color: var(--text-main); font-size: 0.95rem;">${isDeleted ? '<del>' + msg.body + '</del>' : msg.body}</p>` : ''}
                      ${attachmentHTML}
                      <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">${new Date(msg.created_at).toLocaleString('ar-EG')}</small>
                    </div>
                  </div>
                  ${!isDeleted ? `
                  <button class="btn btn-sm btn-link text-danger border-0 p-1 flex-shrink-0" onclick="confirmDeleteMessage(${msg.id})" title="حذف هذه الرسالة">
                    <i class="fa-regular fa-trash-can fs-5"></i>
                  </button>
                  ` : ''}
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
      const selectFileBtn = document.getElementById('btn-select-file');
      const fileInput = document.getElementById('file-upload-input');
      const isVoiceInput = document.getElementById('is-voice-input');
      const attachmentPreview = document.getElementById('attachment-preview');
      const removeAttachmentBtn = document.getElementById('btn-remove-attachment');
      const attachmentFileName = document.getElementById('attachment-file-name');

      // 1. اختيار ملف من الجهاز
      if (selectFileBtn && fileInput) {
        selectFileBtn.addEventListener('click', () => {
          fileInput.click();
        });

        fileInput.addEventListener('change', function () {
          if (this.files && this.files[0]) {
            audioBlob = null;
            isVoiceInput.value = '0';
            attachmentFileName.innerHTML = `<i class="fa-solid fa-file me-2 text-primary"></i> ${this.files[0].name}`;
            attachmentPreview.classList.remove('d-none');
            attachmentPreview.classList.add('d-flex');
          }
        });
      }

      // 2. تسجيل صوتي
      if (recordBtn) {
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
                fileInput.value = '';
                isVoiceInput.value = '1';
                attachmentFileName.innerHTML = `<i class="fa-solid fa-microphone-lines text-info fa-beat me-2"></i> مقطع صوتي جاهز للإرسال`;
                attachmentPreview.classList.remove('d-none');
                attachmentPreview.classList.add('d-flex');
              };

              mediaRecorder.start();
              const icon = document.getElementById('attachment-icon');
              icon.classList.replace('fa-paperclip', 'fa-microphone');
              icon.classList.add('text-danger', 'fa-beat-fade');
            } catch (err) {
              alert('عذراً، لا يمكن الوصول إلى الميكروفون.');
            }
          } else if (mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
            const icon = document.getElementById('attachment-icon');
            icon.classList.remove('fa-microphone', 'text-danger', 'fa-beat-fade');
            icon.classList.add('fa-paperclip');
          }
        });
      }

      // 3. حذف المرفق المحدد
      if (removeAttachmentBtn) {
        removeAttachmentBtn.addEventListener('click', function () {
          audioBlob = null;
          fileInput.value = '';
          isVoiceInput.value = '0';
          attachmentPreview.classList.add('d-none');
          attachmentPreview.classList.remove('d-flex');
        });
      }

      // 4. إرسال النموذج
      sendForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        if (audioBlob) {
          formData.append('attachment', audioBlob, 'voice_record.webm');
        }

        const btnSend = document.getElementById('btn-send-msg');
        btnSend.disabled = true;

        const socketId = window.Echo ? window.Echo.socketId() : '';

        fetch(`/content-monitor/conversations/${conversationId}/send`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Socket-ID': socketId
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
              if (removeAttachmentBtn) removeAttachmentBtn.click();
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
              // تحديث مظهر الرسالة بدلاً من إزالتها
              msgEl.style.transition = 'all 0.4s ease';
              msgEl.style.opacity = '0.7';
              msgEl.style.backgroundColor = 'var(--hover-bg)';
              msgEl.style.border = '1px dashed var(--danger-color)';

              // إضافة شارة "رسالة محذوفة" بجانب الاسم
              const nameContainer = msgEl.querySelector('strong');
              if (nameContainer && !nameContainer.innerHTML.includes('رسالة محذوفة')) {
                nameContainer.insertAdjacentHTML('beforeend', ' <span class="badge bg-soft-danger text-danger ms-2" style="font-size: 0.65rem;">رسالة محذوفة</span>');
              }

              // إخفاء زر الحذف
              const deleteBtn = msgEl.querySelector('button[title="حذف هذه الرسالة"]');
              if (deleteBtn) deleteBtn.remove();

              // شطب النص
              const textBody = msgEl.querySelector('p');
              if (textBody && !textBody.innerHTML.includes('<del>')) {
                textBody.innerHTML = '<del>' + textBody.innerHTML + '</del>';
              }
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
              const msgEl = document.getElementById('message-' + deletedId);

              if (msgEl) {
                // بدلاً من إزالة العنصر، نقوم بتحديث مظهره
                msgEl.style.transition = 'all 0.4s ease';
                msgEl.style.opacity = '0.7';
                msgEl.style.backgroundColor = 'var(--hover-bg)';
                msgEl.style.border = '1px dashed var(--danger-color)';

                // إخفاء زر الحذف
                const deleteBtn = msgEl.querySelector('button[title="حذف هذه الرسالة"]');
                if (deleteBtn) deleteBtn.remove();

                // شطب النص إن أردت (اختياري)
                const textBody = msgEl.querySelector('p');
                if (textBody && !textBody.innerHTML.includes('<del>')) {
                  textBody.innerHTML = '<del>' + textBody.innerHTML + '</del>';
                }
              }
            });
        } else {
          console.error("❌ Laravel Echo is NOT defined. الرجاء التأكد من تشغيل 'npm run dev' واستدعاء app.js في ملف الـ Layout.");
        }
      }, 500);
    };
  </script>
@endpush