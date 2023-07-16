<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\ValidationException;
use App\Utils\Response;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;
use Throwable;

class UserService
{
    public $model;
    public $email;
    function __construct(User $model)
    {
        $this->model = $model;
        $this->email = \Config\Services::email();
    }

 

    public function find($id, $fields = ["*"])
    {
        try {
            $user = $this->model->findById($id, $fields);
            $user['is_author'] = (bool) $user['is_author'];
            $user['is_admin'] = (bool) $user['is_admin'];
            return new Response(200, null, true, $user);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false);
        }
    }

    public function cast($data) {
        $data->is_author = (bool) $data->is_author;
        return $data;
    }
    public function list()
    {
        try {
            $users = $this->model->list(["id", "email", "name", "bio", 'is_author', 'is_admin', "image_url", "created_at", "updated_at"]);
            array_map(function ($user) {
                $user->is_author = (bool) $user->is_author;
                $user->is_admin = (bool) $user->is_admin;
                return $user;
            }, $users);
            return new Response(200, 'all users data', true, $users);
        } catch (Throwable $th) {
            return new Response(200, $th->getMessage(), false, false);
        }
    }

    private function createOtp() : Array
    {
        $otp_expires = time() + 1800;
        $otp = mt_rand(100000, 999999);
        $data['otp'] = $otp;
        $data['otp_expires'] = $otp_expires;
        return $data;
    }

    
    private function getActivationLink($otp)
    {
        return base_url('users/verify?otp='.$otp);
    }

    public function sendOneTimePassword($email, $otp)
    {
        try {
            $this->email->setFrom('fipex@ruang-ekspresi.id', 'Manajemen FiPEX - Information System');
            $this->email->setTo($email);
            $message = "Berikut adalah kode verifikasi yang dapat digunakan untuk memverifikasi akun FiPEX: <br>".
            "<h2>".$otp."</h2> <br>".
            '<p>Atau klik tautan berikut untuk mengaktifkan akun Anda: '. $this->getActivationLink($otp). '</p>'.
            "<i>Kode di atas hanya berlaku untuk 30 menit. Jangan memberitahukan kode tersebut ke siapapun, termasuk pihak panitia FiPEX.</i>";
            $this->email->setSubject('FiPEX Account Verification');
            $this->email->setMessage($message);  
            if(!$this->email->send()) { 
                throw new Exception('error sending OTP code to email', 400);
            }
            return true;
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e){
            throw $e;
        }
    }
    public function create($requestBody)
    {
        try {
            $email = $requestBody['email'];
            $activation_expires = time() + 1800;
            $otp = mt_rand(100000, 999999);
            $requestBody['otp'] = $otp;
            $requestBody['otp_expires'] = $activation_expires;
            $insertedData = $this->model->store($requestBody);
            $this->sendOneTimePassword($email, $otp);
            if ($insertedData === false) {
                return new Response(400, 'error insert data', false, null, null);
            }
            $data = ['id' => $insertedData];
            return new Response(201, 'user register successfully successfully ', true, $data, null);
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }
    }




    public function verifyOtp($otp)
    {
        try {
            $r = $this->model->verifyOtp($otp);
            $this->model->setValidUser($otp);
            return new Response(200, 'Your account has been verify', true, null, null);
        } catch (Throwable $th) {
            return new Response($th->getCode(), $th->getMessage(), false, null, null);
        }  catch (Exception $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }
    }


    public function sendNewOtp($email)
    {
        try {
            $otpData = $this->createOtp();
            $otp = $otpData['otp'];
            $otpExpires = $otpData['otp_expires'];
            $this->model->setOtp($email, $otp, $otpExpires);
            $this->sendOneTimePassword($email, $otp);
            return new Response(200, 'otp sent successfully', true, null);
        } catch (\Throwable $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }
    }

    public function update(string $id, $data = [])
    {
        try {
            $this->model->findById($id);
            $userUpdated = $this->model->updateById($id, $data);
            if ($userUpdated === false) {
                throw new Exception('error on update data');
            }
            return new Response(201, 'user updated successfully', true, $userUpdated);
        } catch (ValidationException $e) {
            return new Response(400, $e->getErrors(), false, null, null);
        } catch (Exception $e) {
            return new Response(400, $e->getMessage(), false, null, null);
        }
    }

    public function delete($id)
    {
        try {
            $response = $this->model->findById($id);
            $deleted = $this->model->deleteById($id);
            return new Response(200, 'user deleted successfully', true, $response, null);
        } catch (DatabaseException $th) {
            return new Response($th->getCode(), $th->getMessage(), false, null);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false, null);
        }
    }

    public function getById($id)
    {
        
        try {
            $result = $this->model->findById($id);
            return new Response(200, 'product data', true, $result);
        } catch (Exception $e) {
            return new Response(400, $e->getMessage(), false);
        }
    }


    public function login($userData)
    {
        try {
            $user = $this->model->getByQuery(['email' => $userData['email']])[0];
            $hashPassword = $user->password;
            $plainPassword = $userData['password'];
            if (!password_verify($plainPassword, $hashPassword)) {
                return new Response(400, 'password not match', false);
            }
            helper('jwt');
            $token = getSignedJWTForUser($user);
            $response = new Response(200, 'user authenticated', true);
            $response->setResult(['token' => $token]);
            return $response;
        } catch (ValidationException $e) {
            return new Response(400, $e->getErrors(), false);
        } catch (Exception $ex) {
            return new Response($ex->getCode(), $ex->getMessage(), false);
        }
    }


    public function getProductByAuthorId(string $userId, array $fields = ["*"])
    {
        $result = $this->model->getWhere(['author_id' => $userId], $fields);
        if ($result === false) {
            return new Response(404, 'product not found', false, null, null);
        };
        return new Response(200, 'product of user', true, $result, null);
    }


    public function getBadgesOfUser($userId, $fields = ["*"])
    {
        try {
            $result = $this->model->getWhere(['user_id' => $userId], $fields);
            return new Response(200, 'badges of user', true, $result, null);
        } catch (Exception $e) {
            return new Response(404, $e->getMessage(), false, null, null);
        }
    }


    public function storeImageFromBase64($path, $base64)
    {
        try {
            $image = base64_decode($base64);
            $file = file_put_contents($path, $image);
            return $path;
        } catch (Throwable $th) {
            throw $th;
        }
    }

}

?>