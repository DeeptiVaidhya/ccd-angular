//import { of } from 'rxjs/observable/of';
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { NgxSpinnerService } from "ngx-spinner";
import { of } from 'rxjs';
// import { ToastrService } from 'ngx-toastr';
import { catchError, map } from 'rxjs/operators';
import { CONSTANTS } from '../config/constants';


@Injectable()
export class HelperService {
	isLoggedIn: boolean = false;
	constructor(private http: HttpClient, private spinnerService: NgxSpinnerService) { }


	/**
	 * Get token and send it to interceptor
	 */
	getToken() {
		return localStorage.getItem('token');
	}

	/**
	 * Check if the API is required for Token
	 */
	public isAuthenticated(): boolean {
		return this.isLoggedIn;
	}

	/**
	 * @desc Common function to call GET/POST with/without parameters
	 * @param url
	 * @param type
	 * @param data
	 * @param isLoggedIn
	 */
	makeHttpRequest(url, type = 'get', data = {}, isLoggedIn = false) {
		let httpRequest: any;
		this.isLoggedIn = isLoggedIn;
		url = CONSTANTS.API_ENDPOINT + url;

		if (type == 'post') {
			httpRequest = this.http[type](url, data);
		} else {
			httpRequest = this.http[type](url);
		}
		(data['showSpinner'] == undefined || (data['showSpinner'] && data['showSpinner'] != false)) &&
			this.spinnerService.show();
		return httpRequest.pipe(
			map(res => {
				let response = res;
				(data['showSpinner'] == undefined || (data['showSpinner'] && data['showSpinner'] != false)) &&
					this.spinnerService.hide();
				return response;
			}),
			catchError(err => of([this.spinnerService.hide()]))
		);
	}
	validateAllFormFields(formGroup: FormGroup) {
		//{1}
		Object.keys(formGroup.controls).forEach(field => {
			//{2}
			const control = formGroup.get(field); //{3}
			if (control instanceof FormControl) {
				//{4}
				control.markAsTouched({ onlySelf: true });
			} else if (control instanceof FormGroup) {
				//{5}
				this.validateAllFormFields(control); //{6}
			}

		});
	}
}
