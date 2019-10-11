<html>

    <?php

    session_start();

    try {
        $bdd = new PDO('mysql:host=localhost;dbname=NicoEats;charset=utf8', 'root', '');
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    if ((!isset($_GET['nav']))||(empty($_GET['nav'])))
    {
        header('Location: NicoEats.php?nav=index');
        exit();
    }

    switch ($_GET['nav']) {
        case 'register_post':

            if ((isset($_POST['firstname']))&&(isset($_POST['name']))&&(isset($_POST['login']))&&(isset($_POST['password']))&&(isset($_POST['password_repeat']))){

                if (empty($_POST['firstname'])){
                    $error_register = 1;
                } else {
                    $error_register = 0;
                }

                if (empty($_POST['name'])){
                    $error_register .= 1;
                } else {
                    $error_register .= 0;
                }

                if (empty($_POST['login'])){
                    $error_register .= 1;
                } else {
                    $error_register .= 0;
                }

                if (empty($_POST['password'])){
                    $error_register .= 1;
                } else {
                    $error_register .= 0;
                }

                if (empty($_POST['password_repeat'])){
                    $error_register .= 1;
                } else {
                    $error_register .= 0;
                }

                if (($_POST['password']) == ($_POST['password_repeat'])) {
                    $error_register .= 0;
                } else {
                    $error_register .= 1;
                }

                $req_login = $bdd->prepare('SELECT * FROM `user` WHERE login=?');
                $req_login->execute(array($_POST['login']));
                $nblogin = $req_login->rowCount();

                if($nblogin == 0){
                    $error_register .= 0;
                } else {
                    $error_register .= 1;
                }

                if($error_register == 00000000){

                    $req_newAccount = $bdd->prepare('INSERT INTO `user` (`firstname`, `name`, `login`, `password`) VALUES ( ?, ?, ?, ?)');
                    $req_newAccount->execute(array((ucfirst(strtolower($_POST['firstname']))), (ucfirst(strtolower($_POST['name']))),(strtolower($_POST['login'])),(sha1('vUP6Kh9FBsCuA7kjj4gQ' . $_POST['password']))));

                    header('Location: NicoEats.php?nav=login&user='.strtolower($_POST['login']));
                    exit();

                } else {

                    header('Location: NicoEats.php?nav=register&error='.$error_register.'&firstname='.ucfirst(strtolower($_POST['firstname'])).'&name='.ucfirst(strtolower($_POST['name'])).'&login='.strtolower($_POST['login']));
                    exit();

                }

            } else {

                header('Location: NicoEats.php?nav=register');
                exit();

            }

            break;
        case 'login_post':

            if ((isset($_POST['login']))&&(isset($_POST['password']))){

                $req_login = $bdd->prepare('SELECT id FROM user WHERE login = :login AND password = :password');
                $req_login->execute(array('login' => strtolower($_POST['login']), 'password' => sha1('vUP6Kh9FBsCuA7kjj4gQ' . $_POST['password'])));
                $resultat_login = $req_login->fetch();
                $nb_resultat_login = $req_login->rowCount();

                if($nb_resultat_login == 1){

                    $req_session = $bdd->prepare('SELECT * FROM user WHERE id = ?');
                    $req_session->execute(array($resultat_login['id']));
                    $resultat_session = $req_session->fetch();

                    session_start();
                    $_SESSION['id'] = $resultat_session['id'];
                    $_SESSION['login'] = $resultat_session['login'];
                    $_SESSION['name'] = $resultat_session['name'];
                    $_SESSION['firstname'] = $resultat_session['firstname'];
                    $_SESSION['avatar_extension'] = $resultat_session['avatar_extension'];

                    header('Location: NicoEats.php?nav=index');
                    exit();

                } else {

                    header('Location: NicoEats.php?nav=login&error=1&user='.strtolower($_POST['login']));
                    exit();

                }

            } else {

                header('Location: NicoEats.php?nav=login');
                exit();

            }

            break;

        case 'profil_post':

            if (((isset($_POST['name']))||(isset($_POST['firstname']))||(isset($_POST['login'])))&&(isset($_SESSION['id']))) {

                if(isset($_POST['name'])) {

                    $req_profil_name = $bdd->prepare('UPDATE `user` SET `name`= :name WHERE `id`= :id');
                    $req_profil_name->execute(array('name' => ucfirst(strtolower($_POST['name'])), 'id' => $_SESSION['id']));
                    $_SESSION['name'] = ucfirst(strtolower($_POST['name']));

                }

                if(isset($_POST['firstname'])) {

                    $req_profil_firstname = $bdd->prepare('UPDATE `user` SET `firstname`= :firstname WHERE `id`=:id');
                    $req_profil_firstname->execute(array('firstname' => ucfirst(strtolower($_POST['firstname'])), 'id' => $_SESSION['id']));
                    $_SESSION['firstname'] = ucfirst(strtolower($_POST['firstname']));

                }

                if(isset($_POST['login'])) {

                    $req_profil_login = $bdd->prepare('UPDATE `user` SET `login`= :login WHERE `id`=:id');
                    $req_profil_login->execute(array('login' => strtolower($_POST['login']), 'id' => $_SESSION['id']));
                    $_SESSION['login'] = strtolower($_POST['login']);

                }

            }

            header('Location: NicoEats.php?nav=profil');
            exit();
            break;

        case 'session':

            session_destroy();

            header('Location: NicoEats.php?nav=login');
            exit();

            break;

        case 'password_post':

            if ((isset($_POST['password_old']))&&(isset($_POST['password_new']))&&(isset($_POST['password_new_repeat']))&&(isset($_SESSION['id']))){

                $req_password = $bdd->prepare('SELECT password FROM user WHERE id = ?');
                $req_password->execute(array($_SESSION['id']));
                $resultat_password = $req_password->fetch();

                if ($resultat_password['password'] == sha1('vUP6Kh9FBsCuA7kjj4gQ' . $_POST['password_old'])) {
                    $error_password = 0;
                } else {
                    $error_password = 1;
                }

                if ($_POST['password_new'] == $_POST['password_new_repeat']) {
                    $error_password .= 0;
                } else {
                    $error_password .= 1;
                }

                if ($error_password == 00) {

                    $req_password_update = $bdd->prepare('UPDATE `user` SET `password`= ? WHERE `id`= ?');
                    $req_password_update->execute(array(sha1('vUP6Kh9FBsCuA7kjj4gQ' . $_POST['password_new']), $_SESSION['id']));

                    header('Location: NicoEats.php?nav=profil');
                    exit();

                }

                header('Location: NicoEats.php?nav=password&error='.$error_password);
                exit();
            }

            header('Location: NicoEats.php?nav=password');
            exit();

        break;

        case 'avatar':
            if(isset($_FILES['avatar']))
            {

                $extension_avaible = array('png', 'gif', 'jpg', 'jpeg');
                $extension_upload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'),1));

                echo ($extension_upload);

                if(in_array($extension_upload, $extension_avaible))
                {

                    $taille = filesize($_FILES['avatar']['tmp_name']);

                    if($taille < 1048576*20){

                        if(move_uploaded_file($_FILES['avatar']['tmp_name'], "Image/upload/avatar/".$_SESSION['id'].".".$extension_upload))
                        {
                            $req_avatar_update = $bdd->prepare('UPDATE `user` SET `avatar_extension`= ? WHERE `id`= ?');
                            $req_avatar_update->execute(array($extension_upload, $_SESSION['id']));

                            $_SESSION['avatar_extension'] = $extension_upload;

                            header('Location: NicoEats.php?nav=profil');
                            exit();
                        }
                        else
                        {
                            header('Location: NicoEats.php?nav=profil&error=1');
                            exit();
                        }
                    }
                }
            }
            break;

        case 'order':

            if (isset($_SESSION['id'])) {

                if (!isset($_GET['id'])||empty($_GET['id'])) {

                    $req_order_existing = $bdd->prepare('SELECT id FROM `order` WHERE id_user = ?');
                    $req_order_existing->execute(array($_SESSION['id']));
                    $resultat_order_existing_nb = $req_order_existing->rowCount();
                    $resultat_order_existing_id = $req_order_existing->fetch();

                    if ($resultat_order_existing_nb == 0) {

                        $req_order_neworder = $bdd->prepare('INSERT INTO `order` (`id_user`) VALUES (?)');
                        $req_order_neworder->execute(array($_SESSION['id']));

                        $req_order_id = $bdd->prepare('SELECT id FROM `order` WHERE id_user = ?');
                        $req_order_id->execute(array($_SESSION['id']));
                        $resultat_order_id = $req_order_id->fetch();

                        if (isset($_GET['product'])) {

                            header('Location: NicoEats.php?nav=order&id='.$resultat_order_id['id'].'&product=' . $_GET['product']);
                            exit();

                        } else {

                            header('Location: NicoEats.php?nav=order&id='.$resultat_order_id['id']);
                            exit();
                        }

                    } else {

                        header('Location: NicoEats.php?nav=order&id='.$resultat_order_existing_id['id']);
                        exit();
                    }

                } else {

                    $req_order_checkid = $bdd->prepare('SELECT * FROM `order` WHERE id = ? ');
                    $req_order_checkid->execute(array($_GET['id']));
                    $resultat_order_checkid = $req_order_checkid->fetch();

                    if ($resultat_order_checkid['id_user'] == $_SESSION['id']){

                        $req_order_step = $bdd->prepare('SELECT step FROM `order` WHERE id = ? ');
                        $req_order_step->execute(array($_GET['id']));
                        $resultat_order_step = $req_order_step->fetch();

                        switch ($resultat_order_step['step']){
                            case '1':
                                if (isset($_GET['product'])) {
                                    header('Location: NicoEats.php?nav=adress&id=' . $resultat_order_checkid['id'].'&product='.$_GET['product']);
                                    exit();
                                } else {
                                    header('Location: NicoEats.php?nav=adress&id=' . $resultat_order_checkid['id']);
                                    exit();
                                }
                                break;

                            case '2':
                                if (isset($_GET['product'])) {
                                    header('Location: NicoEats.php?nav=command&id=' . $resultat_order_checkid['id'].'&product='.$_GET['product']);
                                    exit();
                                } else {
                                    header('Location: NicoEats.php?nav=command&id=' . $resultat_order_checkid['id']);
                                    exit();
                                }
                                break;
                        }
                    }
                }

            } else {
                header('Location: NicoEats.php?nav=login');
                exit();
            }


            break;

        case 'adress_add_post':

            if (!isset($_SESSION['id'])) {
                header('Location: NicoEats.php?nav=login');
                exit();
            }

            $req_order_checkid = $bdd->prepare('SELECT * FROM `order` WHERE id = ? ');
            $req_order_checkid->execute(array($_GET['id']));
            $resultat_order_checkid = $req_order_checkid->fetch();

            if ($resultat_order_checkid['id_user'] != $_SESSION['id']){
                header('Location: NicoEats.php?nav=order');
                exit();
            }

            if ((isset($_POST['name']))&&(isset($_POST['street_number']))&&(isset($_POST['road']))&&(isset($_POST['city']))&&(isset($_POST['administrative_area']))&&(isset($_POST['postal_code']))&&(isset($_POST['country']))&&(isset($_POST['comment']))) {

                if ((empty($_POST['name']))||(empty($_POST['street_number']))||(empty($_POST['road']))||(empty($_POST['city']))||(empty($_POST['administrative_area']))||(empty($_POST['postal_code']))||(empty($_POST['country']))) {

                    header('Location: NicoEats.php?nav=adress_add&id='.$_POST['id'].'&error=1&comment='.$_POST['comment'].'&name='.$_POST['name']);
                    exit();

                } else {

                    $req_newAdress = $bdd->prepare('INSERT INTO `adress` (`id_user`, `name`, `street_number`, `road`, `city`, `administrative_area`, `postal_code`, `country`, `comment` ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $req_newAdress->execute(array(($_SESSION['id']), ($_POST['name']), ($_POST['street_number']), ($_POST['road']), ($_POST['city']), ($_POST['administrative_area']), ($_POST['postal_code']), ($_POST['country']), ($_POST['comment'])));

                    header('Location: NicoEats.php?nav=adress&id='.$_POST['id']);
                    exit();
                }

            }

            header('Location: NicoEats.php?nav=adress_add');
            exit();


        case 'adress_post':

            if (!isset($_SESSION['id'])) {
                header('Location: NicoEats.php?nav=login');
                exit();
            }

            $req_order_checkid = $bdd->prepare('SELECT * FROM `order` WHERE id = ? ');
            $req_order_checkid->execute(array($_GET['id']));
            $resultat_order_checkid = $req_order_checkid->fetch();

            if ($resultat_order_checkid['id_user'] != $_SESSION['id']){
                header('Location: NicoEats.php?nav=order');
                exit();
            }

            if (isset($_POST['adress'])) {

                $req_adress_update = $bdd->prepare('UPDATE `order` SET `adress`= ? WHERE `id`= ?');
                $req_adress_update->execute(array($_POST['adress'], $_GET['id']));

                $req_order_step = $bdd->prepare('SELECT step FROM `order` WHERE id = ? ');
                $req_order_step->execute(array($_GET['id']));
                $resultat_order_step = $req_order_step->fetch();

                if ( $resultat_order_step['step'] < 2 ) {
                    $req_step_update = $bdd->prepare('UPDATE `order` SET `step`= ? WHERE `id`= ?');
                    $req_step_update->execute(array(2, $_GET['id']));
                }

            }

            header('Location: NicoEats.php?nav=order');
            exit();

            break;
    }

    ?>

    <head>
        <title>NicoEats.com</title>
        <link rel="stylesheet" href="NicoEats.css" />

        <link href="Image/icon.ico" rel="shortcut icon" type="image/x-icon" />
    </head>

    <body>

        <?php if (isset($_SESSION['avatar_extension'])&&($_SESSION['avatar_extension'] != NULL)){ ?>
            <style>
                header {
                    padding-top: 18px;
                    padding-bottom: 38px;}
                header img {
                    padding: 10px 0;}
                header #profil {
                    height: 44px;}
                header #profil img {
                    border-radius: 22px;
                    height: 44px;
                    width: 44px;
                    padding: 0;}
                header #profil p {
                    margin-top: 12px;
                    margin-left: 10px;}
            </style>
        <?php } ?>

        <header>
            <a href="NicoEats.php">
                <img src="Image/title.png">
            </a>
            <div id="profil">

                <?php if(isset($_SESSION['id'])){ ?>
                    <a href="NicoEats.php?nav=profil">
                        <?php if (isset($_SESSION['avatar_extension'])&&($_SESSION['avatar_extension'] != NULL)){ ?>
                            <img src="Image/upload/avatar/<?php echo $_SESSION['id'].".".$_SESSION['avatar_extension']; ?>">
                        <?php } else { ?>
                            <img src="Image/profil.png">
                        <?php } ?>
                        <p><?php echo $_SESSION['name']; ?></p>
                    </a>
                <?php } else { ?>
                    <a href="NicoEats.php?nav=login">
                        <img src="Image/profil.png">
                        <p><?php echo 'Connexion'; ?></p>
                    </a>
                <?php } ?>

            </div>
        </header>

        <?php
        switch ($_GET['nav']) {
            case 'index':
        ?>

        <section id="default">
            <img id="order_img" src="Image/orange_juice_gif.gif">
            <div id="order">
                <a href="NicoEats.php?nav=order">
                    <p>
                        Commander en cliquant ici
                        <img src="Image/order_button.png">
                    </p>
                </a>
            </div>
        </section>

        <?php
            break;
            case 'login':
        ?>

        <section id="login">
            <h1>Connectez-vous à <div>Nico<span>Eats</span></div></h1>
            <form method="post" action="NicoEats.php?nav=login_post">
                <p class="field">
                    <img src="Image/profil.png">
                    <input type="text" name="login" <?php if(isset($_GET['user'])){ echo('value='.$_GET['user']); } ?> placeholder="Identifiant"/>
                </p>
                <p class="field" <?php if(isset($_GET['error']) && $_GET['error'] == 1){ echo('id="field_error"'); } ?> >
                    <img src="Image/key.png">
                    <input type="password" name="password" placeholder="Mot de passe"/>
                </p>
                <input type="submit" />
            </form>
            <p>Pas encore incris ? <a href="NicoEats.php?nav=register">Créez un compte</a></p>
        </section>

        <?php
            break;
            case 'register':

                if (isset($_GET['error'])) {
                    $error_register_array = str_split($_GET['error']);
                }
                ?>

                <section id="register">
                    <?php if (isset($_GET['error'])) { ?>
                        <p id="error_alert">
                            <img src="Image/error_alert.png">
                            <?php
                            if($error_register_array[0] == 1 || $error_register_array[1] == 1 || $error_register_array[2] == 1 || $error_register_array[3] == 1 || $error_register_array[4] == 1){
                                echo('<span>Champ(s) vide(s)!</span> ');
                            }
                            if($error_register_array[5] == 1){
                                echo('<span>Les mots de passe ne sont pas identique!</span> ');
                            }
                            if($error_register_array[6] == 1){
                                echo('<span>Identifiant déjà utilisé!</span>');
                            }
                            ?>
                        </p>
                    <?php } ?>
                    <h1>Créez votre compte
                        <div>Nico<span>Eats</span></div>
                    </h1>
                    <form method="post" action="NicoEats.php?nav=register_post">
                        <p class="field" <?php if(isset($error_register_array) && $error_register_array[0] == 1){ echo('id="field_error"'); } ?> >
                            <img src="Image/name.png">
                            <input type="text" name="firstname" placeholder="Nom" <?php if(isset($_GET['firstname'])){ echo('value="'.$_GET['firstname'].'"'); } ?> />
                        </p>
                        <p class="field" <?php if(isset($error_register_array) && $error_register_array[1] == 1){ echo('id="field_error"'); } ?> >
                            <img src="Image/name.png">
                            <input type="text" name="name" placeholder="Prénom" <?php if(isset($_GET['name'])){ echo('value="'.$_GET['name'].'"'); } ?> />
                        </p>
                        <p class="field" <?php if(isset($error_register_array) && ($error_register_array[2] == 1 || $error_register_array[6] == 1)){ echo('id="field_error"'); } ?> >
                            <img src="Image/profil.png">
                            <input type="text" name="login" placeholder="Identifiant" <?php if(isset($_GET['login'])){ echo('value="'.$_GET['login'].'"'); } ?> />
                        </p>
                        <p class="field" <?php if(isset($error_register_array) && ($error_register_array[3] == 1 || $error_register_array[5] == 1)){ echo('id="field_error"'); } ?> >
                            <img src="Image/key.png">
                            <input type="password" name="password" placeholder="Mot de passe"/>
                        </p>
                        <p class="field" <?php if(isset($error_register_array) && ($error_register_array[4] == 1 || $error_register_array[5] == 1)){ echo('id="field_error"'); } ?> >
                            <img src="Image/key.png">
                            <input type="password" name="password_repeat" placeholder="Confirmez le mot de passe"/>
                        </p>
                        <input type="submit"/>
                    </form>
                    <p>Déjà incris ? <a href="NicoEats.php?nav=login">Connectez-vous</a></p>
                </section>


                <?php
                break;
            case 'profil':

                if (!isset($_SESSION['id'])){
                    header('Location: NicoEats.php?nav=login');
                    exit();
                }

                if (isset($_SESSION['avatar_extension'])&&($_SESSION['avatar_extension'] != NULL)){ ?>
                    <style>
                        #profil #title label .img_down {
                            background-image: url("Image/upload/avatar/<?php echo $_SESSION['id'].".".$_SESSION['avatar_extension']; ?>");
                            border-radius: 37px;
                        }
                        #profil #title label .img_up:hover {
                            background-image: url("Image/profil_edit.png");
                        }
                    </style>
                <?php } else { ?>
                    <style>
                        #profil #title label .img_down {
                            background-image: url("Image/profil.png");
                        }
                        #profil #title label .img_up:hover {
                            background-image: url("Image/profil_add.png");
                        }
                    </style>
                <?php } ?>

                <section id="profil">
                    <div id="title">
                        <form method="post" action="NicoEats.php?nav=avatar" enctype="multipart/form-data">
                            <label type="file" for="img_input" <?php  if (isset($_SESSION['avatar_extension'])&&($_SESSION['avatar_extension'] != NULL)){ ?> title="Modifiez votre photo de profile" <?php } else { ?> title="Ajoutez une photo de profile" <?php } ?> ><div class="img_down" ><div class="img_up"></div></div></label>
                            <input type="file" id="img_input" onchange="form.submit();" name="avatar"/>
                        </form>
                        <h1>Information du compte</h1>
                    </div>
                    <form method="post" action="NicoEats.php?nav=profil_post">
                        <table>
                            <tr>
                                <td class="label"><img src="Image/name.png" />Nom</td>
                                <td class="data">
                                    <?php
                                        if ((isset($_GET['modif']))&&($_GET['modif']==1)){
                                            ?><input type="text" id="ahahaha" name="firstname" value="<?php echo $_SESSION['firstname']; ?>" autofocus onfocus="this.setSelectionRange(this.value.length,this.value.length);"/><?php
                                        } else {
                                            ?><a href="NicoEats.php?nav=profil&modif=1" title="Cliquez pour modifier"><?php echo $_SESSION['firstname']; ?></a><?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label"><img src="Image/name.png" />Prenom</td>
                                <td class="data">
                                    <?php
                                        if ((isset($_GET['modif']))&&($_GET['modif']==2)){
                                            ?><input autofocus type="text" id="input" name="name" value="<?php echo $_SESSION['name']; ?>" autofocus onfocus="this.setSelectionRange(this.value.length,this.value.length);"/><?php
                                        } else {
                                            ?><a href="NicoEats.php?nav=profil&modif=2" title="Cliquez pour modifier"><?php echo $_SESSION['name']; ?></a><?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label"><img src="Image/profil.png" />Pseudo</td>
                                <td class="data">
                                    <?php
                                        if ((isset($_GET['modif']))&&($_GET['modif']==3)){
                                            ?><input autofocus type="text" id="input" name="login" value="<?php echo $_SESSION['login']; ?>" autofocus onfocus="this.setSelectionRange(this.value.length,this.value.length);"/><?php
                                        } else {
                                            ?><a href="NicoEats.php?nav=profil&modif=3" title="Cliquez pour modifier"><?php echo $_SESSION['login']; ?></a><?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr id="tr_end">
                                <td class="label" id="td_end"></td>
                                <td class="data" id="td_end"></td>
                            </tr>
                        </table>
                    </form>
                    <div id="password">
                        <p>Vous souhaitez modifier votre mots de passe, <a href="NicoEats.php?nav=password">Cliquez ici</a></p>
                    </div>
                    <div id="logout">
                        <a href="NicoEats.php?nav=session">Deconnexion</a>
                    </div>
                </section>

                <?php

                break;

            case 'password':

                if (!isset($_SESSION['id'])){
                    header('Location: NicoEats.php?nav=login');
                    exit();
                }

                if (isset($_GET['error'])) {
                    $error_password_array = str_split($_GET['error']);
                }

                ?>

                <section id="password">
                    <h1>Changement de mot de passe</h1>
                    <?php if (isset($_GET['error'])) { ?>
                        <p id="error_alert">
                            <img src="Image/error_alert.png">
                            <span>Erreur, veuillez ressaisir les mots de passe</span>
                        </p>
                    <?php } ?>
                    <form action="NicoEats.php?nav=password_post" method="post">
                        <div>
                            <p>Ancien mot de passe:</p>
                            <input type="password" name="password_old" autofocus <?php if(isset($error_password_array) && $error_password_array[0] == 1){ echo('id="field_error"'); } ?>/>
                        </div>
                        <div>
                            <p>Nouveau mot de passe:</p>
                            <input type="password" name="password_new" <?php if(isset($error_password_array) && $error_password_array[1] == 1){ echo('id="field_error"'); } ?>/>
                        </div>
                        <div>
                            <p>Confirmez le mot de passe:</p>
                            <input type="password" name="password_new_repeat" <?php if(isset($error_password_array) && $error_password_array[1] == 1){ echo('id="field_error"'); } ?>/>
                        </div>
                        <input type="submit"/>
                    </form>
                </section>


                <?php

                break;

            case 'adress_add':

                if ((!isset($_SESSION['id']))) {
                    header('Location: NicoEats.php?nav=login');
                    exit();
                }

                if ((!isset($_GET['id']))) {
                    header('Location: NicoEats.php?nav=order');
                    exit();
                }

                ?>

                <section id="adress_add">
                    <?php if (isset($_GET['error'])) { ?>
                        <p id="error_alert">
                            <img src="Image/error_alert.png">
                            <span>Adresse incorrect ou mal saisie</span>
                        </p>
                    <?php } ?>
                    <h1>Saisissez une adresse de livraison</h1>
                    <div id="adress_form">
                        <form method="post" action="NicoEats.php?nav=adress_add_post">
                            <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
                            <fieldset>
                                <div>
                                    <input id="user_input_autocomplete_address" name="user_input_autocomplete_address" placeholder="Entrez l'adresse complète">
                                </div>
                            </fieldset>
                            <fieldset>
                                <div>
                                    <input name="name" placeholder="Entrez un nom" <?php if (isset($_GET['name'])) { ?> value="<?php echo $_GET['name']; ?>" <?php } ?>/>
                                </div>
                                <div>
                                    <label>Numéro de voie :</label>
                                    <input id="street_number" name="street_number" readonly="true" />
                                </div>
                                <div>
                                    <label>Rue :</label>
                                    <input id="route" name="road" readonly="true" />
                                </div>
                                <div>
                                    <label>Ville :</label>
                                    <input id="locality" name="city" readonly="true" />
                                </div>
                                <div>
                                    <label>Région :</label>
                                    <input id="administrative_area_level_1" name="administrative_area" readonly="true" />
                                </div>
                                <div>
                                    <label>Code postale :</label>
                                    <input id="postal_code" name="postal_code" readonly="true" />
                                </div>
                                <div>
                                    <label>Pays :</label>
                                    <input id="country" name="country" readonly="true" />
                                </div>
                                <div>
                                    <input id="comment" name="comment" placeholder="Indication/Commentaire" <?php if (isset($_GET['comment'])) { ?> value="<?php echo $_GET['comment']; ?>" <?php } ?>/>
                                </div>
                                <div id="div_submit">
                                    <input type="submit" />
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&amp;key=AIzaSyCS2TwD_xQ1AGOP9POwS3nLGWehhYh5OyA"></script>
                    <script type="text/javascript" src="autocomplete.js"></script>
                </section>

            <?php
                break;

            case 'adress':

                if ((!isset($_SESSION['id']))) {
                    header('Location: NicoEats.php?nav=login');
                    exit();
                }

                if ((!isset($_GET['id']))) {
                    header('Location: NicoEats.php?nav=order');
                    exit();
                }

                ?>

                <section id="adress">
                    <div id="order_step">
                        <div id="step_nav">
                            <p id="one"><a href="" id="here">Adresse</a></p>
                            <p id="two"><a href="">Date</a></p>
                            <p id="three"><a href="">Ma commande</a></p>
                            <p id="four"><a href="">Validation</a></p>
                            <p id="five"><a href="">Paiement</a></p>
                        </div>
                        <?php
                    $req_order_step = $bdd->prepare('SELECT step FROM `order` WHERE id = ? ');
                    $req_order_step->execute(array($_GET['id']));
                    $resultat_order_step = $req_order_step->fetch();
                    ?> <img src="Image/order_step_<?php echo $resultat_order_step['step']; ?>.png"> <?php
                    ?>
                    </div>

                    <h1>Selectionnez une adresse de livraison</h1>

                    <div id="adress_add_button">
                        <a href="NicoEats.php?nav=adress_add&id=<?php echo $_GET['id']; ?>">
                            <p>Ajoutez une adresse</p>
                            <img src="Image/add.png">
                        </a>

                    </div>

                    <div id="adress_list">
                        <form method="post" action="NicoEats.php?nav=adress_post">
                            <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                            <?php

                            $req_adress = $bdd->prepare('SELECT * FROM adress WHERE id_user = ?');
                            $req_adress->execute(array($_SESSION['id']));
                            $nb_req_adress = $req_adress->rowCount();
                            $adress_count = 0;

                            while ($resultat_adress = $req_adress->fetch()) { $adress_count++; ?>

                                <div class="adress_radio" <?php if ($adress_count == 1 && $adress_count ==  $nb_req_adress){ echo'id="firstandlast"'; } elseif ($adress_count == 1) { echo'id="first"';} elseif ($adress_count ==  $nb_req_adress) { echo'id="last"';} ?>>
                                    <input type="radio" title="<?php echo $resultat_adress['street_number'].' '.$resultat_adress['road'].', '.$resultat_adress['city']; ?>" value="<?php echo $resultat_adress['id']; ?>" name="adress_id" <?php if ($adress_count == 1) { echo'checked ';} ?> id="<?php echo $resultat_adress['id']; ?>">
                                    <label for="<?php echo $resultat_adress['id']; ?>" title="<?php echo $resultat_adress['street_number'].' '.$resultat_adress['road'].', '.$resultat_adress['city']; ?>"><?php echo $resultat_adress['name'];?></label>
                                </div>

                            <?php } ?>

                            <div id="div_submit">
                                <input type="submit" />
                            </div>
                        </form>
                    </div>
                </section>

        <?php
                break;

        }
        ?>

        <footer>

        </footer>
    </body>

</html>