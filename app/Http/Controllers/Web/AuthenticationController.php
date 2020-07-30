<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{
    //
    public function getSocialRedirect($account)
    {
        try {
            return Socialite::with($account)->redirect();
        } catch (\InvalidArgumentException $e) {
            return redirect('/login');
        }
    }

    public function getSocialCallback($account)
    {
        // �ӵ����� OAuth �ص��л�ȡ�û���Ϣ
        $socialUser = Socialite::with($account)->user();

        // �ڱ��� users ���в�ѯ���û����ж��Ƿ��Ѵ���
        $user = User::where( 'provider_id', '=', $socialUser->id )
            ->where( 'provider', '=', $account )
            ->first();
        if ($user == null) {
            // ������û����������䱣�浽 users ��
            $newUser = new User();

            $newUser->name        = $socialUser->getNickname();
            $newUser->email       = $socialUser->getEmail() == '' ? '' : $socialUser->getEmail();
            $newUser->avatar      = $socialUser->getAvatar();
            $newUser->password    = '';
            $newUser->provider    = $account;
            $newUser->provider_id = $socialUser->getId();

            $newUser->save();
            $user = $newUser;
        }

        // �ֶ���¼���û�
        Auth::login( $user );

        // ��¼�ɹ����û��ض�����ҳ
        return redirect('/');
    }
}
