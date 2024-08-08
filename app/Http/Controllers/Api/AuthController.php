<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\DeleteAccountRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\LogoutRequest;
use App\Http\Requests\Api\Auth\NewPasswordRequest;
use App\Http\Requests\Api\Auth\OtpVerifyRequest;
use App\Http\Requests\Api\Auth\RecoverVerifyRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\SocailLoginRequest;
use App\Http\Requests\Api\Auth\VerifyRequest;
use App\Mail\ForgotOtp;
use App\Mail\OtpSend;
use App\Models\Category;
use App\Models\LinkedAccount;
use App\Models\OtpVerify;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserInterest;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function verify(VerifyRequest $request)
    {
        $otp = random_int(100000, 999999);
        $mail_details = [
            'body' => $otp,
        ];
        Mail::to($request->email)->send(new OtpSend($mail_details));
        $user = new OtpVerify();
        $user->email = $request->email;
        $user->otp = $otp;
        $user->save();
        return response()->json([
            'status' => true,
            'action' => 'User verify and Otp send',
        ]);
    }

    public function otpVerify(OtpVerifyRequest $request)
    {
        $user = OtpVerify::where('email', $request->email)->latest()->first();
        if ($user) {
            if ($request->otp == $user->otp) {
                $user = OtpVerify::where('email', $request->email)->delete();
                return response()->json([
                    'status' => true,
                    'action' => 'OTP verify',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'title' => 'Invalid OTP',
                    'errors' => [
                        [
                            'field' => 'otp',
                            'message' => 'The OTP you entered is invalid or expired. Please enter the correct OTP or request a new OTP and try again'
                        ]
                    ]
                ]);
            }
        }
    }

    public function register(RegisterRequest $request)
    {

        $create = new User();
        $create->first_name = $request->first_name;
        $create->last_name = $request->last_name;
        $create->email = $request->email;
        $create->password = Hash::make($request->password);
        $create->timezone = $request->timezone ?? 'No Time';
        $create->is_password_added = 1;

        $create->save();

        $userdevice = new UserDevice();
        $userdevice->user_id = $create->uuid;
        $userdevice->device_name = $request->device_name ?? 'No name';
        $userdevice->device_id = $request->device_id ?? 'No ID';
        $userdevice->timezone = $request->timezone ?? 'No Time';
        $userdevice->token = $request->fcm_token ?? 'No tocken';
        $userdevice->save();


        $newuser  = User::where('uuid', $create->uuid)->first();

        $newuser->token = $newuser->createToken('Register')->plainTextToken;

        $interest = UserInterest::where('user_id', $newuser->uuid)->first();
        if ($interest) {
            $catIds = UserInterest::where('user_id', $newuser->uuid)->pluck('category_id');
            $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
            foreach ($categories as $item) {
                $item->is_added = true;
            }
            $newuser->interest = $categories;
        } else {
            $newuser->interest = [];
        }
        $is_subscribe = UserSubscription::where('user_id', $newuser->uuid)->first();
        if ($is_subscribe) {
            $newuser->is_subscribe = true;
        } else {
            $newuser->is_subscribe = false;
        }
        return response()->json([
            'status' => true,
            'action' => 'User register successfully',
            'data' => $newuser
        ]);
    }

    public function socialLogin(SocailLoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $check = LinkedAccount::where('platform', $request->platform)->where('email', $request->email)->where('user_id', $user->uuid)->first();
            $check1 = LinkedAccount::where('platform', $request->platform)->where('platform_id', $request->platform_id)->where('user_id', $user->uuid)->first();
            if ($check || $check1) {

                $userdevice = new UserDevice();
                $userdevice->user_id = $user->uuid;
                $userdevice->device_name = $request->device_name ?? 'No name';
                $userdevice->device_id = $request->device_id ?? 'No ID';
                $userdevice->timezone = $request->timezone ?? 'No Time';
                $userdevice->token = $request->fcm_token ?? 'No tocken';
                $userdevice->save();

                $interest = UserInterest::where('user_id', $user->uuid)->first();
                if ($interest) {
                    $catIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                    $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
                    foreach ($categories as $item) {
                        $item->is_added = true;
                    }
                    $user->interest = $categories;
                } else {
                    $user->interest = [];
                }
                $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
                if ($is_subscribe) {
                    $user->is_subscribe = true;
                } else {
                    $user->is_subscribe = false;
                }



                $user->token = $user->createToken('Login')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'data' => $user,
                    'action' => "Login successfully"
                ]);
            } else {

                $linked = new LinkedAccount();
                $linked->user_id = $user->uuid;
                $linked->platform = $request->platform;
                $linked->platform_id = $request->platform_id;
                $linked->email = $request->email;
                $linked->save();

                $userdevice = new UserDevice();
                $userdevice->user_id = $user->uuid;
                $userdevice->device_name = $request->device_name ?? 'No name';
                $userdevice->device_id = $request->device_id ?? 'No ID';
                $userdevice->timezone = $request->timezone ?? 'No Time';
                $userdevice->token = $request->fcm_token ?? 'No tocken';
                $userdevice->save();

                $interest = UserInterest::where('user_id', $user->uuid)->first();
                if ($interest) {
                    $catIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                    $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
                    foreach ($categories as $item) {
                        $item->is_added = true;
                    }
                    $user->interest = $categories;
                } else {
                    $user->interest = [];
                }
                $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
                if ($is_subscribe) {
                    $user->is_subscribe = true;
                } else {
                    $user->is_subscribe = false;
                }

                $user->token = $user->createToken('Login')->plainTextToken;



                return response()->json([
                    'status' => true,
                    'data' => $user,
                    'action' => "User Login successfully"
                ]);
            }
        } else {
            $check = LinkedAccount::where('platform', $request->platform)->where('platform_id', $request->platform_id)->first();
            if ($check) {
                $user = User::find($check->user_id);

                $userdevice = new UserDevice();
                $userdevice->user_id = $user->uuid;
                $userdevice->device_name = $request->device_name ?? 'No name';
                $userdevice->device_id = $request->device_id ?? 'No ID';
                $userdevice->timezone = $request->timezone ?? 'No Time';
                $userdevice->token = $request->fcm_token ?? 'No tocken';
                $userdevice->save();

                $interest = UserInterest::where('user_id', $user->uuid)->first();
                if ($interest) {
                    $catIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                    $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
                    foreach ($categories as $item) {
                        $item->is_added = true;
                    }
                    $user->interest = $categories;
                } else {
                    $user->interest = [];
                }
                $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
                if ($is_subscribe) {
                    $user->is_subscribe = true;
                } else {
                    $user->is_subscribe = false;
                }

                $user->token = $user->createToken('Login')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'data' => $user,
                    'action' => "Login successfully"
                ]);
            }
            $create = new User();
            $create->first_name = $request->first_name;
            $create->email = $request->email;
            $create->password = '';
            $create->timezone = $request->timezone ?? 'No Time';
            $create->is_password_added = 0;
            $create->save();

            $linked = new LinkedAccount();
            $linked->user_id = $create->uuid;
            $linked->platform = $request->platform;
            $linked->platform_id = $request->platform_id;
            $linked->email = $request->email;
            $linked->save();

            $userdevice = new UserDevice();
            $userdevice->user_id = $create->uuid;
            $userdevice->device_name = $request->device_name ?? 'No name';
            $userdevice->device_id = $request->device_id ?? 'No ID';
            $userdevice->timezone = $request->timezone ?? 'No Time';
            $userdevice->token = $request->fcm_token ?? 'No tocken';
            $userdevice->save();

            $user = User::find($create->uuid);
            $interest = UserInterest::where('user_id', $user->uuid)->first();
            if ($interest) {
                $catIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
                foreach ($categories as $item) {
                    $item->is_added = true;
                }
                $user->interest = $categories;
            } else {
                $user->interest = [];
            }
            $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
            if ($is_subscribe) {
                $user->is_subscribe = true;
            } else {
                $user->is_subscribe = false;
            }

            $user->token = $user->createToken('Register')->plainTextToken;



            return response()->json([
                'status' => true,
                'data' => $user,
                'action' => "User register successfully"
            ]);
        }
    }

    public function login(LoginRequest $request)
    {
        $user = User::Where('email', $request->email)->first();
        if ($user) {
            if ($user->password != null) {

                if ($user->timezone !== $request->timezone) {
                    $user->timezone = $request->timezone ?: 'No Time';
                    $user->save();
                }

                if (Hash::check($request->password, $user->password)) {
                    $userdevice = new UserDevice();
                    $userdevice->user_id = $user->uuid;
                    $userdevice->device_name = $request->device_name ?? 'No name';
                    $userdevice->device_id = $request->device_id ?? 'No ID';
                    $userdevice->timezone = $request->timezone ?? 'No Time';
                    $userdevice->token = $request->fcm_token ?? 'No tocken';
                    $userdevice->save();

                    $user->token = $user->createToken('Login')->plainTextToken;
                    $interest = UserInterest::where('user_id', $user->uuid)->first();
                    if ($interest) {
                        $catIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                        $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
                        foreach ($categories as $item) {
                            $item->is_added = true;
                        }
                        $user->interest = $categories;
                    } else {
                        $user->interest = [];
                    }
                    $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
                    if ($is_subscribe) {
                        $user->is_subscribe = true;
                    } else {
                        $user->is_subscribe = false;
                    }

                    return response()->json([
                        'status' => true,
                        'action' => "Login successfully",
                        'data' => $user,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'title' => 'Authentication Error!',
                        'errors' => [
                            [
                                'field' => 'password',
                                'message' => "Oops! Incorrect password. Please double-check and try again."
                            ]
                        ]
                    ]);
                }
            }
            $check = LinkedAccount::where('user_id', $user->uuid)->first();

            if ($check) {
                return response()->json([
                    'status' => false,
                    'title' => "Social Media Login Required!",
                    'errors' => [
                        [
                            'field' => $check->platform,
                            'message' => $check->email
                        ]
                    ]
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'title' => 'Invalid Credentials',
            'errors' => [
                [
                    'field' => 'account',
                    'message' => 'Account not found'
                ]
            ]
        ]);
    }

    public function recover(RecoverVerifyRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $otp = random_int(100000, 999999);

            $userOtp = new OtpVerify();
            $userOtp->email = $request->email;
            $userOtp->otp = $otp;
            $userOtp->save();

            $mailDetails = [
                'body' => $otp,
                'name' => $user->first_name
            ];

            Mail::to($request->email)->send(new ForgotOtp($mailDetails));

            return response()->json([
                'status' => true,
                'action' => 'Otp send successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'title' => 'Validation Error!',
                'errors' => [
                    [
                        'field' => 'email',
                        'message' => "Oops! We couldn't find this email address in our records"
                    ]
                ]
            ]);
        }
    }

    public function newPassword(NewPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user->password != '') {
                if (Hash::check($request->password, $user->password)) {
                    return response()->json([
                        'status' => false,
                        'title' => 'Validation Error!',
                        'errors' => [
                            [
                                'field' => 'email',
                                'message' => "Your new password should be different from your old password."
                            ]
                        ]
                    ]);
                } else {
                    $user->update([
                        'password' => Hash::make($request->password)
                    ]);
                    $user->is_password_added = 1;
                    $user->save();
                    return response()->json([
                        'status' => true,
                        'action' => "New password set",
                    ]);
                }
            } else {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
                $user->is_password_added = 1;
                $user->save();
                return response()->json([
                    'status' => true,
                    'action' => "New password set",
                ]);
            }

            // $user->update([
            //     'password' => Hash::make($request->password)
            // ]);
            return response()->json([
                'status' => true,
                'action' => "New Password set"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'This Email Address is not registered'
            ]);
        }
    }


    public function logout(LogoutRequest $request)
    {
        $user  = User::find($request->user()->uuid);

        UserDevice::where('user_id', $user->uuid)->where('device_id', $request->device_id)->delete();
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'action' => 'User logged out'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = User::find($request->user()->uuid);

        if ($user) {
            // if (Hash::check($request->password, $user->password)) {
            $user->tokens()->delete();
            $user->delete();
            return response()->json([
                'status' => true,
                'action' => "Account deleted",
            ]);
            // } else {
            //     return response()->json([
            //         'status' => false,
            //         'action' => 'Please enter correct password',
            //     ]);
            // }
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = User::find($request->user()->uuid);

        if ($user) {
            if ($request->has('old_password')) {
                if (Hash::check($request->old_password, $user->password)) {
                    if (Hash::check($request->new_password, $user->password)) {

                        return response()->json([
                            'status' => false,
                            'action' => "New password is same as old password",
                        ]);
                    } else {
                        $user->update([
                            'password' => Hash::make($request->new_password)
                        ]);
                        $user->is_password_added = 1;
                        $user->save();
                        return response()->json([
                            'status' => true,
                            'action' => "Password Change",
                        ]);
                    }
                }
                return response()->json([
                    'status' => false,
                    'action' => "Old password is wrong",
                ]);
            }
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            $user->is_password_added = 1;
            $user->save();

            return response()->json([
                'status' => true,
                'action' => "Password Change",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'User not found'
            ]);
        }
    }

    public function editImage(Request $request)
    {

        $user = User::find($request->user()->uuid);
        if ($user) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/profile', $file);

                // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
                // $path = Storage::disk('s3')->url($path);

                $user->profile_picture = '/uploads/' . $path;
            }
            $user->save();
            $token = $request->bearerToken();
            $user->token = $token;

            return response()->json([
                'status' => true,
                'action' => "Image edit",
                'data' => $user
            ]);
        }

        return response()->json([
            'status' => false,
            'action' => "User not found"
        ]);
    }

    public function removeImage(Request $request)
    {
        $user = User::find($request->user()->uuid);

        if ($user) {
            $user->profile_picture = '';

            $user->save();
            $token = $request->bearerToken();
            $user->token = $token;
            return response()->json([
                'status' => true,
                'action' => "Image remove",
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }
}
