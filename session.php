<?php

    class session_CLASS{

        private function sessionSTART($name="jam", $lifetime=10800, $path='/', $domain =null, $secure=1, $http_only=1){

            if (empty($_SESSION)) {
                session_name($name ."_session");

                $domain = $_SERVER['SERVER_NAME'];
                session_set_cookie_params($lifetime, $path = '/', $domain, $secure, $http_only);
                session_start();
            }
            
        }

        private function secure_SESSION(){
            ini_set('session.use_only_cookies', true);
            ini_set('session.use_trans_sid', false);
            ini_set('session.user_strict_mode', true);
            ini_set('session.cookie_httponly', true);
            ini_set('session.cookie_secure', true);
            ini_set('session.gc_maxlifetime', 3600);
        }

        private function set_SESSION_HIJACKING(){
            if (!isset($_SESSION['IPaddress']) || !isset($_SESSION['User_agent'])) {
                return false;
            }
            
            if ($_SESSION['IPaddress'] != $_SERVER['REMOTER_ADDR']) {
                return false;
            }

            if ($_SESSION['User_agent'] != $_SERVER['HTTP_USER_AGENT']) {
                return false;
            }

            return true;
        }

        private function get_SESSION_HIJACKING(){
            if (!$this->set_SESSION_HIJACKING()) {
                $this->session_RegenerateID();
                $_SESSION['IPaddress'] = $_SERVER['REMOTER_ADDR'];
                $_SESSION['User_agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
        }

        private function session_RegenerateID(){
            session_regenerate_id(true);
        }

        private function session_Lifetime_Handler(){
            $now = time();
            if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
                $this->session_RegenerateID();
            }else {
                $_SESSION['discard_after'] = $now + 3600;
            }
            
        }

        public function execute_Session_Code(){
            // ini settings
            $this->secure_SESSION();

            // start custome session
            $this->sessionSTART();

            // check session hijacking
            $this->get_SESSION_HIJACKING();

            // check session life span
            $this->session_Lifetime_Handler();
        }

    }
?>