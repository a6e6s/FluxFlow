<?php

return [
    'collaborators' => 'المتعاونون',
    'invite_label' => 'دعوة عبر البريد الإلكتروني',
    'invite_placeholder' => 'name@example.com',
    'invite_button' => 'دعوة',
    'inviting' => 'جاري الإرسال...',
    'no_collaborators' => 'لا يوجد متعاونون بعد',
    'pending' => 'قيد الانتظار',
    'pending_invitations' => 'الدعوات المعلقة',
    'remove' => 'إزالة',
    'cancel_invitation' => 'إلغاء الدعوة',
    'invited_owner' => 'هذا المستخدم هو مالك المشروع.',
    'already_collaborator' => 'هذا المستخدم متعاون بالفعل.',
    'attached' => 'تمت إضافة :name إلى المهمة.',
    'invited' => 'تم إرسال الدعوة إلى :email.',
    'invalid_email' => 'يرجى إدخال بريد إلكتروني صالح.',
    'collaborator_removed' => 'تمت إزالة المتعاون.',
    'invitation_cancelled' => 'تم إلغاء الدعوة.',

    'preview' => [
        'existing_user' => 'مستخدم مسجل',
        'new_invite' => 'سيتم إرسال دعوة جديدة',
    ],

    'notification' => [
        'body' => 'قام :inviter بدعوتك للتعاون في "‎:task‎".',
    ],

    'mail' => [
        'subject' => 'تمت دعوتك للتعاون في "‎:task‎"',
        'greeting' => 'مرحباً،',
        'line1' => 'قام :inviter بدعوتك للتعاون في المهمة "‎:task‎" ضمن مشروع "‎:project‎".',
        'line2' => 'اضغط على الزر أدناه لقبول الدعوة. إذا لم يكن لديك حساب بعد، يمكنك التسجيل أولاً وسيتم ربط المهمة بحسابك تلقائياً.',
        'cta' => 'قبول الدعوة',
        'fallback' => 'إذا لم يعمل الزر، انسخ الرابط التالي في متصفحك:',
    ],

    'accepted' => 'تم قبول الدعوة. مرحباً بك في المهمة!',
    'declined' => 'تم رفض الدعوة.',
    'expired' => 'انتهت صلاحية هذه الدعوة.',
    'email_mismatch' => 'هذه الدعوة تخص بريداً إلكترونياً مختلفاً.',

    'drawer' => [
        'title' => 'الإشعارات',
        'invited_you' => 'دعاك للتعاون في مهمة',
        'accept' => 'قبول',
        'decline' => 'رفض',
        'empty_title' => 'لا توجد إشعارات جديدة',
        'empty_subtitle' => 'ستظهر هنا الدعوات الجديدة للتعاون في المهام.',
        'unknown_sender' => 'مستخدم',
        'unknown_project' => 'مشروع غير معروف',
        'unknown_task' => 'مهمة غير معروفة',
    ],
];
