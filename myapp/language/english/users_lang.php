<?php

$lang['display_user'] = 'Display user';  // Text in title bar
$lang['edit_user'] = 'Edit user';  // Text in title bar
$lang['create_account'] = 'Create account';  // Text in title bar
$lang['users'] = 'Users';  // Text in title bar
$lang['you_login_google'] = 'You have logged in with Google';
$lang['you_login_facebook'] = 'You have logged in with Facebook';
$lang['user_name'] = 'User name';
$lang['first_name'] = 'First name';
$lang['last_name'] = 'Last name';
$lang['email'] = 'Email';
$lang['password'] = 'Password';
$lang['new_password'] = 'New password';
$lang['repeat_password'] = 'Repeat password';
$lang['repeat_new_password'] = 'Repeat new password';
$lang['cannot_change'] = '(Cannot be changed)';
$lang['leave_blank_pw'] = '(Leave blank if not changing password)';
$lang['preferred_language'] = 'Preferred language';
$lang['no_language'] = 'None specified';
$lang['english'] = 'English';
$lang['simp_chinese'] = 'Chinese (simplified)';
$lang['trad_chinese'] = 'Chinese (traditional)';
$lang['danish'] = 'Danish';
$lang['portuguese'] = 'Portuguese';
$lang['spanish'] = 'Spanish';
$lang['number_of_users'] = 'Number of users: %s';
$lang['showing_per_page'] = 'Each page shows %s users';

$lang['this_your_profile'] = 'This is your user profile';
$lang['change_through_google'] = 'If your want to change your name or email address, you must do so in your Google profile.';
$lang['change_through_facebook'] = 'If your want to change your name or email address, you must do so in your Facebook profile.';
$lang['delete_profile'] = 'Delete profile'; // Text in title bar on pop-up dialog
$lang['delete_oauth2_profile1'] = 'Do you want to delete your account on this server including all your data on this site?';
$lang['delete_google_profile2'] = 'Note: This will not change your account on Google, but it will remove Bible Online Learner from your list of approved applications.';
$lang['delete_facebook_profile2'] = 'Note: This will not change your account on Facebook, nor will it remove Bible Online Learner from your list of approved applications.';
$lang['delete_profile_button'] = 'Delete the profile';

$lang['edit_user_profile'] = 'Edit user profile';
$lang['click_to_delete_profile'] = 'Click on this button to delete your profile:';
$lang['delete_profile_confirm'] = 'Do you want to delete your account including all your data on this site?';

$lang['pw_min_length'] = 'The Password field, if specified, must be at least %d characters in length.'; 

$lang['user_list'] = 'User List';
$lang['configure_your_users'] = 'Configure your users';
$lang['administrator'] = 'Sysadmin';
$lang['teacher'] = 'Facilitator';
$lang['last_login'] = 'Last login';
$lang['never'] = 'Never';
$lang['user_operations'] = 'Operations';

$lang['assign_to_class'] = 'Assign to class';
$lang['user_edit'] = 'Edit';
$lang['user_delete'] = 'Delete';

$lang['delete_user'] = 'Delete user'; // Confirmation dialog title
$lang['delete_user_confirm'] = 'Do you want to delete user %s including all their data on this site?';
$lang['add_user'] = 'Add new user';

$lang['edit_user_information'] = 'Edit user information';
$lang['specify_user_information'] = 'Specify user information';
$lang['this_user_google'] = 'This user logs in with Google';
$lang['this_user_facebook'] = 'This user logs in with Facebook';

$lang['user_name_used'] = 'The user name "%s" is already in use';

$lang['account_created_subject']  = 'Bible Online Learner account created';
$lang['account_created_message1'] = "Dear %s,\n\n"  // First name, last name
                                    . "You now have an account at the Bible Online Learner website.\n\n"
                                    . "Your user name is: %s\n" // Username
                                    . "Your password is: %s\n\n"; // Password
$lang['account_created_message2'] = "You have been granted administrator privileges.\n\n";
$lang['account_created_message2t'] = "You have been granted facilitator privileges.\n\n";
$lang['account_created_message3'] = "Please visit %s, and log in;\n" // Site URL
                                    . "then change your password by selecting 'Profile' from the 'My data' menu.\n";

$lang['account_you_created_message1'] = "Dear %s,\n\n"  // First name, last name
                                    . "You have created an account at the Bible Online Learner website.\n\n"
                                    . "Your user name is: %s\n" // Username
                                    . "Your password is: %s\n\n"; // Password
$lang['account_you_created_message3'] = "Please visit %s, and log in;\n" // Site URL
                                    . "then change your password by selecting 'Profile' from the 'My data' menu.\n\n"
                                    . "If you do not log in within 48 hours, your account will be deleted.\n\n"
                                    . "If you did not create an account, you may ignore this message.\n";

$lang['you_created_account'] = "A new account has been created";
$lang['password_sent']       = 'An email containing your user name and password has been sent to %s'; // Email address

$lang['user_profile_deleted'] = 'User Profile Deleted'; // Text in title bar
$lang['your_account_deleted'] = 'Your account on this server has been deleted';
$lang['go_to_home'] = 'Go to home page';

$lang['google_no_response_delete'] = 'Google did not respond to this request. This typically happens if you logged in here more than an hour ago. Try logging in here again and then delete your profile.';

$lang['cannot_delete_self'] = 'You cannot delete yourself';

$lang['expiry_warning_subject'] = 'Inactive account on Bible Online Learner';
$lang['expiry_warning_1_message'] = "Dear %s,\n\n"  // First name, last name
                                  . "You have not used your account on %s for nine months.\n" // Site URL
                                  . "In another nine months, your account will be deleted.\n\n";
$lang['expiry_warning_2_message'] = "Dear %s,\n\n"  // First name, last name
                                  . "You have not used your account on %s for 17 months.\n" // Site URL
                                  . "In one more month, your account will be deleted.\n\n";

$lang['expiry_warning_message_local'] = "If you want to keep your account, simply go to the website mentioned above\n"
                                         . "and log in using the user name '%s'."; // Username
$lang['expiry_warning_message_google'] = "If you want to keep your account, simply go to the website mentioned above\n"
                                          . "and log in using your Google account.";
$lang['expiry_warning_message_facebook'] = "If you want to keep your account, simply go to the website mentioned above\n"
                                            . "and log in using your Facebook account.";


$lang['only_admin_delete'] = 'Only sysadmins can delete facilitators and sysadmins';