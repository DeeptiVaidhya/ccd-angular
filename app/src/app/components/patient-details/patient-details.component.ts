import { TreatmentService } from './../../service/treatment.service';
import { HelperService } from './../../service/helper.service';
import { PatientService } from './../../service/patient.service';
import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';

@Component({
	selector: 'app-patient-details',
	templateUrl: './patient-details.component.html',
	styleUrls: ['./patient-details.component.scss']
})
export class PatientDetailsComponent implements OnInit {
	treatmentPlanForm: FormGroup;
	patientId: any;
	patientDetails:any = [];
	paymentDetails:any = [];
	prescriptionPay:any;
	editFlag:boolean = false;
	treatmentPalnDetails:any=[]
	copayCoveredPrescriptions:any;
	nonCoveredPrescriptions:any;
	constructor(
		public route: Router,
		private formBuilder: FormBuilder,
		private patientService: PatientService,
		private treatmentService: TreatmentService,
		private toastr: ToastrService,
		private helper: HelperService,
		private activeRoute: ActivatedRoute,
	) { }

	ngOnInit(): void {
		this.activeRoute.params.subscribe(params => {
			this.patientId = params['patientId'];
		});
		this.getPatientDetails();
		this.treatmentPlanForm = this.formBuilder.group(
			{
				treatment_plan_details:[
					'',
					Validators.compose([
						Validators.pattern(/^.*[^ ].*/),
						Validators.required,
					])
				],
				patient_id:[
					this.patientId,
				],
			}
		);
		
	}

	getPatientDetails(){
		this.patientService.getPatient({'patientId':this.patientId}).subscribe(res =>{
			if(res.status == 'success'){
				this.patientDetails = res.data;
				this.copayCoveredPrescriptions = res.prescriptions['copay_covered_prescriptions'];
				this.nonCoveredPrescriptions = res.prescriptions['non_covered_prescriptions'];
				this.paymentDetails = res.payment_data;
				this.treatmentPalnDetails = res.treatment_plan;
				this.prescriptionPay = parseInt(res.total_prescription_price);
			}
		});
	}

	viewBillingCode(){
		this.route.navigate(['/add-billing-code/'+this.patientId]);
	}

	viewPrescriptions(){
		this.route.navigate(['/add-prescription/'+this.patientId]);
	}

	viewAssistance(){
		this.route.navigate(['/assistance/'+this.patientId]);
	}

	editPatient(){
		this.route.navigate(['update-patient/'+this.patientId]);
	}

	viewPatientReport(){
		this.route.navigate(['patient-report/'+this.patientId]);
	}

	getTotalPay(){
		let pay = 0;
		if(this.paymentDetails){
			 pay = parseFloat(this.paymentDetails['co_pays'])+parseFloat(this.paymentDetails['individual_deductible'])+parseFloat(this.paymentDetails['co_insurance'])+(parseFloat(this.copayCoveredPrescriptions)+parseFloat(this.nonCoveredPrescriptions));
		}
		return pay>0 ? pay :0;
	}

	editTreatmentPaln(){
		this.editFlag = true;
		this.treatmentPlanForm.controls.treatment_plan_details.setValue(this.treatmentPalnDetails['treatment_plan_details']);
	}

	updateTreatmentPaln(){
		this.treatmentService.editTreatmentPlanDetails(this.treatmentPlanForm.value).subscribe(res =>{
			if(res.status == 'success'){
				this.treatmentPalnDetails = res.data;
				this.editFlag = false;
				this.treatmentPlanForm.controls.treatment_plan_details.setValue(this.treatmentPalnDetails['treatment_plan_details']);
			}
		});
	}

}
