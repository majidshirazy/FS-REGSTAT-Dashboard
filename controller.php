<?php
session_start();

require_once "config/initial.php";
require_once "config/db.php";


if (isset($_POST['loginform'])){
    $posteduser = $_POST['username'];
    login($posteduser);  
} elseif (isset($_POST['registerform'])) {
    $posteduser = $_POST['username'];
    $posteduser = $_POST['password'];
    $posteduser = $_POST['email'];
    register($posteduser,$postedpass,$postedemail);
} elseif (isset($_POST["add_new_user"])){
    $posteduser = $_POST['new_username'];
    $postedpass = $_POST['new_password'];
    $postedemail = $_POST['new_email'];
    $role = $_POST['new_role'];
    add_new_user($posteduser,$postedpass,$postedemail,$role);
} elseif (isset($_POST['profile_update'])){
    profile_update($posteduser,$postedpass,$postedemail);
} elseif (isset($_POST['admin_update'])){
    $posteduser = $_POST['username'];
    $postedpass = $_POST['password'];
    $postedemail = $_POST['email'];
    $role = $_POST['role'];
    admin_update($posteduser,$postedpass,$postedemail,$role);
} elseif (isset($_POST['delete_user'])){
    $username = $_POST['username'];
    $role = $_POST['role'];
    delete_user($username,$role);
} elseif (isset($_POST['start_pcap'])) {
    $packet_type = $_POST["sip_packet_type"];
    $sip_number = $_POST['sip_number'];
    $sip_port = $_POST['sip_port'];
    $sip_other_options = $_POST['sip_other_options'];
    exec("sudo systemctl is-active tcpdump.service",$result);
    if ($result[0] == "active"){
        shell_exec("sudo systemctl stop tcpdump.service");
        start_pcap($packet_type,$sip_number,$sip_port,$sip_other_options);
    } else {
        start_pcap($packet_type,$sip_number,$sip_port,$sip_other_options);
    }
} elseif (isset($_POST['stop_pcap'])) {
    stop_pcap();
}


function userexist($posteduser){
    $sql="SELECT id,username FROM user WHERE username = '$posteduser'";
    $results = mysqli_query($db, $sql);
    if (mysqli_num_rows($results) > 0) {
      echo "User is taken";	
    }else{
      echo 'user is not taken';
    }
    exit();
    }

function register($posteduser,$postedpass,$postedemail){
    global $db;
    $sql = "INSERT INTO user (`username`, `password`,`email`) VALUES ('$posteduser','$postedpass','$postedemail')";
    if ($db->query($sql) === TRUE) {
        session_regenerate_id();
        //$_SESSION['loggedin'] = FALSE;
        $_SESSION['name'] = $_POST['username'];
        $_SESSION['id'] = $id;
        header('Location: index.php');
      } else {
        echo "Error: " . $sql . "<br>" . $db->error;
      }
      $db->close();
}


function add_new_user($posteduser,$postedpass,$postedemail,$role){
    global $db;
    $sql = "INSERT INTO user (`username`,`password`,`email`,`role`) VALUES ('$posteduser','$postedpass','$postedemail','$role')";
    if ($db->query($sql) === TRUE) {
        session_regenerate_id();
        //$_SESSION['loggedin'] = FALSE;
        // $_SESSION['name'] = $_POST['username'];
        // $_SESSION['id'] = $id;
        header('Location: admin.php');
      } else {
        echo "Error: " . $sql . "<br>" . $db->error;
      }
      $db->close();
}


function login($posteduser) {
    global $db;
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
    }
    if ($query = $db->prepare('SELECT `id`,`password`,`role` FROM `user` WHERE `username` = ?')) {
        $query->bind_param('s', $posteduser);
        $query->execute();
        $query->store_result();

        if ($query->num_rows > 0) {
            $query->bind_result($id, $password,$role);
            $query->fetch();
            // echo "id is: ".$id." And the password is: ".$password;
            if ($_POST['password'] === $password) {
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;
                $_SESSION["ses_time"] = time();
                if ($role === "admin"){
                    $_SESSION["role"] = "ADMIN";
                    header('Location: admin.php');
                } else {
                    header('Location: profile.php');
                }
            } else {
                echo 'Incorrect username and/or password!';
            }
        } else {
            echo 'Incorrect username and/or password! <br />';
            echo "For Register click link below<br />";
            echo "<a href='register.php'>register</a>";
        }
            $db->close();
    }
}

function delete_user($username,$role){
    global $db;
    $sql = "DELETE FROM `user` WHERE username='$username'";
    if ($role != "admin"){
        if ($db->query($sql)) {
        } else {
            echo "Error updating record: " . $db->error;
        }
    } else {
        echo "You Can Not Delete Admin Users.";
    }
    $db->close();
    header('Location: admin.php');
}


function admin_update($posteduser,$postedpass,$postedemail,$role){
    global $db;
    $sql = "UPDATE `user` SET `password`='$postedpass',`email`='$postedemail',`role`='$role' WHERE username='$posteduser'";
    if ($db->query($sql)) {
        header('Location: admin.php');
    } else {
        echo "Error updating record: " . $db->error;
    }
    $db->close();
}


function profile_update($posteduser,$postedpass,$postedemail){
    global $db;
    if ($_SESSION['name']){
        $posteduser = $_SESSION['name'];
    } else {
        $posteduser = $_POST['username'];
    }
    $postedpass = $_POST['password'];
    $postedemail = $_POST['email'];
    $sql = "UPDATE `user` SET `password`='$postedpass',`email`='$postedemail' WHERE username='$posteduser'";
    if ($db->query($sql)) {
        header('Location: profile.php');
    } else {
        echo "Error updating record: " . $db->error;
    }
    $db->close();
}


function start_pcap ($packet_type,$sip_number,$sip_port,$sip_other_options) {
    $pcap_option_file = fopen("pcap/dump_options", "w");
    if ($packet_type == "invite"){
        $packet_type = "invite|progress|setup|ok|cancel|bye|ack|auth|try|sdp";
    } elseif ($packet_type == "register"){
        $packet_type = "register|ok|cancel|ack|auth|try";
    }
    if ($packet_type != "all"){
        if (empty($sip_number)){
            $REGEX = "REGEX=\"$packet_type\"\n";
        } else {
            $REGEX = "REGEX=\"($packet_type)(?=.*$sip_number)\"\n";
        }
    } else {
        if (empty($sip_number)){
            $REGEX = "\n";
        } else {
            $REGEX = "REGEX=\"$sip_number\"\n";
        }
    }
    
    if(empty($sip_port) && empty($sip_other_options)) {
        $PORT_AND_OPTIONS = "PORT_AND_OPTIONS=\"udp port 5060\"\n";
    }elseif (!empty($sip_port) && empty($sip_other_options)) {
        $PORT_AND_OPTIONS = "PORT_AND_OPTIONS=\"udp port $sip_port\"\n";
    } elseif (empty($sip_port) && !empty($sip_other_options)){
        $PORT_AND_OPTIONS = "PORT_AND_OPTIONS=\"$sip_other_options port 5060\"\n";
    } else {
        $PORT_AND_OPTIONS = "PORT_AND_OPTIONS=\"$sip_other_options port $sip_port\"\n";
    }
    fwrite($pcap_option_file,$REGEX);
    fwrite($pcap_option_file,$PORT_AND_OPTIONS);
    fclose($pcap_option_file);
    shell_exec("sudo systemctl start tcpdump.service");
    header('Location: utility.php');
}

function stop_pcap () {
    shell_exec("sudo systemctl stop tcpdump.service");
    header('Location: utility.php');
}