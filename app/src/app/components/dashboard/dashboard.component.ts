import { HelperService } from './../../service/helper.service';
import { PatientService } from './../../service/patient.service';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';

@Component({
	selector: 'app-dashboard',
	templateUrl: './dashboard.component.html',
	styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
	patientSearchForm: FormGroup;
	patientList:any =[];
	searchPatientFlag:boolean = false;
	constructor(
		public route: Router,
		private formBuilder: FormBuilder,
		private patientService: PatientService,
		private toastr: ToastrService,
		private helper: HelperService,
	) { }

	ngOnInit(): void {
		this.patientSearchForm = this.formBuilder.group(
			{
				serach_key:[
					'',
					Validators.compose([
						Validators.pattern(/^[a-zA-Z]+[a-zA-Z '".-]*$/),
						Validators.required,
					])
				] 
			}
		);
	}

	addNewPatient() {
		this.route.navigate(['/add-patient']);
	}

	clearSerach(){
		this.patientList = [];
		this.searchPatientFlag = false;
		this.patientSearchForm.controls.serach_key.setValue('');
	}

	searchPatient(){
		if(this.patientSearchForm.valid){
			this.patientService.searchPatient(this.patientSearchForm.value).subscribe(res =>{
				if(res.status =='success'){
					this.searchPatientFlag = true;
					this.patientList = res.data;
				}else{
					this.toastr.error(res.msg);
				}
			})
		}else{
			this.toastr.error("Please enter valid search key.");
		}
	}

	patientDetails(patientId:any){
		this.route.navigate(['patient-details/'+patientId]);
	}

}
