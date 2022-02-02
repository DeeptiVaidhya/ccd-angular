import { HelperService } from './helper.service';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PatientService {

  constructor(public helperService: HelperService) { }

	/**
	 * @desc Calling api for add patient 
	 * @param patientDetails
	 */
	addPatient(patientDetails:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"user/add-patient",
			"post",
			patientDetails
		);
	}
	/**
	 * @desc Calling api for get patient details
	 * @param patientId
	 */
	getPatient(patientDetails:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"user/get-patient",
			"post",
			patientDetails
		);
	}

	/**
	 * @desc Calling api for update patient details
	 * @param patientDetails
	 */
	updatePatient(patientDetails:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"user/update-patient",
			"post",
			patientDetails
		);
	}

	/**
	 * @desc Calling api for search patient using search key
	 * @param keyData
	 */
	searchPatient(keyData:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"user/search-patient",
			"post",
			keyData
		);
	}

	
}
