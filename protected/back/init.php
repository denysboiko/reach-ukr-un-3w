<?php
	if(!isset($this_is_page) || $this_is_page != true) { exit(0); }

	include 'framework.php';
	include 'medoo.php';

	//*
	$db = new medoo([
		'database_type' => 'mssql'
		, 'database_name' => 'ukr_3w'
		, 'server' => '192.168.56.101'
		, 'username' => 'ukr_3w'
		, 'password' => 'ukr_3w'
		, 'port' => '1433'
		// , 'charset' => 'utf8'
	]);
	/*/
	$db = new medoo([
		'database_type' => 'mssql'
		, 'database_name' => 'web_ukr_framework'
		, 'server' => '188.184.78.26'
		, 'username' => 'ukr_framework'
		, 'password' => 'reach%123'
		, 'port' => '1433'
		// , 'charset' => 'utf8'
	]);
	//*/

	Framework::setDBConn($db);

	class User extends BaseUser {
		static function getAuthorize($email, $password) {
			$conn = Framework::getDBConn();
			$user = $conn->select('user', ['id', 'email', 'role'], [ 'AND' => ['email' => $email, 'password' => User::getPasswordHash($password)]]);
			$user = count($user) == 1 ? $user[0] : null;

			if(!$user) {
				return new Exception('Wrong email/password');
			} else if($user['role'] == 'disabled') {
				return new Exception('Account disabled');
			} else {
				return new User($user['id'], [ 'email' => $user['email'], 'role' => $user['role'] ]);
			}
		}
		static function getUser($user_id) {
			if($user_id === null) {
				return new User(null);
			} else {
				$conn = Framework::getDBConn();
				$user = $conn->select('user', ['id', 'email', 'role', 'notification_receiver'], ['id' => $user_id]);
				$user = count($user) == 1 ? $user[0] : null;
				if($user) {
					return new User($user['id'], [ 'email' => $user['email'], 'role' => $user['role'], 'notification_receiver' => $user['notification_receiver']]);
				} else {
					return null;
				}
			}
		}

		static function getUserAdmins() {
			$conn = Framework::getDBConn();

			$users = $conn->select('user', ['id'], ['AND' => ['role' => 'admin', 'notification_receiver' => 1]]);
			$res = [];
			for($i = 0; $user = $users[$i], $i < count($users); ++$i) {
				array_push($res, User::getUser($user['id']));
			}

			return $res;
		}

		static function getRecoveryCode($email) {
			$conn = Framework::getDBConn();

			$user = $conn->select('user', ['id'], ['email' => $email]);
			if(count($user) != 1) {
				return new Exception('User with this email not found!');
			}
			$user = $user[0];

			$recovery_code = Framework::uuid4();

			$conn->update('user', ['recovery_code' => $recovery_code], ['id' => $user['id']]);

			return $recovery_code;
		}

		static function passwordRecovery($recovery_code) {
			$conn = Framework::getDBConn();

			$user = $conn->select('user', ['id'], ['recovery_code' => $recovery_code]);

			if(count($user) != 1) {
				return new Exception('Wrong recovery code!');
			} else {
				$user = User::getUser($user[0]['id']);

				$user->new_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);
				$user->setPassword($user->new_password);

				$user->save();

				$conn->update('user', ['recovery_code' => null], ['id' => $user->id]);

				return $user;
			}
		}
		
		static function confirmEmail($recovery_code) {
			$conn = Framework::getDBConn();

			$user = $conn->select('user', ['id'], ['recovery_code' => $recovery_code]);

			if(count($user) != 1) {
				return new Exception('Wrong recovery code!');
			} else {
				$user = User::getUser($user[0]['id']);
				$conn->update('user', ['recovery_code' => null, 'role' => 'disabled'], ['id' => $user->id]);
			}

			return $user;
		}


		function save() {
			$db = Framework::getDBConn();

			if($this->id) {
				// user exists
				if(0 != $db->count('user', [ 'AND' => ['email' => $this->email, 'id[!]' => $this->id ]])) {
					throw new Exception('This email has been used by another user!');
				}


				$new_user_data = [
					'email' => $this->email
					, 'role' => $this->role
				];

				if($this->role == 'admin' && isset($this->notification_receiver)) {
					$new_user_data['notification_receiver'] = $this->notification_receiver ? 1 : 0;
				}


				if($this->passwordHash) {
					$new_user_data['password'] = $this->passwordHash;
				}

				$db->update('user', $new_user_data, [ 'id' => $this->id ]);
			} else {
				// new user
				if(0 != $db->count('user', [ 'email' => $this->email ])) {
					throw new Exception('This email has been used by another user!');
				}

				$new_user_data = [
					'email' => $this->email
					, 'role' => $this->role
					, 'password' => $this->passwordHash
				];

				if($this->role == 'admin' && isset($this->notification_receiver)) {
					$new_user_data['notification_receiver'] = $this->notification_receiver ? 1 : 0;
				}

				$db->insert('user', $new_user_data);

				$newUser = $db->select('user', ['id'], ['email' => $this->email]);
				$newUser = count($newUser) == 1 ? $newUser[0] : null;
				$this->id = $newUser['id'];
			}
		}
	}
	Framework::setUserClass('User');



	include 'PHPMailer/class.phpmailer.php';
	include 'PHPMailer/class.smtp.php';


	class Email extends BaseEmail {
		public $title_suffix = '';
		public $title = '';
		public $title_prefix = 'Ukrainian Alerts: ';
		public $body = '';

		function send($to) {
			$title = $this->title_prefix . $this->title . $this->title_suffix;

			$message = $this->body;
			$message = preg_replace("/(?!\r)\n/", "\r\n", $message);
			$message = wordwrap($message, 70, "\r\n");


			$mail = new PHPMailer();
			$mail->IsSMTP();
			
			$mail->SMTPSecure = 'tls';
			$mail->Port = 587;
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'ukraine.reach@gmail.com';
			$mail->Password = 'reach951';
			$mail->setFrom('ukraine.reach@gmail.com', 'REACH Ukraine');

			$mail->addAddress($to);

			$mail->Subject = $title;
			$mail->Body = $message;

			$mail->send();
		}
	}
