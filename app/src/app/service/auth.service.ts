import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { HelperService } from "./helper.service";

@Injectable()
export class AuthService {
	constructor(public helperService: HelperService) { }

	/**
	 * @desc Calling api for for user login with username/email and password
	 * @param credentials
	 */
	login(credentials): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/login",
			"post",
			credentials
		);
	}

	/**
	 * @desc Function is used to check a user is logged in or not called every time
	 * 		 when a user performs any activity in application
	 */
	checkLogin(): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/check-login",
			"post",
			{},
			true
		);
	}

	/**
	 * @desc Check a user's email is unique or not, called when adding a patient/provider from research staff or from edit profile
	 * @param data
	 */
	isEmailUnique(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/check-email",
			"post",
			data
		);
	}

	/**
	 * @desc Getting users according to their type like patient/research-staff/provider
	 */
	getUser(user_type, is_active = false): Observable<any> {
		return this.helperService.makeHttpRequest(
			"user/user/?user_type=" +
			user_type +
			(is_active ? "&is_active=true" : ""),
			"get",
			{},
			true
		);
	}

	/**
	 * @desc Function is used to get user details for a paticular user.
	 * @param input
	 */
	getUserDetail(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"user/user-detail",
			"post",
			data,
			true
		);
	}

	/**
	 * @desc Get profile details for a patient/provider/researcher
	 */
	get_profile(): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/profile",
			"get",
			{},
			true
		);
	}

	/**
	 * @desc Cheching a password entered is correct or not, called from edit profile page
	 * @param data
	 */
	isCurrentPassword(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/check-password",
			"post",
			data,
			true
		);
	}

	/**
	 * @desc Cheching a password entered is previous or not, called from edit profile page
	 * @param data
	 */
	isPreviousPassword(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/check-previous-password",
			"post",
			data,
			true
		);
	}

	/**
	 * @desc Updating profile for each user type like research-staff, patient and provider
	 * @param data
	 */
	update_profile(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/profile",
			"post",
			data,
			true
		);
	}

	/**
	 * @desc Logout function for each user.
	 */
	logout(): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/logout",
			"get",
			{},
			true
		);
	}

	/**
	 * @desc Update a user like patient/provider, called from research staff
	 * @param data
	 */
	updateUser(data) {
		return this.helperService.makeHttpRequest(
			"user/user",
			"put",
			data,
			true
		);
	}

	/**
	 * @desc create user password
	 */
	change_password(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/reset-password",
			"post",
			data
		);
	}

	/**
	 * @desc check reset password code
	 */
	reset_password_code(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/reset-password-code",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for forgot Password
	 * @param data
	 */
	forgot_password(data): Observable<any> {
		return this.helperService.makeHttpRequest(
			"auth/forgot-password",
			"post",
			data
		);
	}

}
