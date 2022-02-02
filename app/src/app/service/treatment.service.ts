import { HelperService } from './helper.service';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class TreatmentService {

  constructor(public helperService: HelperService) { }

	/**
	 * @desc Calling api for add billing code
	 * @param billingCodeDetails
	 */
	addBillingCode(billingCodeDetails:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/add-billing-code",
			"post",
			billingCodeDetails
		);
	}

	/**
	 * @desc Calling api for update billing code details
	 * @param billingCodeDetails
	 */
	updateBillingCode(billingCodeDetails:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/update-billing-code",
			"post",
			billingCodeDetails
		);
	}

	/**
	 * @desc Calling api for get billing code list of patient
	 * @param data
	 */
	getBillingCodeList(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/get-billing-code-list",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for get billing code details for patient
	 * @param data
	 */
	getBillingCodeDetails(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/get-billing-code-details",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for get cpt code
	 * @param data
	 */
	getCptCode(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/get-cpt-code",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for get provider list
	 * @param data
	 */
	getProvider(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/get-provider",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for add patient prescription
	 * @param data
	 */
	addPatientPrescriptions(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/add-prescription",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for add patient prescription
	 * @param data
	 */
	updatePatientPrescriptions(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/update-prescription",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for get patient prescription list
	 * @param patientId
	 */
	getPatientPrescriptions(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/get-patient-prescription",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for get patient prescription details
	 * @param prescriptionId
	 */
	getPatientPrescriptionsDetails(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/get-prescription-details",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for search prescription
	 * @param key
	 */
	searchPatientPrescription(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/search-prescription",
			"post",
			data
		);
	}


	/**
	 * @desc Calling api for add update treatment plan
	 * @param pal data
	 */
	editTreatmentPlanDetails(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/edit-treatment-plan",
			"post",
			data
		);
	}


	/**
	 * @desc Calling api for update payment data
	 * @param participant_id
	 */
	updatePatientPaymentData(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/update-payment-data",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for get assistance list
	 * @param participant_id
	 */
	getAllAssistanceList(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/get-patient-assistance-data",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for add Program/assistance
	 * @param programData
	 */
	addProgram(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/add-program",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for update assistance type
	 * @param programData
	 */
	updateAssistanceType(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/update-assistance-type",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for update patient 
	 * @param programData
	 */
	updatePatientProfile(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/update-patient-profile",
			"post",
			data
		);
	}
	/**
	 * @desc Calling api for add assistance to patient 
	 * @param programData
	 */
	addNeededPatientAssistance(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/add-needed-patient-assistance",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for update assistance type
	 * @param programData
	 */
	addprogramToPatient(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/add-program-to-patient",
			"post",
			data
		);
	}

	/**
	 * @desc Calling api for get program list
	 * @param programData
	 */
	searchProgram(data:any): Observable<any> {
		return this.helperService.makeHttpRequest(
			"treatment/search-program",
			"post",
			data
		);
	}


}
