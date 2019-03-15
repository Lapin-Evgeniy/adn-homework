<?php  

namespace models;

use PDO;
use core\classes\Model;

class User extends Model 
{
	public function loginUser($username, $password)
	{
		$errors = array();

		if(empty($username) OR empty($password)) {
			$errors[] = 'Для авторизации введите логин и пароль.';
			return $errors;
		}

		$query_stmt = $this->db->prepare("
			SELECT username, email, password, status
			FROM users 
			WHERE username = :username;");
		$query_stmt->execute(array('username' => $username));
		$result = $query_stmt->fetch(PDO::FETCH_ASSOC);

		if($result['status'] = 0) {
			$errors[] = 'Аккаунт не активирован, обратитесь к администратору admin@admin.com';
		}
		if($result === false) {
			$errors[] = 'Пользователя не существует. Пройдите <a href="/reg">регистрацию</a>';	
		}

		if($result) {
			if($result['password'] == md5($password)) {
				return true;
			} 
			else {
				$errors[] = 'Неверный логин или пароль';
				return $errors;		
			}
		}
	}

	/**
	 * [getUserType description]
	 * @param  [string] $username [description]
	 * @return [number]           [description]
	 */
	public function getUserType($username)
	{
		$query_stmt = $this->db->prepare("
			SELECT type 
			FROM users 
			WHERE username = :username;");
		$query_stmt->execute(array('username' => $username));
		$result = $query_stmt->fetch(PDO::FETCH_ASSOC);

		return $result['type'];
	}

	/**
	 * [getUsersList description]
	 * @return [type] [description]
	 */
	public function getUsersList()
	{
		$query_stmt = $this->db->query('
			SELECT u.id, ua.firstname, ua.lastname, u.email, u.username, u.email
			FROM users u 
			INNER JOIN user_attributes ua ON u.id = ua.id');
		$query_stmt->execute();
		$result = $query_stmt->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	/**
	 * [getUser description]
	 * @param  [type] $userID [description]
	 * @return [type]         [description]
	 */
	public function getUser($userID)
	{
		$query_stmt = $this->db->prepare('
			SELECT u.*, ua.*
			FROM users u 
			INNER JOIN user_attributes ua ON u.id = ua.id
			WHERE u.id = :userID');
		$query_stmt->execute(array('userID' => $userID));
		$result = $query_stmt->fetch(PDO::FETCH_ASSOC);

		return $result;
	}

	/**
	 * [addUser description]
	 * @param [array] $data [description]
	 */
	public function addUser($data)
	{
		$data['status'] = 0;
		$data['type'] = 1;

		$query_stmt = $this->db->prepare("
			INSERT INTO users (username, email, password, status, type)
			VALUES (:username, :email, :password, :status, :type)");
		$result = $query_stmt->execute($data);

		return $result;
	}

	/**
	 * [editUser description]
	 * @param  [type] $id     [description]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function editUser($id, $params)
	{
		unset($params['submit']);
		$params['id'] = $id;

		$query_stmt = $this->db->prepare("
			UPDATE users u, user_attributes ua SET
				u.username = :username, 
				u.email = :email, 
				u.status = :status,
				u.password = :password, 
				ua.firstname = :firstname,
				ua.lastname = :lastname,
				ua.patronymic = :patronymic,
				ua.phone = :phone,
				ua.adress = :adress,
				ua.type = :type			
			WHERE u.id = :id AND ua.id = :id");
		$result = $query_stmt->execute($params);

		return $result;
	}
}


?>