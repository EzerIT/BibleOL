<?php

$lang['display_user'] = 'Mostrar usuario';  // Text in title bar
$lang['edit_user'] = 'Editar usuario';  // Text in title bar
$lang['create_account'] = 'Crear cuenta';  // Text in title bar
$lang['users'] = 'Usuarios';  // Text in title bar
$lang['you_login_google'] = 'Usted ha iniciado sesión con Google';
$lang['you_login_facebook'] = 'Usted ha iniciado sesión con Facebook';
$lang['user_name'] = 'Nombre de usuario';
$lang['first_name'] = 'Primer nombre';
$lang['last_name'] = 'Apellido';
$lang['email'] = 'Correo Electrónico';
$lang['password'] = 'Contraseña';
$lang['new_password'] = 'Nueva contraseña';
$lang['repeat_password'] = 'Repetir contraseña';
$lang['repeat_new_password'] = 'Repetir nueva contraseña';
$lang['cannot_change'] = '(No puede ser cambiado)';
$lang['leave_blank_pw'] = '(Deje en blanco si no va a cambiar contraseña)';
$lang['preferred_language'] = 'Lenguaje preferido';
$lang['no_language'] = 'Ninguno específico';
$lang['english'] = 'Inglés';
$lang['danish'] = 'Danés';
$lang['portuguese'] = 'Portugués';
$lang['spanish'] = 'Español';
$lang['number_of_users'] = 'Número de usuarios: %s';
$lang['showing_per_page'] = 'Cada página muestra %s usuarios';

$lang['this_your_profile'] = 'Este es tu perfil de usuario';
$lang['change_through_google'] = 'Si quieres cambiar tu nombre o correo electrónico, debes hacerlo en tu perfil de Google.';
$lang['change_through_facebook'] = 'Si quieres cambiar tu nombre o correo electrónico, debes hacerlo en tu perfil de Facebook.';
$lang['delete_profile'] = 'Borrar perfil'; // Text in title bar on pop-up dialog
$lang['delete_oauth2_profile1'] = '¿Desea borrar su cuenta en este servidor incluyendo todos tus datos en este sitio?';
$lang['delete_google_profile2'] = 'Nota: Esto no cambiará tu cuenta en Google, pero removerá Bible Online Learner de tu lista de aplicaciones aprovadas.';
$lang['delete_facebook_profile2'] = 'Nota: Esto no cambiará tu cuenta en Facebook, ni removerá Bible Online Learner de tu lista de aplicaciones aprovadas.';
$lang['delete_profile_button'] = 'Eliminar el perfil';

$lang['edit_user_profile'] = 'Editar perfil de usuario';
$lang['click_to_delete_profile'] = 'Haga click en este botón para eliminar tu perfil:';
$lang['delete_profile_confirm'] = '¿Desea borrar su cuenta incluyendo todos tus datos en este sitio?';

$lang['pw_min_length'] = 'El campo de contraseña, si es especificado, debe tener al menos %d caracteres en longitud.'; 

$lang['user_list'] = 'Lista de Usuario';
$lang['configure_your_users'] = 'Configurar tus usuarios';
$lang['administrator'] = 'Sysadmin';
$lang['teacher'] = 'Facilitador';
$lang['last_login'] = 'Última sesión iniciada';
$lang['never'] = 'Nunca';
$lang['user_operations'] = 'Operaciones';

$lang['assign_to_class'] = 'Asignar a clase';
$lang['user_edit'] = 'Editar';
$lang['user_delete'] = 'Borrar';

$lang['delete_user'] = 'Borrar usuario'; // Confirmation dialog title
$lang['delete_user_confirm'] = '¿Quieres borrar usuario %s incluyendo todo sus datos en este sitio?';
$lang['add_user'] = 'Adicionar nuevo usuario';

