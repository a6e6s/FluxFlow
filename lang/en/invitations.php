<?php

return [
    'collaborators' => 'Collaborators',
    'invite_label' => 'Invite by email',
    'invite_placeholder' => 'name@example.com',
    'invite_button' => 'Invite',
    'inviting' => 'Inviting...',
    'no_collaborators' => 'No collaborators yet',
    'pending' => 'Pending',
    'pending_invitations' => 'Pending invitations',
    'remove' => 'Remove',
    'cancel_invitation' => 'Cancel invitation',
    'invited_owner' => 'This user owns the project.',
    'already_collaborator' => 'This user is already a collaborator.',
    'attached' => ':name has been added to the task.',
    'invited' => 'Invitation sent to :email.',
    'invalid_email' => 'Please enter a valid email address.',
    'collaborator_removed' => 'Collaborator removed.',
    'invitation_cancelled' => 'Invitation cancelled.',

    'preview' => [
        'existing_user' => 'Existing user',
        'new_invite' => 'New invitation will be sent',
    ],

    'notification' => [
        'body' => ':inviter invited you to collaborate on “:task”.',
    ],

    'mail' => [
        'subject' => 'You have been invited to collaborate on “:task”',
        'greeting' => 'Hello,',
        'line1' => ':inviter has invited you to collaborate on the task “:task” in project “:project”.',
        'line2' => 'Click the button below to accept the invitation. If you do not have an account yet, you can register first and the task will be linked to you automatically.',
        'cta' => 'Accept invitation',
        'fallback' => 'If the button does not work, copy the link below into your browser:',
    ],

    'accepted' => 'Invitation accepted. Welcome to the task!',
    'declined' => 'Invitation declined.',
    'expired' => 'This invitation has expired.',
    'email_mismatch' => 'This invitation belongs to a different email address.',

    'drawer' => [
        'title' => 'Notifications',
        'invited_you' => 'invited you to collaborate on a task',
        'accept' => 'Accept',
        'decline' => 'Decline',
        'empty_title' => 'You are all caught up',
        'empty_subtitle' => 'New invitations to collaborate on tasks will appear here.',
        'unknown_sender' => 'Someone',
        'unknown_project' => 'Unknown project',
        'unknown_task' => 'Unknown task',
    ],
];
