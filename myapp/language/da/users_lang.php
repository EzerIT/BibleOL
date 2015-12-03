<?php

$lang['display_user'] = 'Vis bruger';  // Text in title bar
$lang['edit_user'] = 'Redigér bruger';  // Text in title bar
$lang['create_account'] = 'Opret konto';  // Text in title bar
$lang['users'] = 'Brugere';  // Text in title bar
$lang['you_login_google'] = 'Du er logget ind med Google';
$lang['you_login_facebook'] = 'Du er logget ind med Facebook';
$lang['user_name'] = 'Brugernavn';
$lang['first_name'] = 'Fornavn';
$lang['last_name'] = 'Efternavn';
$lang['email'] = 'Email';
$lang['password'] = 'Adgangskode';
$lang['new_password'] = 'Ny adgangskode';
$lang['repeat_password'] = 'Gentag adgangskode';
$lang['repeat_new_password'] = 'Gentag ny adgangskode';
$lang['cannot_change'] = '(Kan ikke ændres)';
$lang['leave_blank_pw'] = '(Udelades hvis du ikke vil ændre adgangskode)';
$lang['preferred_language'] = 'Foretrukket sprog';
$lang['no_language'] = 'Intet angivet';
$lang['english'] = 'Engelsk';
$lang['danish'] = 'Dansk';
$lang['number_of_users'] = 'Antal brugere: %s';
$lang['showing_per_page'] = 'Der vises %s brugere per side';

$lang['this_your_profile'] = 'Dette er din brugerprofil';
$lang['change_through_google'] = 'Hvis du vil ændre dit navn eller email-adresse, skal du gøre det i din profil hos Google.';
$lang['change_through_facebook'] = 'Hvis du vil ændre dit navn eller email-adresse, skal du gøre det i din profil hos Facebook.';
$lang['delete_profile'] = 'Nedlæg profil'; // Text in title bar on pop-up dialog
$lang['delete_oauth2_profile1'] = 'Ønsker du at nedlægge din konto på denne server inklusive alle dine data på dette websted?';
$lang['delete_google_profile2'] = 'NB: Dette vil ikke ændre din konto hos Google, men det vil fjerne Bible Online Learner fra din liste over godkendte programmer.';
$lang['delete_facebook_profile2'] = 'NB: Dette vil ikke ændre din konto hos Facebook, og det vil ikke fjerne Bible Online Learner fra din liste over godkendte programmer.';
$lang['delete_profile_button'] = 'Nedlæg profilen';

$lang['edit_user_profile'] = 'Redigér brugerprofil';
$lang['click_to_delete_profile'] = 'Klik på denne knap for at nedlægge din profil:';
$lang['delete_profile_confirm'] = 'Vil du nedlægge din konto og fjerne alle dine data fra dette websted?';

$lang['pw_min_length'] = 'Adgangskoden skal bestå af mindst %d tegn.'; 

$lang['user_list'] = 'Brugerfortegnelse';
$lang['configure_your_users'] = 'Konfigurér dine brugere';
$lang['administrator'] = 'Sysadmin';
$lang['teacher'] = 'Facilitator';
$lang['last_login'] = 'Sidste login';
$lang['never'] = 'Aldrig';
$lang['user_operations'] = 'Funktioner';

$lang['assign_to_class'] = 'Tildel til klasse';
$lang['user_edit'] = 'Redigér';
$lang['user_delete'] = 'Slet';

$lang['delete_user'] = 'Slet bruger'; // Confirmation dialog title
$lang['delete_user_confirm'] = 'Ønsker du at slette bruger %s og fjerne vedkommendes data fra dette websted?';
$lang['add_user'] = 'Tilføj ny bruger';

$lang['edit_user_information'] = 'Redigér brugeroplysninger';
$lang['specify_user_information'] = 'Angiv brugeroplysninger';
$lang['this_user_google'] = 'Denne bruger logger ind med Google';
$lang['this_user_facebook'] = 'Denne bruger logger ind med Facebook';

$lang['user_name_used'] = 'Brugernavnet "%s" er optaget';

$lang['account_created_subject']  = 'Bible Online Learner - konto oprettet';
$lang['account_created_message1'] = "Kære %s %s,\n\n"  // First name, last name
                                    . "Du har nu en konto på webstedet Bible Online Learner.\n\n"
                                    . "Dit brugernavn er: %s\n" // Username
                                    . "Din adgangskode er: %s\n\n"; // Password
$lang['account_created_message2'] = "Du har fået tildelt administratorrettigheder.\n\n";
$lang['account_created_message2t'] = "Du har fået tildelt facilitator-rettigheder.\n\n";
$lang['account_created_message3'] = "Gå til %s, og log ind;\n" // Site URL
                                    . "derefter kan du ændre din adgangskode ved at vælge 'Profil' fra menuen 'Mine data'.\n";

$lang['account_you_created_message1'] = "Kære %s %s,\n\n"  // First name, last name
                                    . "Du har oprettet en konto på webstedet Bible Online Learner.\n\n"
                                    . "Dit brugernavn er: %s\n" // Username
                                    . "Din adgangskode er: %s\n\n"; // Password
$lang['account_you_created_message3'] = "Gå til %s, og log ind;\n" // Site URL
                                    . "derefter kan du ændre din adgangskode ved at vælge 'Profil'\n"
                                    . "fra menuen 'Mine data'.\n\n"
                                    . "Hvis du ikke logger ind inden der er gået 48 timer, vil din\n"
                                    . "konto blive slettet.\n\n"
                                    . "Hvis det ikke er dig der har oprettet en konto, kan du ignorere denne mail.\n\n";

$lang['you_created_account'] = "En ny konto er oprettet";
$lang['password_sent']       = 'En email med dit brugernavn og en adgangskode er blevet sendt til %s'; // Email address

$lang['user_profile_deleted'] = 'Brugerprofil nedlagt'; // Text in title bar
$lang['your_account_deleted'] = 'Din konto på denne server er nedlagt';
$lang['go_to_home'] = 'Gå til hjemmesiden';

$lang['google_no_response_delete'] = 'Google svarede ikke på denne anmodning. Dette sker typisk hvis du loggede ind for mere end en time siden. Prøv at logge ind her igen, og nedlæg så din profil.';

$lang['cannot_delete_self'] = 'Du kan ikke slette dig selv';

$lang['expiry_warning_subject'] = 'Inaktiv konto på Bible Online Learner';
$lang['expiry_warning_1_message'] = "Kære %s %s,\n\n"  // First name, last name
                                  . "Du har ikke benyttet din konto på %s i ni måneder.\n" // Site URL
                                  . "Om yderligere ni måneder bliver din konto slettet.\n\n";
$lang['expiry_warning_2_message'] = "Kære %s %s,\n\n"  // First name, last name
                                  . "Du har ikke benyttet din konto på %s i 17 måneder.\n" // Site URL
                                  . "Om en måned bliver din konto slettet.\n\n";

$lang['expiry_warning_message_local'] = "Hvis du ønsker at bevare din konto, skal du blot gå til ovennævnte\n"
                                      . "websted og logge ind med brugernavnet '%s'."; // Username
$lang['expiry_warning_message_google'] = "Hvis du ønsker at bevare din konto, skal du blot gå til ovennævnte\n"
                                       . "websted og logge ind med din Google-konto.";
$lang['expiry_warning_message_facebook'] = "Hvis du ønsker at bevare din konto, skal du blot gå til ovennævnte\n"
                                         . "websted og logge ind med din Facebook-konto.";

$lang['only_admin_delete'] = 'Kun sysadmins kan slette facilitatorer og sysadmins';