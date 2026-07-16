@extends('layouts.app')

@section('content')
 <div class="container-fluid p-0">
  {{-- رأس الصفحة --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
   <div>
    <h4 class="fw-bold mb-1" style="color: var(--text-main);">{{ $conversation->name }}</h4>
    <small class="text-muted">أستاذ المادة: {{ $conversation->teacher->full_name ?? 'غير محدد' }}</small>
   </div>
   <a href="{{ route('dashboard.conversations.index') }}" class="btn btn-sm"
    style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">
    <i class="fa-solid fa-arrow-right me-1"></i> العودة للمحادثات
   </a>
  </div>

  <div class="row">
   <div class="col-12">
    {{-- صندوق المحادثة --}}
    <div class="custom-card d-flex flex-column" style="height: 600px; padding: 20px;">

     {{-- منطقة الرسائل --}}
     <div id="chat-messages" class="flex-grow-1 overflow-y-auto mb-3 p-3"
      style="background-color: var(--bg-main); border-radius: 8px; border: 1px solid var(--border-color);">
      @forelse($conversation->messages as $message)
       <div class="d-flex align-items-start mb-3 justify-content-between p-2 rounded msg-item"
        id="message-{{ $message->id }}" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">

        <div class="d-flex align-items-start gap-2">
         {{-- أيقونة تدل على نوع المرسل --}}
         <div class="p-2 rounded bg-soft-info d-flex align-items-center justify-content-center"
          style="width: 35px; height: 35px;">
          @if(class_basename($message->sender_type) === 'Teacher')
           <i class="fa-solid fa-user-tie text-info"></i>
          @else
           <i class="fa-solid fa-graduation-cap text-info"></i>
          @endif
         </div>

         <div>
          <strong class="d-block" style="color: var(--text-main); font-size: 0.9rem;">
           {{ $message->sender->fullname ?? $message->sender->full_name ?? 'مستخدم' }}
           <span class="text-muted fw-normal"
            style="font-size: 0.75rem;">({{ class_basename($message->sender_type) === 'Teacher' ? 'أستاذ' : 'طالب' }})</span>
          </strong>

          {{-- متن الرسالة --}}
          @if($message->body)
           <p class="mb-1 mt-1" style="color: var(--text-main);">{{ $message->body }}</p>
          @endif

          {{-- في حال وجود ملف مرفق --}}
          @if($message->attachment_path)
           <div class="mt-1">
            @if($message->attachment_type === 'image')
             <img src="{{ asset($message->attachment_path) }}" class="img-fluid rounded" style="max-width: 200px;"
              alt="مرفق">
            @else
             <a href="{{ asset($message->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1"
              style="font-size: 0.8rem;">
              <i class="fa-solid fa-paperclip me-1"></i> تحميل المرفق
             </a>
            @endif
           </div>
          @endif

          <small class="text-muted d-block mt-1"
           style="font-size: 0.7rem;">{{ $message->created_at->format('Y-m-d H:i') }}</small>
         </div>
        </div>

        {{-- زر الحذف المخصص للآدمن --}}
        <button class="btn btn-sm btn-link text-danger border-0 p-1" onclick="confirmDeleteMessage({{ $message->id }})"
         title="حذف هذه الرسالة">
         <i class="fa-regular fa-trash-can fs-5"></i>
        </button>
       </div>
      @empty
       <div id="no-messages-alert" class="text-center py-5 text-muted">
        <i class="fa-regular fa-comments fs-1 mb-3"></i>
        <p>لا توجد رسائل في هذه المحادثة بعد.</p>
       </div>
      @endforelse
     </div>

     {{-- تنبيه بأن القنوات للقراءة فقط للآدمن أيضاً في هذه الواجهة --}}
     <div class="p-2 rounded bg-soft-warning text-warning text-center" style="font-size: 0.9rem;">
      <i class="fa-solid fa-circle-info me-1"></i> هذه الواجهة مخصصة لمراقبة وضبط المحتوى وحذف المخالفات فقط.
     </div>

    </div>
   </div>
  </div>
 </div>

 {{-- مودال تأكيد الحذف (Glass Modal متناسق مع تصميمكم) --}}
 <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
   <div class="modal-content glass-modal">
    <div class="modal-header border-0">
     <h5 class="modal-title fw-bold text-danger">تأكيد حذف الرسالة</h5>
     <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body text-end">
     <p style="color: var(--text-main);">هل أنت متأكد من رغبتك في حذف هذه الرسالة نهائياً من المحادثة؟ لا يمكن التراجع عن
      هذا الإجراء.</p>
    </div>
    <div class="modal-footer border-0">
     <button type="button" class="btn btn-sm" data-bs-dismiss="modal"
      style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color);">إلغاء</button>
     <button type="button" class="btn btn-sm btn-reject px-3" id="btn-confirm-delete">حذف الآن</button>
    </div>
   </div>
  </div>
 </div>
@endsection

@push('scripts')
 <script>
  let messageIdToDelete = null;
  const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

  // 1. فتح مودال تأكيد الحذف وحفظ معرف الرسالة
  function confirmDeleteMessage(id) {
   messageIdToDelete = id;
   deleteModal.show();
  }

  // 2. إرسال طلب الحذف للـ Backend عند تأكيد العملية
  document.getElementById('btn-confirm-delete').addEventListener('click', function () {
   if (!messageIdToDelete) return;

   fetch(`/content-monitor/conversations/messages/${messageIdToDelete}`, {
    method: 'DELETE',
    headers: {
     'X-CSRF-TOKEN': '{{ csrf_token() }}',
     'Accept': 'application/json',
     'Content-Type': 'application/json'
    }
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      // إزالة الرسالة محلياً من شاشة الآدمن الذي قام بالحذف
      const msgEl = document.getElementById('message-' + messageIdToDelete);
      if (msgEl) {
       msgEl.remove();
      }
      deleteModal.hide();
     } else {
      alert('حدث خطأ أثناء محاولة الحذف.');
     }
    })
    .catch(error => {
     console.error('Error:', error);
     alert('فشل الاتصال بالسيرفر.');
    });
  });

  // 3. الجزء السحري: الاستماع اللحظي (Real-time Listener) عبر Laravel Echo
  // إذا قام شخص آخر بالحذف، تنحذف الرسالة فوراً من الشاشة دون تحديث الصفحة
  window.onload = function () {
   // النزول لأسفل المحادثة تلقائياً عند فتح الصفحة
   const chatContainer = document.getElementById('chat-messages');
   chatContainer.scrollTop = chatContainer.scrollHeight;

   if (typeof window.Echo !== 'undefined') {
    window.Echo.private('conversation.' + {{ $conversation->id }})
     .listen('MessageDeleted', (e) => {
      console.log('حدث حذف رسالة لحظي:', e);
      const deletedMsgEl = document.getElementById('message-' + e.deleted_message_id);
      if (deletedMsgEl) {
       deletedMsgEl.style.transition = 'all 0.5s ease';
       deletedMsgEl.style.opacity = '0';
       deletedMsgEl.style.transform = 'scale(0.9)';

       // حذف العنصر نهائياً بعد انتهاء تأثير الأنيميشن
       setTimeout(() => {
        deletedMsgEl.remove();
        // إذا فرغت المحادثة، أظهر تنبيه فارغ
        if (document.querySelectorAll('.msg-item').length === 0) {
         location.reload(); // لإظهار ديف "لا توجد رسائل"
        }
       }, 500);
      }
     });
   }
  };
 </script>
@endpush