$lang['edit_user_information'] = 'Editar información de usuario';
$lang['specify_user_information'] = 'Especificar información de usuario';
$lang['this_user_google'] = 'Este usuario inicia sesión con Google';
$lang['this_user_facebook'] = 'Este usuario inicia sesión con Facebook';

$lang['user_name_used'] = 'El nombre de usuario "%s" ya está en uso';

$lang['account_created_subject']  = 'Cuenta Bible Online Learner creada';
$lang['account_created_message1'] = "Querido/a %s,\n\n"  // First name, last name
                                    . "Usted tiene una cuenta en el sitio web Bible Online Learner.\n\n"
                                    . "Tu nombre de usuario es: %s\n" // Username
                                    . "Tu contraseña es: %s\n\n"; // Password
$lang['account_created_message2'] = "Se le ha concedido privilegios de administrador.\n\n";
$lang['account_created_message2t'] = "Se le ha concedido privilegios de facilitador.\n\n";
$lang['account_created_message3'] = "Por favor de visitar %s, e inicie sesión;\n" // Site URL
                                    . "luego cambie su contraseña seleccionando 'Perfil' del menú 'Mi información'.\n";

$lang['account_you_created_message1'] = "Querido/a %s,\n\n"  // First name, last name
                                    . "Usted ha creado una cuenta en el sitio web de Bible Online Learner.\n\n"
                                    . "Tu nombre de usuario es: %s\n" // Username
                                    . "Tu contraseña es: %s\n\n"; // Password
$lang['account_you_created_message3'] = "Por favor visita %s, e inicie sesión;\n" // Site URL
                                    . "luego cambie su contraseña sleccionando 'Perfil' del menú 'Mi información'.\n\n"
                                    . "Si usted no inicia sesión dentro de 48 horas, su cuenta será eliminada.\n\n"
                                    . "Si usted no ha creado una cuenta, puede ignorar este mensaje.\n";

$lang['you_created_account'] = "Una nueva cuenta ha sido creada";
$lang['password_sent']       = 'Un correo electrónico conteniendo su nombre de usuario y contraseña ha sido enviado a %s'; // Email address

$lang['user_profile_deleted'] = 'Perfil de Usuario Eliminado'; // Text in title bar
$lang['your_account_deleted'] = 'Su cuenta en este servidor ha sido eliminada';
$lang['go_to_home'] = 'Ir a página de inicio';

$lang['google_no_response_delete'] = 'Google no respondió a este pedido. Típicamente esto pasa si usted inicio sesión aquí hace más de una hora. Trate de iniciar sesión aquí una vez más y luego borre su perfil.';

$lang['cannot_delete_self'] = 'No se puede eliminar a sí mismo';

$lang['expiry_warning_subject'] = 'Cuenta inactiva en Bible Online Learner';
$lang['expiry_warning_1_message'] = "Querido/a %s,\n\n"  // First name, last name
                                  . "Usted no ha usado su cuenta en %s por nueve meses.\n" // Site URL
                                  . "En otros nueve meses, tu cuenta será eliminada.\n\n";
$lang['expiry_warning_2_message'] = "Querido/a %s,\n\n"  // First name, last name
                                  . "Usted no ha usado su cuenta en %s por 17 meses.\n" // Site URL
                                  . "En un mes más, su cuenta seráa eliminada.\n\n";

$lang['expiry_warning_message_local'] = "Si desea mantener su cuenta, simplemente vaya al sitio web mencionado arriba\n"
                                         . "e inicie sesión usando el nombre de usuario '%s'."; // Username
$lang['expiry_warning_message_google'] = "Si desea mantener su cuenta, simplemente vaya al sitio web mencionado arriba\n"
                                          . "e inicie sesión usando su cuenta de Google.";
$lang['expiry_warning_message_facebook'] = "Si desea mantener su cuenta, simplemente vaya al sitio web mencionado arriba\n"
                                            . "e inicie sesión usando su cuenta de Facebook.";


$lang['only_admin_delete'] = 'Sólo sysadmins puede eliminar facilitadores y sysadmins';